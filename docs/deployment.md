# Deploying to Portainer

The site runs as one Docker container (nginx + php-fpm, SQLite). Staging and production use the same
image and the same `docker-compose.yml`. Only the stack environment variables are different.

Both environments run on one server behind one shared Caddy stack
([`docker-compose.proxy.yml`](../docker-compose.proxy.yml)). Caddy gets the HTTPS certificates and
sends each domain to its app over a shared Docker network named `proxy`:

```
internet ──80/443──▶ caddy ──proxy network──▶ hebron-production:8080  (production stack)
                                         └──▶ hebron-staging:8080     (staging stack)
```

The app containers do not publish any ports. Only Caddy is reachable from outside.

## How a deploy works

1. You push to `main` (deploys staging) or `production` (deploys production).
2. [`.github/workflows/deploy.yml`](../.github/workflows/deploy.yml) builds the image and pushes it to GHCR:

   | Branch       | Image tag                                | GitHub environment |
   |--------------|------------------------------------------|--------------------|
   | `production` | `ghcr.io/itshiroto/hebron-v3:stable`     | `production`       |
   | `main`       | `ghcr.io/itshiroto/hebron-v3:latest`     | `staging`          |

   Every build is also tagged with the commit SHA, for rollbacks.
3. The workflow calls the `PORTAINER_WEBHOOK_URL` secret of that GitHub environment.
4. Portainer pulls the new image and recreates the container.
5. On boot the container runs `migrate --force`, `storage:link` and `optimize` (`AUTORUN_ENABLED`).

Data lives in two named volumes per stack: `database` (SQLite file) and `uploads` (`storage/app`).
Portainer prefixes volume names with the stack name, so staging and production never share data.

## One-time setup

### 1. GHCR access in Portainer

If the `hebron-v3` package on GitHub is private, Portainer needs credentials to pull it.

1. On GitHub, create a classic personal access token with only the `read:packages` scope.
2. In Portainer, go to **Registries → Add registry → Custom registry**.
3. Set **Registry URL** to `ghcr.io`, turn on **Authentication**, and enter your GitHub username and the token.

Do this once per Portainer instance (once if staging and production share a server).

### 2. Generate an APP_KEY for each environment

Use a different key for staging and production. Run this on any machine with Docker:

```bash
docker run --rm ghcr.io/itshiroto/hebron-v3:stable php artisan key:generate --show
```

Keep the output (`base64:...`). If you change the key later, all sessions and encrypted values become invalid.

### 3. Point DNS at the server

Create `A` (and `AAAA` if you use IPv6) records for the production and staging domains that point to
the server's IP. Open ports 80 and 443 (TCP and UDP) in the firewall. Caddy needs both for Let's Encrypt.

If the domains are behind Cloudflare, set the Cloudflare SSL mode to **Full (strict)**.

### 4. Create the proxy stack

Deploy this stack **before** the app stacks, because it creates the `proxy` network they join.

**Stacks → Add stack → Repository**:

| Field                | Value                                  |
|----------------------|----------------------------------------|
| Name                 | `proxy`                                |
| Repository URL       | `https://github.com/itshiroto/hebron-v3` |
| Repository reference | `refs/heads/main`                      |
| Compose path         | `docker-compose.proxy.yml`             |

Environment variables:

| Variable            | Value                                                      |
|---------------------|------------------------------------------------------------|
| `STAGING_DOMAIN`    | the staging domain, e.g. `staging.hebronfamilychurch.com` (required) |
| `PRODUCTION_DOMAIN` | `hebronfamilychurch.com` (or leave out)                    |

GitOps updates are not needed for this stack. To change the proxy config later, click
**Pull and redeploy**. Certificates are kept in the stack's `data` volume, so a redeploy does not
request new ones.

The Caddyfile is inline in the compose file (`configs.content`). This needs Docker Compose 2.23 or
later, which recent Portainer versions include.

### 5. Create the app stacks

Create one stack per environment: **Stacks → Add stack → Repository**.

| Field                    | Production                         | Staging                            |
|--------------------------|------------------------------------|------------------------------------|
| Name                     | `hebron-production`                | `hebron-staging`                   |
| Repository URL           | `https://github.com/itshiroto/hebron-v3` | same                         |
| Repository reference     | `refs/heads/production`            | `refs/heads/main`                  |
| Compose path             | `docker-compose.yml`               | `docker-compose.yml`               |
| Authentication           | GitHub username + PAT with `repo` read access (if the repo is private) | same |

Under **GitOps updates**:

- Turn on **GitOps updates** and select **Webhook** as the mechanism.
- Turn on **Re-pull image** (without it, Portainer keeps the old `stable`/`latest` image).
- Copy the webhook URL. You need it in step 6.

Under **Environment variables**, add:

| Variable     | Production                           | Staging                               |
|--------------|--------------------------------------|---------------------------------------|
| `APP_KEY`    | production key from step 2           | staging key from step 2               |
| `APP_URL`    | `https://hebronfamilychurch.com`     | your staging URL, e.g. `https://staging.hebronfamilychurch.com` |
| `IMAGE_TAG`  | `stable` (or leave out)              | `latest`                              |
| `APP_ENV`    | `production` (or leave out)          | `staging`                             |

`APP_KEY` is required. The stack does not start without it.

`APP_ENV` also sets the name Caddy uses to find the app (`hebron-production` or `hebron-staging`).
Use exactly `production` or `staging`, or Caddy returns `502 Bad Gateway`.

Click **Deploy the stack**.

### 6. Connect GitHub to the webhooks

In the GitHub repo, go to **Settings → Environments** and create two environments, `production` and
`staging`. In each one, add a secret `PORTAINER_WEBHOOK_URL` with the webhook URL of the matching stack.

> If a repository-level `PORTAINER_WEBHOOK_URL` secret exists from the old setup, delete it after you
> add the environment secrets. Otherwise a staging environment with no secret silently falls back to it
> and redeploys production.

Optional: on the `production` environment, add **Required reviewers** so that each production
deploy waits for approval.

### 7. First deploy

Push to `main` (any commit) to trigger the first staging build and deploy. Push to `production` for
the first production deploy.

### 8. First-run data

The container runs migrations automatically, but the database is empty. Open a console on the
container (**Containers → hebron-…-app → Console → Connect**, user `www-data`, `/bin/sh`) and run:

```bash
php artisan db:seed --class=PageSeeder --force
```

This fills the page content. Do not run the full `DatabaseSeeder`, because it creates a test user.

Create an admin user. Only emails that end in `@gpdi-hebron.com` can open `/admin`:

```bash
php artisan make:filament-user
```

To copy data from the old MariaDB instead of seeding (this deletes all rows in the target first):

```bash
php artisan db:copy-from "mariadb://user:pass@host:3306/hebron_v3"
```

## Day-to-day releases

1. Push or merge your work into `main`. This deploys staging automatically.
2. Check the staging site.
3. When staging looks good, deploy the same commit to production:

   ```bash
   git push origin main:production
   ```

Watch progress in the repo's **Actions** tab. In Portainer, the stack page shows the new container.

### Redeploy without a new build

If the image built but the redeploy did not happen, you do not need a new build. Use one of these:

- In Portainer, open the stack and click **Pull and redeploy**.
- Re-run the failed workflow run:

  ```bash
  gh run rerun <run-id> --repo itshiroto/hebron-v3
  ```

  A re-run uses the workflow file **from the commit of that run**, not the current one. A run from
  before the environment setup reads only a repository-level `PORTAINER_WEBHOOK_URL` and ignores the
  environment secrets. For those old runs, use **Pull and redeploy**, or push again:

  ```bash
  git push origin main:production
  ```

## Rollback

Every build is also tagged with its commit SHA. To roll back one environment:

1. In the stack's **Environment variables**, set `IMAGE_TAG` to the SHA of the last good build
   (find it in the **Actions** tab or with `git log`).
2. Click **Update the stack** with **Re-pull image** turned on.
3. After you push a fix, set `IMAGE_TAG` back to `stable` (production) or `latest` (staging).

A rollback does not undo migrations. If a bad release ran a destructive migration, restore the
database volume from a backup.

## Backups

The data is in the stack's volumes. Back them up before risky releases, for example from the server:

```bash
docker run --rm -v hebron-production_database:/data -v "$PWD":/backup alpine tar czf /backup/db-$(date +%F).tgz -C /data .
```

Do the same for the `hebron-production_uploads` volume. Use `docker volume ls` to confirm the
exact volume names.

## Troubleshooting

| Symptom | Check |
|---------|-------|
| Workflow fails at "Redeploy Portainer stack" with `curl -fsS -X POST ""` and `curl: (3) URL rejected` | The secret `PORTAINER_WEBHOOK_URL` does not exist in that GitHub environment (step 6). The image is already in GHCR, so see [Redeploy without a new build](#redeploy-without-a-new-build). |
| Workflow fails at "Redeploy Portainer stack" with an HTTP error | The webhook URL is wrong, or GitOps updates are off on the stack. |
| Deploy succeeds but the site does not change | **Re-pull image** is off on the stack. |
| Stack fails with `set APP_KEY in the Portainer stack env` | Add `APP_KEY` to the stack environment variables. |
| Portainer cannot pull the image | GHCR registry credentials are missing or the token expired (step 1). |
| App stack fails with `network proxy declared as external, but could not be found` | Deploy the proxy stack first (step 4). |
| `502 Bad Gateway` from Caddy | The app stack is not running, or its `APP_ENV` is not exactly `production` or `staging`. Check the Caddy container logs. |
| Browser shows a certificate error | DNS does not point to the server yet, or ports 80/443 are closed. Check the Caddy container logs for the Let's Encrypt error. |
| Mixed-content warnings or `http://` links | `APP_URL` is not `https://`. |
| 500 errors | Open the container **Logs** in Portainer. The app logs to stderr. |
