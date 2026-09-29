# Hebron Website Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Restyle the public Hebron Family Church site (7 pages) into the approved formal, elegant and modern design. The design uses Indonesian labels, a teal/cream palette defined as CSS variables, a serif for headings and sans for body, and a shared header and footer. Small admin-panel additions support it.

**Architecture:** The Laravel, Inertia, Vue 3 and Filament stack stays as it is. The backend changes are small:

- a divisions migration
- extra controller props
- a few optional Filament fields
- one pure helper class for service times

The frontend gets:

- design tokens in `app.css` and Tailwind
- a small set of presentational components in `resources/js/Components/site/`
- a rewrite of each page on top of those components

Every behaviour that can be tested lives in PHP and is covered with Pest. Vue work is verified with `npm run build` (vue-tsc plus the SSR build) and a manual check against the canvas.

**Tech Stack:** Laravel 12, Inertia 2 (`inertiajs/inertia-laravel`, `@inertiajs/vue3`), Vue 3 `<script setup lang="ts">`, Tailwind CSS 3, Filament 3, Pest 3, date-fns 4, `@iconify/vue`.

**Spec:** `docs/superpowers/specs/2026-09-29-website-redesign-design.md`
**Design canvas (source of truth for visuals):** https://claude.ai/artifact/1xWRNWzBBX3nkRozWYiTew. Open the page named in each task and match spacing, type sizes and colours.

## Global Constraints

- All UI labels are Indonesian, using the exact strings from spec §8 "UI label reference". The motto stays English: "United as One Family to Live like Christ".
- Times display in 24-hour format followed by "WIB" (e.g. `07.30 WIB`). CMS values that already end in "WIB" must not produce "WIB WIB".
- Colours come only from CSS variables in `resources/css/app.css`, through Tailwind names. Components contain **no hex values**.
- Tailwind colour names:
  - `brand-deep`, `brand-deeper`, `brand-teal`, `brand-mint`, `brand-orange`, `brand-coral`, `brand-cream`
  - `brand-primary`, `brand-accent`
  - `heading`, `decor`, `ink`, `ink-muted`, `mist`, `line`, `line-strong`

  The spec's `primary`/`accent` roles live under `brand-` because the shadcn `primary`/`accent` names are already taken in `tailwind.config.js`.
- Contrast rules:
  - Coral never carries text.
  - Orange and mint text only on `brand-deep` or `brand-deeper`.
  - On `brand-teal` bands, text is white or cream.
- Fonts:
  - Headings use `font-serif`, which is `var(--font-serif)` = Instrument Serif.
  - Body uses `font-sans`, which is `var(--font-sans)` = Montserrat.
- Buttons and interactive targets are at least 44px tall, with corners of `rounded-button` (`var(--radius-button)` = 2px).
- The logo is the colour logo: `generalSettings.church_logo` if set, else `/hebron.svg`. The white logo is never used on the public site.
- The footer credit reads exactly: `GPdI Hebron Production Team w/ 34 Creative Labs`.
- **SSR-safe:** `npm run build` also runs `vite build --ssr`. Never touch `window`, `document` or `matchMedia` at module or setup top level; only inside `onMounted`, watchers that run after mount, or event handlers. Do not use Pinia in new code, because `ssr.ts` does not install it.
- Every new admin field is optional. Pages must render when any section or row is missing.
- Keep the existing routes, Inertia component names (`Welcome`, `AboutUs`, `Division`, `Ministry`, `DevotionList`, `Devotion`, `Contact`) and Filament page names.

## Review Focus

These are the inputs most likely to break for real users, even though the spec never names them. Each line has a pinning test in its owning task.

1. **Fresh or empty database** (no `pages`/`sections` rows): every public route must return 200. `ContactController` currently crashes with a null `sections()` call. *Pinned in Task 5, `tests/Feature/PublicPagesTest.php`.*
2. **Devotion list with a bad query:**
   - `?category=foo`, `?category[]=x` and `?page=999` must return 200.
   - An unknown category is ignored and shows everything; a page past the end shows an empty list.

   *Pinned in Task 4.*
3. **Division titles with punctuation** (`DoT's`) must produce a stable URL slug (`dots`) for deep links. A null `time` or `short_time` must pass through as `null`, not crash. *Pinned in Task 2.*
4. **Service times from the CMS already ending in "WIB"** (the live site stores `07.30 WIB`) must be normalised, so the UI never shows "07.30 WIB WIB". A missing `livestream` flag counts as false. *Pinned in Task 3 (`ServiceTimes` unit test).*
5. **Hero video that cannot autoplay, or `prefers-reduced-motion: reduce`:**
   - The poster image stays visible.
   - The toggle button's label matches the real state ("Putar video latar" when paused).

   *Pinned by the manual check in Task 6, Step 6.*

---

## File Map

**Backend**
- Create `app/Support/ServiceTimes.php`: normalises the schedule services array. It is pure, with no DB access.
- Modify `app/Models/Division.php`: new fillable fields, the `slug` accessor, and `ordered()` scope.
- Modify `app/Models/Devotion.php`: a `CATEGORIES` constant, plus `snippet` and `reading_minutes` accessors.
- Create `database/migrations/2026_09_29_000001_add_audience_short_time_sort_to_divisions_table.php`
- Create `database/factories/DivisionFactory.php`, `database/factories/DevotionFactory.php`
- Modify the controllers `HomeController`, `DivisionController`, `DevotionController`, `AboutController` and `ContactController`.
- Modify `app/Http/Middleware/HandleInertiaRequests.php`: share `services`.
- Modify the Filament files `DivisionResource.php`, `Pages/HomePage.php`, `Pages/AboutPage.php` and `Pages/GeneralSettings.php`.
- Modify `tests/TestCase.php` (`withoutVite`).
- Tests:
  - `tests/Unit/ServiceTimesTest.php`
  - `tests/Feature/DivisionPageTest.php`
  - `tests/Feature/HomePageTest.php`
  - `tests/Feature/DevotionPageTest.php`
  - `tests/Feature/PublicPagesTest.php`
  - `tests/Feature/AboutPageTest.php`

**Frontend**
- Modify `resources/css/app.css` (tokens, fonts, helpers), `tailwind.config.js` and `resources/views/app.blade.php`.
- Create `resources/js/types/site.d.ts`, `resources/js/lib/site.ts` and `resources/js/composables/useSiteSettings.ts`.
- Create in `resources/js/Components/site/`:
  - `BaseButton.vue`
  - `SectionHeading.vue`
  - `PageHero.vue`
  - `SiteHeader.vue`
  - `MobileMenu.vue`
  - `SiteFooter.vue`
  - `DivisionCard.vue`
  - `DivisionTabs.vue`
  - `PostCard.vue`
- Rewrite `resources/js/Layouts/DefaultLayout.vue` and the pages `Welcome.vue`, `AboutUs.vue`, `Division.vue`, `Ministry.vue`, `DevotionList.vue`, `Devotion.vue` and `Contact.vue`.
- Delete `resources/js/Components/Navbar.vue`, `Footer.vue`, `DivisionCard.vue` (the old one in `Components/`), `ServiceCard.vue` and `resources/js/Stores/navigationStore.js`.
- Remove `vue3-marquee` from `package.json`.

---

### Task 0: Workspace setup and green baseline

**Files:**
- Modify: `tests/TestCase.php`

**Interfaces:**
- Produces: a working `php artisan test` (Feature tests can render Inertia pages without a Vite manifest).

- [ ] **Step 1: Install dependencies and environment**

```bash
composer install
npm ci
cp -n .env.example .env
php artisan key:generate
```

- [ ] **Step 2: Run the existing suite to see the baseline**

Run: `php artisan test`
Expected: the `ExampleTest` "returns a successful response" test **FAILS** with a 500 "Vite manifest not found" error, because `app.blade.php` calls `@vite` and there is no build.

- [ ] **Step 3: Disable Vite in tests**

Replace the contents of `tests/TestCase.php` with:

```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }
}
```

- [ ] **Step 4: Run the suite**

Run: `php artisan test`
Expected: all tests PASS (at least `ExampleTest` and the Breeze auth/profile tests).

- [ ] **Step 5: Confirm the frontend builds before changes**

Run: `npm run build`
Expected: it exits 0 (vue-tsc, the client build and the SSR build). If it fails before any change, stop and report. Do not fix unrelated errors silently.

- [ ] **Step 6: Commit**

```bash
git add tests/TestCase.php
git commit -m "test: render Inertia pages without a Vite manifest"
```

---

### Task 1: Design tokens, fonts and Tailwind mapping

**Files:**
- Modify: `resources/css/app.css` (lines 1–2 font imports; add tokens inside the existing `@layer base { :root { … } }`; add a `@layer components` block)
- Modify: `tailwind.config.js` (the `fontFamily` block, `borderRadius`, `colors`)
- Modify: `resources/views/app.blade.php`

**Interfaces:**
- Produces:
  - Tailwind classes used by every later task: `bg-brand-deep`, `text-heading`, `border-line`, `bg-mist`, `text-ink-muted`, `bg-decor`, `rounded-button`, `font-serif`, `font-sans`.
  - The component classes `.container-site`, `.section-y`, `.rich-text` and `.mission-list`.

- [ ] **Step 1: Replace the font imports (lines 1–2 of `app.css`)**

```css
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Montserrat:ital,wght@0,400..700;1,400..700&display=swap');
```

(The Playfair Display import is removed.)

- [ ] **Step 2: Add the brand tokens at the top of the existing `:root { … }` block inside `@layer base`**

```css
    /* Brand — change here only (palette v2: teal / cream / coral / orange / mint) */
    --brand-deep: 23 63 85;        /* #173F55 headings, dark bands */
    --brand-deeper: 16 48 63;      /* #10303F footer */
    --brand-teal: 39 97 127;       /* #27617F primary */
    --brand-mint: 131 204 197;     /* #83CCC5 highlights on dark only */
    --brand-orange: 249 150 11;    /* #F9960B accent on deep/deeper only */
    --brand-coral: 250 90 35;      /* #FA5A23 decorative only, never text */
    --brand-cream: 248 246 234;    /* #F8F6EA warm off-white */

    --ink: 22 48 61;
    --ink-muted: 82 99 110;
    --mist: var(--brand-cream);
    --line: 233 229 214;
    --line-strong: 217 211 191;

    --color-accent: var(--brand-orange);
    --color-decor: var(--brand-coral);
    --color-primary: var(--brand-teal);
    --color-heading: var(--brand-deep);

    --font-serif: 'Instrument Serif', 'Times New Roman', serif;
    --font-sans: 'Montserrat', system-ui, sans-serif;
    --radius-button: 2px;
```

- [ ] **Step 3: Append the component helpers at the end of `app.css`**

```css
@layer components {
  .container-site {
    @apply mx-auto w-full max-w-[1440px] px-5 lg:px-[120px];
  }
  .section-y {
    @apply py-16 lg:py-32;
  }
  /* CMS rich text (values, ministries, pastor greeting, devotion body) */
  .rich-text p { @apply mb-4 last:mb-0; }
  .rich-text ul { @apply list-disc pl-5; }
  .rich-text ol { @apply list-decimal pl-5; }
  .rich-text li { @apply mb-1; }
  .rich-text a { @apply text-brand-teal underline underline-offset-2; }
  .rich-text h2 { @apply mb-4 mt-10 font-serif text-3xl leading-tight text-heading lg:text-[40px]; }
  .rich-text h3 { @apply mb-3 mt-8 font-serif text-2xl text-heading; }
  .rich-text blockquote { @apply my-8 border-l-2 border-brand-teal pl-5 font-serif text-[26px] italic leading-snug text-heading lg:pl-8 lg:text-[34px]; }
  .rich-text blockquote cite, .rich-text cite { @apply mt-3 block font-sans text-xs font-semibold not-italic uppercase tracking-[0.16em] text-brand-teal; }
  /* Mission list: roman numerals in the serif */
  .mission-list ol, .mission-list ul { @apply flex flex-col gap-4 pl-8; list-style: lower-roman; }
  .mission-list li::marker { font-family: var(--font-serif); color: rgb(var(--brand-teal)); }
}
```

- [ ] **Step 4: Map the tokens in `tailwind.config.js`**

At the top of the file, under the `import forms …` line, add:

```js
const token = (name) => `rgb(var(${name}) / <alpha-value>)`;
```

Replace the existing `fontFamily: { … }` block with:

```js
    		fontFamily: {
    			sans: ['var(--font-sans)'],
    			serif: ['var(--font-serif)']
    		},
```

Inside `borderRadius`, add `button: 'var(--radius-button)',` as the first entry.

Inside `colors`, add these entries after `chart: { … }`, keeping every existing entry:

```js
    			brand: {
    				deep: token('--brand-deep'),
    				deeper: token('--brand-deeper'),
    				teal: token('--brand-teal'),
    				mint: token('--brand-mint'),
    				orange: token('--brand-orange'),
    				coral: token('--brand-coral'),
    				cream: token('--brand-cream'),
    				primary: token('--color-primary'),
    				accent: token('--color-accent')
    			},
    			heading: token('--color-heading'),
    			decor: token('--color-decor'),
    			ink: {
    				DEFAULT: token('--ink'),
    				muted: token('--ink-muted')
    			},
    			mist: token('--mist'),
    			line: {
    				DEFAULT: token('--line'),
    				strong: token('--line-strong')
    			}
```

- [ ] **Step 5: Update `resources/views/app.blade.php`**

- Change `<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">` to `<html lang="id">`.
- Delete the two lines under `<!-- Fonts -->` (the bunny.net preconnect and the figtree stylesheet).
- Change `<body class="font-sans antialiased">` to `<body class="bg-white font-sans text-ink antialiased">`.

- [ ] **Step 6: Verify the build**

Run: `npm run build`
Expected: exits 0.

Then run: `grep -c "rgb(var(--brand-deep)" public/build/assets/*.css`
Expected: at least 1. If it is 0, no template uses the class yet, which is also fine; in that case check that `grep -c -- "--brand-deep" public/build/assets/*.css` is at least 1.

- [ ] **Step 7: Commit**

```bash
git add resources/css/app.css tailwind.config.js resources/views/app.blade.php
git commit -m "feat(ui): add brand tokens as CSS variables, Instrument Serif + Montserrat"
```

---

### Task 2: Divisions data (audience, short time, sort, slug)

**Files:**
- Create: `database/migrations/2026_09_29_000001_add_audience_short_time_sort_to_divisions_table.php`
- Create: `database/factories/DivisionFactory.php`
- Modify: `app/Models/Division.php`
- Modify: `app/Http/Controllers/DivisionController.php`
- Modify: `app/Filament/Resources/DivisionResource.php`
- Test: `tests/Feature/DivisionPageTest.php`

**Interfaces:**
- Produces:
  - `Division::ordered()`, a query scope ordered by `sort`, then `id`.
  - Serialised division fields: `id`, `title`, `description`, `image`, `time|null`, `short_time|null`, `audience|null`, `link|null`, `sort`, `slug`.
  - `Division::factory()`

- [ ] **Step 1: Write the failing test** in `tests/Feature/DivisionPageTest.php`

```php
<?php

use App\Models\Division;
use Inertia\Testing\AssertableInertia as Assert;

it('lists divisions in sort order with a url slug', function () {
    Division::factory()->create(['title' => 'Speaker of Truth', 'sort' => 2, 'short_time' => 'Sab 16.30', 'audience' => 'Mahasiswa']);
    Division::factory()->create(['title' => "DoT's", 'sort' => 1, 'time' => null, 'short_time' => null, 'audience' => null]);

    $this->get('/division')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Division')
            ->has('data', 2)
            ->where('data.0.title', "DoT's")
            ->where('data.0.slug', 'dots')
            ->where('data.0.time', null)
            ->where('data.0.short_time', null)
            ->where('data.1.slug', 'speaker-of-truth')
            ->where('data.1.short_time', 'Sab 16.30')
            ->where('data.1.audience', 'Mahasiswa'));
});

it('renders the divisions page with no divisions', function () {
    $this->get('/division')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('Division')->has('data', 0));
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --filter=DivisionPageTest`
Expected: FAIL with "Call to undefined method App\Models\Division::factory()" (no factory yet).

- [ ] **Step 3: Add the migration**

`database/migrations/2026_09_29_000001_add_audience_short_time_sort_to_divisions_table.php`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->string('audience')->nullable()->after('title');
            $table->string('short_time')->nullable()->after('time');
            $table->unsignedInteger('sort')->default(0)->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn(['audience', 'short_time', 'sort']);
        });
    }
};
```

- [ ] **Step 4: Add the factory**

`database/factories/DivisionFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Division>
 */
class DivisionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->unique()->words(3, true),
            'audience' => 'Remaja',
            'description' => $this->faker->sentence(),
            'image' => 'divisions-image/sample.jpg',
            'time' => 'Setiap Sabtu · 13.30 WIB',
            'short_time' => 'Sab 13.30',
            'link' => null,
            'sort' => 0,
        ];
    }
}
```

- [ ] **Step 5: Update the model** so that `app/Models/Division.php` reads:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'audience', 'description', 'image', 'time', 'short_time', 'link', 'sort'];

    protected $appends = ['slug'];

    protected $casts = ['sort' => 'integer'];

    public function getSlugAttribute(): string
    {
        return Str::slug($this->title);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort')->orderBy('id');
    }
}
```

- [ ] **Step 6: Order the list in `DivisionController`**

In `app/Http/Controllers/DivisionController.php`, replace `$divisionsData = Division::all();` with:

```php
        $divisionsData = Division::ordered()->get();
```

- [ ] **Step 7: Run the test to verify it passes**

Run: `php artisan test --filter=DivisionPageTest`
Expected: PASS (2 tests).

- [ ] **Step 8: Update the Filament form and table** in `app/Filament/Resources/DivisionResource.php`

Replace the `Section::make()->columns(1)->schema([ … ])` contents with:

```php
                Section::make()->columns(1)->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('audience')
                        ->label('Audience')
                        ->helperText('Contoh: Anak, Remaja, Mahasiswa')
                        ->maxLength(255),
                    Textarea::make('description')->required()->maxLength(255)->rows(5),
                    TextInput::make('time')
                        ->label('Time (full)')
                        ->helperText('Contoh: Setiap Sabtu · 13.30 WIB. Kosongkan jika jadwal belum ditentukan.')
                        ->maxLength(255),
                    TextInput::make('short_time')
                        ->label('Time (short, for lists)')
                        ->helperText('Contoh: Sab 13.30')
                        ->maxLength(32),
                    TextInput::make('link')->maxLength(255),
                    FileUpload::make('image')
                        ->label('Image')
                        ->image()
                        ->imageEditor()
                        ->directory('divisions-image')
                        ->required(),
                ])
```

In `table()`, change the chain to add ordering and drag-to-reorder:

```php
        return $table
            ->reorderable('sort')
            ->defaultSort('sort')
            ->columns([
                TextColumn::make('title')->searchable(),
                TextColumn::make('audience'),
                ImageColumn::make('image'),
                TextColumn::make('time'),
            ])
```

(Keep the existing `->filters`, `->actions` and `->bulkActions` calls after `->columns`.)

- [ ] **Step 9: Run the migration locally and smoke-test the admin**

Run: `php artisan migrate`
Expected: it runs `2026_09_29_000001_add_audience_short_time_sort_to_divisions_table`.

Manual: open `/admin`, go to Divisions → Division List, edit a division, fill in Audience and Time (short), and save. Then drag a row in the table and reload; the order persists.

- [ ] **Step 10: Run the full suite and commit**

Run: `php artisan test`
Expected: PASS.

```bash
git add database/migrations/2026_09_29_000001_add_audience_short_time_sort_to_divisions_table.php database/factories/DivisionFactory.php app/Models/Division.php app/Http/Controllers/DivisionController.php app/Filament/Resources/DivisionResource.php tests/Feature/DivisionPageTest.php
git commit -m "feat(divisions): add audience, short time, sort order and url slug"
```

---

### Task 3: Service times, homepage data and admin fields

**Files:**
- Create: `app/Support/ServiceTimes.php`
- Create: `database/factories/DevotionFactory.php`
- Modify: `app/Models/Devotion.php`
- Modify: `app/Http/Middleware/HandleInertiaRequests.php`
- Modify: `app/Http/Controllers/HomeController.php`
- Modify: `app/Filament/Pages/HomePage.php`
- Test: `tests/Unit/ServiceTimesTest.php`, `tests/Feature/HomePageTest.php`

**Interfaces:**
- Consumes: `Division::ordered()` (Task 2).
- Produces:
  - `App\Support\ServiceTimes::normalize(mixed $services): array<int, array{title: string, time: string, livestream: bool}>`. `time` never ends in "WIB".
  - A shared Inertia prop `services` (the same shape) on every page.
  - Home props `divisions` (array of divisions) and `latestDevotions` (at most 3, newest first, each with `snippet` and `reading_minutes`, without `content`).
  - `Devotion::CATEGORIES = ['devotion', 'news', 'announcement']`, the accessors `snippet: string` and `reading_minutes: int`, and `Devotion::factory()`.

- [ ] **Step 1: Write the failing unit test** in `tests/Unit/ServiceTimesTest.php`

```php
<?php

use App\Support\ServiceTimes;

it('strips a trailing WIB and defaults livestream to false', function () {
    expect(ServiceTimes::normalize([
        ['title' => 'Ibadah Raya 1', 'time' => '07.30 WIB'],
        ['title' => 'Ibadah Raya 2', 'time' => '10.00', 'livestream' => true],
        ['title' => 'Ibadah Raya 3', 'time' => ' 17.30 wib '],
    ]))->toBe([
        ['title' => 'Ibadah Raya 1', 'time' => '07.30', 'livestream' => false],
        ['title' => 'Ibadah Raya 2', 'time' => '10.00', 'livestream' => true],
        ['title' => 'Ibadah Raya 3', 'time' => '17.30', 'livestream' => false],
    ]);
});

it('returns an empty list for missing or malformed input', function () {
    expect(ServiceTimes::normalize(null))->toBe([])
        ->and(ServiceTimes::normalize('nope'))->toBe([])
        ->and(ServiceTimes::normalize([['title' => 'No time']]))->toBe([]);
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --filter=ServiceTimesTest`
Expected: FAIL with `Class "App\Support\ServiceTimes" not found`.

- [ ] **Step 3: Implement `app/Support/ServiceTimes.php`**

```php
<?php

namespace App\Support;

class ServiceTimes
{
    /**
     * @return array<int, array{title: string, time: string, livestream: bool}>
     */
    public static function normalize(mixed $services): array
    {
        if (! is_array($services)) {
            return [];
        }

        $out = [];
        foreach ($services as $service) {
            $time = is_array($service) ? trim((string) ($service['time'] ?? '')) : '';
            if ($time === '') {
                continue;
            }
            $out[] = [
                'title' => trim((string) ($service['title'] ?? '')),
                'time' => trim(preg_replace('/\s*WIB\s*$/i', '', $time)),
                'livestream' => (bool) ($service['livestream'] ?? false),
            ];
        }

        return $out;
    }
}
```

- [ ] **Step 4: Run the unit test to verify it passes**

Run: `php artisan test --filter=ServiceTimesTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Write the failing feature test** in `tests/Feature/HomePageTest.php`

```php
<?php

use App\Models\Devotion;
use App\Models\Division;
use App\Models\Pages;
use Inertia\Testing\AssertableInertia as Assert;

it('passes ordered divisions, the 3 newest devotions and normalised services', function () {
    Division::factory()->create(['title' => 'B', 'sort' => 2]);
    Division::factory()->create(['title' => 'A', 'sort' => 1]);
    foreach (range(1, 4) as $i) {
        Devotion::factory()->create(['title' => "Post $i", 'created_at' => now()->subDays(10 - $i)]);
    }
    $home = Pages::create(['name' => 'Home']);
    $home->sections()->create(['name' => 'schedule', 'content' => [
        'services' => [['title' => 'Ibadah Raya 2', 'time' => '10.00 WIB', 'livestream' => true]],
    ]]);

    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('divisions.0.title', 'A')
            ->where('divisions.1.title', 'B')
            ->has('latestDevotions', 3)
            ->where('latestDevotions.0.title', 'Post 4')
            ->has('latestDevotions.0.snippet')
            ->has('latestDevotions.0.reading_minutes')
            ->missing('latestDevotions.0.content')
            ->where('services.0.time', '10.00')
            ->where('services.0.livestream', true));
});

it('renders the homepage on an empty database', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('divisions', 0)
            ->has('latestDevotions', 0)
            ->has('services', 0));
});
```

- [ ] **Step 6: Run the test to verify it fails**

Run: `php artisan test --filter=HomePageTest`
Expected: FAIL with "Call to undefined method App\Models\Devotion::factory()".

- [ ] **Step 7: Add the devotion factory**

`database/factories/DevotionFactory.php`:

```php
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\Devotion>
 */
class DevotionFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'category' => 'devotion',
            'image' => null,
            'content' => '<p>'.implode('</p><p>', $this->faker->paragraphs(3)).'</p>',
        ];
    }
}
```

- [ ] **Step 8: Add the category constant and accessors** so that `app/Models/Devotion.php` reads:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Devotion extends Model
{
    use HasFactory;

    public const CATEGORIES = ['devotion', 'news', 'announcement'];

    protected $fillable = ['title', 'slug', 'category', 'image', 'content', 'published_at'];

    public function getSnippetAttribute(): string
    {
        return Str::words(trim(strip_tags((string) $this->content)), 50);
    }

    public function getReadingMinutesAttribute(): int
    {
        $words = preg_split('/\s+/', trim(strip_tags((string) $this->content)), -1, PREG_SPLIT_NO_EMPTY);

        return max(1, (int) ceil(count($words) / 200));
    }
}
```

- [ ] **Step 9: Share `services` in `HandleInertiaRequests::share()`**

Add `use App\Support\ServiceTimes;` to the imports. In the returned array, after `'generalSettings' => $generalSettings,`, add:

```php
            'services' => fn () => ServiceTimes::normalize(
                Pages::where('name', 'Home')->first()
                    ?->sections()->where('name', 'schedule')->first()
                    ?->content['services'] ?? null
            ),
```

- [ ] **Step 10: Pass divisions and the latest devotions from `HomeController`**

Add `use App\Models\Devotion;` and `use App\Models\Division;` to the imports. Replace the `return Inertia::render('Welcome', [ … ]);` call with:

```php
        return Inertia::render('Welcome', [
            'hero' => $heroData,
            'visionMission' => $visionMissionData,
            'carousel' => $carouselData,
            'schedule' => $scheduleData,
            'pastorGreeting' => $pastorGreetingData,
            'divisions' => Division::ordered()->get(),
            'latestDevotions' => Devotion::latest()->take(3)->get()
                ->map(fn (Devotion $d) => $d->append(['snippet', 'reading_minutes'])->makeHidden('content'))
                ->values(),
        ]);
```

- [ ] **Step 11: Run the feature test to verify it passes**

Run: `php artisan test --filter=HomePageTest`
Expected: PASS (2 tests).

- [ ] **Step 12: Add the hero video and livestream toggle to the Filament `HomePage`**

In `app/Filament/Pages/HomePage.php`:

1. Add `use Filament\Forms\Components\Toggle;` to the imports.
2. In the "Hero Section" schema, directly after the `FileUpload::make('heroData.image')…->required(),` field, add:

```php
                    FileUpload::make('heroData.video')
                        ->label('Background Video (optional, muted loop)')
                        ->acceptedFileTypes(['video/mp4', 'video/webm'])
                        ->maxSize(51200)
                        ->directory('home-page-hero')
                        ->helperText('Gambar latar dipakai sebagai poster dan cadangan.'),
```

3. In the `Repeater::make('scheduleData.services')` schema, after the `time` input, add:

```php
                            Toggle::make('livestream')
                                ->label('Live Streaming')
                                ->default(false),
```

   Then change that repeater's `->columns(2)` to `->columns(3)`.

4. In `submit()`, right after the `heroData['image']` normalisation block, add:

```php
        if (isset($data['heroData']['video']) && is_array($data['heroData']['video'])) {
            $data['heroData']['video'] = $data['heroData']['video'][0] ?? null;
        }
```

- [ ] **Step 13: Smoke-test the admin**

Manual: in `/admin` → Pages → Home, turn on Live Streaming for the 10.00 service, save, and reload. The toggle stays on. Upload a short mp4, save, and reload. The file is listed.

- [ ] **Step 14: Run the full suite and commit**

Run: `php artisan test`
Expected: PASS.

```bash
git add app/Support/ServiceTimes.php database/factories/DevotionFactory.php app/Models/Devotion.php app/Http/Middleware/HandleInertiaRequests.php app/Http/Controllers/HomeController.php app/Filament/Pages/HomePage.php tests/Unit/ServiceTimesTest.php tests/Feature/HomePageTest.php
git commit -m "feat(home): share normalised service times, pass divisions and latest devotions, hero video + livestream fields"
```

---

### Task 4: Devotions: category filter, pagination, related posts

**Files:**
- Modify: `app/Http/Controllers/DevotionController.php`
- Test: `tests/Feature/DevotionPageTest.php`

**Interfaces:**
- Consumes: `Devotion::CATEGORIES`, the `snippet` and `reading_minutes` accessors, and `Devotion::factory()` (Task 3).
- Produces:
  - `DevotionList` props:
    - `posts`, a Laravel paginator JSON: `data[]` (with `snippet` and `reading_minutes`, no `content`), plus `current_page`, `last_page`, `next_page_url`, `prev_page_url` and `links[]`.
    - `category: 'devotion'|'news'|'announcement'|null`
  - `Devotion` props: `data` (with `content` and `reading_minutes`) and `related` (at most 3, excluding the current post, with `reading_minutes`, no `content`).

- [ ] **Step 1: Write the failing test** in `tests/Feature/DevotionPageTest.php`

```php
<?php

use App\Models\Devotion;
use Inertia\Testing\AssertableInertia as Assert;

it('paginates devotions 9 per page, newest first, without content', function () {
    foreach (range(1, 10) as $i) {
        Devotion::factory()->create(['title' => "Post $i", 'created_at' => now()->subDays(20 - $i)]);
    }

    $this->get('/devotion')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('DevotionList')
            ->where('category', null)
            ->has('posts.data', 9)
            ->where('posts.data.0.title', 'Post 10')
            ->has('posts.data.0.snippet')
            ->missing('posts.data.0.content')
            ->where('posts.last_page', 2));
});

it('filters by a known category and keeps the filter in pagination links', function () {
    Devotion::factory()->count(2)->create(['category' => 'news']);
    Devotion::factory()->count(3)->create(['category' => 'devotion']);

    $this->get('/devotion?category=news')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('category', 'news')
            ->has('posts.data', 2)
            ->where('posts.data.0.category', 'news'));
});

it('ignores unknown or malformed categories and out-of-range pages', function () {
    Devotion::factory()->count(2)->create();

    $this->get('/devotion?category=foo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('category', null)->has('posts.data', 2));
    $this->get('/devotion?category[]=news')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('category', null)->has('posts.data', 2));
    $this->get('/devotion?page=999')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('posts.data', 0));
});

it('shows a post with up to 3 related posts excluding itself', function () {
    $post = Devotion::factory()->create(['slug' => 'kasih-karunia', 'created_at' => now()]);
    Devotion::factory()->count(4)->create(['created_at' => now()->subDay()]);

    $this->get('/devotion/kasih-karunia')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Devotion')
            ->where('data.slug', 'kasih-karunia')
            ->has('data.content')
            ->has('data.reading_minutes')
            ->has('related', 3)
            ->missing('related.0.content')
            ->where('related', fn ($related) => collect($related)->pluck('id')->doesntContain($post->id)));
});

it('returns 404 for an unknown slug', function () {
    $this->get('/devotion/tidak-ada')->assertNotFound();
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --filter=DevotionPageTest`
Expected: FAIL. The first test fails because the `posts` prop is missing (the controller still sends `data`).

- [ ] **Step 3: Rewrite `app/Http/Controllers/DevotionController.php`**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Devotion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DevotionController extends Controller
{
    public function all(Request $request)
    {
        $category = $request->query('category');
        if (! is_string($category) || ! in_array($category, Devotion::CATEGORIES, true)) {
            $category = null;
        }

        $posts = Devotion::query()
            ->when($category, fn ($query) => $query->where('category', $category))
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Devotion $d) => $d->append(['snippet', 'reading_minutes'])->makeHidden('content'));

        return Inertia::render('DevotionList', [
            'posts' => $posts,
            'category' => $category,
        ]);
    }

    public function single(string $slug)
    {
        $data = Devotion::where('slug', $slug)->firstOrFail();

        $related = Devotion::whereKeyNot($data->getKey())
            ->latest()
            ->take(3)
            ->get()
            ->map(fn (Devotion $d) => $d->append('reading_minutes')->makeHidden('content'))
            ->values();

        return Inertia::render('Devotion', [
            'data' => $data->append('reading_minutes'),
            'related' => $related,
        ]);
    }
}
```

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --filter=DevotionPageTest`
Expected: PASS (5 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/DevotionController.php tests/Feature/DevotionPageTest.php
git commit -m "feat(devotions): category filter, 9-per-page pagination, related posts"
```

---

### Task 5: Settings links, About beliefs, empty-database safety

**Files:**
- Modify: `app/Filament/Pages/GeneralSettings.php`
- Modify: `app/Filament/Pages/AboutPage.php`
- Modify: `app/Http/Controllers/AboutController.php`
- Modify: `app/Http/Controllers/ContactController.php`
- Test: `tests/Feature/AboutPageTest.php`, `tests/Feature/PublicPagesTest.php`

**Interfaces:**
- Produces:
  - `generalSettings.maps_url?: string` and `generalSettings.livestream_url?: string` (shared prop keys).
  - An `AboutUs` prop `beliefs: { title?: string, intro?: string (HTML), link?: string, items?: {title: string, desc: string, verse?: string}[] }`, which is `[]` when unset.
  - `Contact` renders on an empty database.

- [ ] **Step 1: Write the failing tests**

`tests/Feature/PublicPagesTest.php`:

```php
<?php

it('renders every public page on an empty database', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/', '/about', '/division', '/ministry', '/devotion', '/contact']);
```

`tests/Feature/AboutPageTest.php`:

```php
<?php

use App\Models\Pages;
use Inertia\Testing\AssertableInertia as Assert;

it('passes the beliefs section to the about page', function () {
    $about = Pages::create(['name' => 'About']);
    $about->sections()->create(['name' => 'beliefs', 'content' => [
        'title' => 'Apa yang kami percaya',
        'intro' => '<p>Sebagai bagian dari GPdI…</p>',
        'link' => 'https://example.org/pengakuan-iman',
        'items' => [['title' => 'Alkitab', 'desc' => 'Firman Allah.', 'verse' => '2 Timotius 3:16']],
    ]]);

    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('AboutUs')
            ->where('beliefs.title', 'Apa yang kami percaya')
            ->where('beliefs.items.0.verse', '2 Timotius 3:16'));
});

it('sends empty beliefs when none are configured', function () {
    $this->get('/about')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('AboutUs')->where('beliefs', []));
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --filter='PublicPagesTest|AboutPageTest'`
Expected:
- `PublicPagesTest` fails for `/contact` with a 500 ("Call to a member function sections() on null").
- `AboutPageTest` fails because the `beliefs` prop is missing.

- [ ] **Step 3: Make `ContactController` null-safe**

Replace the body of `show()` in `app/Http/Controllers/ContactController.php` with:

```php
        $contactPage = Pages::where('name', 'Contact')->first();

        $heroSection = $contactPage?->sections()->where('name', 'hero')->first();
        $contactListSection = $contactPage?->sections()->where('name', 'contact_list')->first();

        $heroContent = $heroSection?->content ?: ['title' => 'Kami senang bertemu denganmu', 'subtitle' => null, 'image' => null];

        if (! empty($heroContent['image'])) {
            $heroContent['image'] = Storage::url($heroContent['image']);
        }

        return Inertia::render('Contact', [
            'hero' => $heroContent,
            'contactList' => $contactListSection?->content ?? [],
        ]);
```

- [ ] **Step 4: Pass beliefs from `AboutController`**

In `app/Http/Controllers/AboutController.php`:

1. Add `$beliefsData = [];` next to the other `…Data = [];` initialisers.
2. Add a `case 'beliefs': $beliefsData = $section->content; break;` branch to the `switch`.
3. Add `'beliefs' => $beliefsData,` to the `Inertia::render('AboutUs', [ … ])` array.

Keep `history` in the payload; it is unused by the new page but harmless.

- [ ] **Step 5: Run the tests to verify they pass**

Run: `php artisan test --filter='PublicPagesTest|AboutPageTest'`
Expected: PASS (6 dataset cases + 2 tests).

- [ ] **Step 6: Add the beliefs section to the Filament `AboutPage`**

In `app/Filament/Pages/AboutPage.php`:

1. Add `use Filament\Forms\Components\Textarea;` to the imports.
2. Add the property `public ?array $beliefsData = [];` after `$historyData`.
3. In `mount()`, before `$this->form->fill([`, add:

```php
        $beliefsSection = $this->aboutPage->sections()->firstOrCreate(
            ['name' => 'beliefs', 'page_id' => $this->aboutPage->id],
            ['content' => []]
        );
        $this->beliefsData = $beliefsSection->content;
```

4. In the `fill([…])` array, add `'beliefsData' => $this->beliefsData,`.
5. In `form()`, after the "History Section" `Section::make(…)`, add:

```php
            Section::make('Beliefs Section (Pengakuan Iman)')
                ->description('Ditampilkan di halaman Tentang Kami, menggantikan Sejarah.')
                ->schema([
                    TextInput::make('beliefsData.title')
                        ->label('Title')
                        ->placeholder('Apa yang kami percaya'),
                    RichEditor::make('beliefsData.intro')
                        ->label('Intro'),
                    TextInput::make('beliefsData.link')
                        ->label('Link to full Pengakuan Iman GPdI')
                        ->url(),
                    Repeater::make('beliefsData.items')
                        ->label('Belief items')
                        ->schema([
                            TextInput::make('title')->label('Title')->required(),
                            Textarea::make('desc')->label('One-sentence summary')->rows(2)->required(),
                            TextInput::make('verse')->label('Verse reference'),
                        ])
                        ->columns(1)
                        ->reorderable()
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null),
                ]),
```

6. In `submit()`, before the `Notification::make()` call, add:

```php
        $this->aboutPage->sections()->updateOrCreate(
            ['name' => 'beliefs', 'page_id' => $this->aboutPage->id],
            ['content' => $data['beliefsData'] ?? []]
        );
```

- [ ] **Step 7: Add the links to the Filament `GeneralSettings`**

In `app/Filament/Pages/GeneralSettings.php`, inside the "Site Information" section schema, after the `church_address` RichEditor, add:

```php
                    TextInput::make('generalSettingsData.maps_url')
                        ->label('Google Maps link (Petunjuk arah)')
                        ->url(),
                    TextInput::make('generalSettingsData.livestream_url')
                        ->label('Live streaming link (Tonton Online)')
                        ->url(),
```

(`submit()` already saves the whole `generalSettingsData` array, so no change is needed there. Confirm by reading `submit()`; if it copies keys one by one, add the two keys.)

- [ ] **Step 8: Smoke-test the admin**

Manual: in `/admin`:
- Open About Us, add 2 belief items, reorder them, and save. After a reload the order persists.
- Open General Settings, fill in both links, and save. After a reload the values are still there.

- [ ] **Step 9: Run the full suite and commit**

Run: `php artisan test`
Expected: PASS.

```bash
git add app/Filament/Pages/GeneralSettings.php app/Filament/Pages/AboutPage.php app/Http/Controllers/AboutController.php app/Http/Controllers/ContactController.php tests/Feature/AboutPageTest.php tests/Feature/PublicPagesTest.php
git commit -m "feat(about): Pengakuan Iman section; settings links; contact renders on empty db"
```

---

### Task 6: Shared site shell (types, helpers, header, mobile menu, footer, hero, buttons)

**Files:**
- Create: `resources/js/types/site.d.ts`
- Create: `resources/js/lib/site.ts`
- Create: `resources/js/composables/useSiteSettings.ts`
- Create: `resources/js/Components/site/BaseButton.vue`, `SectionHeading.vue`, `PageHero.vue`, `SiteHeader.vue`, `MobileMenu.vue`, `SiteFooter.vue`
- Modify: `resources/js/Layouts/DefaultLayout.vue`

**Interfaces:**
- Consumes: the shared props `generalSettings` (Task 5 keys) and `services` (Task 3). Tailwind names come from Task 1.
- Produces (used by Tasks 7–11):
  - Types: `DivisionItem`, `DevotionSummary`, `DevotionFull`, `Paginated<T>`, `GeneralSettings`, `ServiceTime`
  - `NAV_ITEMS`, `CATEGORY_LABELS`, `storageUrl(path)`, `formatDate(iso)`, `isActive(url, href)`, `readingLabel(minutes)`
  - `useSiteSettings()` → `{ churchName, logo, address, socials, mapsUrl, livestreamUrl, services }`
  - `<BaseButton href? variant? external? type?>`, where `variant` is `'primary'|'outline'|'outline-light'|'light'|'accent'|'link'|'link-light'`
  - `<SectionHeading eyebrow title align? tone? as?>` with an `#actions` slot
  - `<PageHero image? video? eyebrow title subtitle? size?>`; its default slot holds buttons.

- [ ] **Step 1: Create `resources/js/types/site.d.ts`**

```ts
export interface GeneralSettings {
    church_name?: string;
    church_subtitle?: string;
    church_logo?: string;
    church_address?: string;
    social_media?: { platform: string; url: string }[];
    maps_url?: string;
    livestream_url?: string;
}

export interface ServiceTime {
    title: string;
    time: string;
    livestream: boolean;
}

export interface DivisionItem {
    id: number;
    title: string;
    slug: string;
    audience: string | null;
    description: string;
    image: string | null;
    time: string | null;
    short_time: string | null;
    link: string | null;
    sort: number;
}

export type DevotionCategory = 'devotion' | 'news' | 'announcement';

export interface DevotionSummary {
    id: number;
    title: string;
    slug: string;
    category: DevotionCategory;
    image: string | null;
    snippet?: string;
    reading_minutes: number;
    created_at: string;
}

export interface DevotionFull extends DevotionSummary {
    content: string;
}

export interface Paginated<T> {
    data: T[];
    current_page: number;
    last_page: number;
    next_page_url: string | null;
    prev_page_url: string | null;
    links: { url: string | null; label: string; active: boolean }[];
}
```

- [ ] **Step 2: Create `resources/js/lib/site.ts`**

```ts
import { format } from 'date-fns';
import { id as localeId } from 'date-fns/locale';

export const NAV_ITEMS = [
    { name: 'Tentang Kami', href: '/about' },
    { name: 'Divisi', href: '/division' },
    { name: 'Pelayanan', href: '/ministry' },
    { name: 'Renungan', href: '/devotion' },
    { name: 'Kontak', href: '/contact' },
] as const;

export const CATEGORY_LABELS: Record<string, string> = {
    devotion: 'Renungan',
    news: 'Berita',
    announcement: 'Pengumuman',
};

export const MOTTO = 'United as One Family to Live like Christ';

export function storageUrl(path?: string | null): string | null {
    if (!path) return null;
    if (/^(https?:)?\/\//.test(path) || path.startsWith('/')) return path;
    return `/storage/${path}`;
}

export function formatDate(iso: string): string {
    return format(new Date(iso), 'd MMMM yyyy', { locale: localeId });
}

export function isActive(currentUrl: string, href: string): boolean {
    return currentUrl === href || currentUrl.startsWith(`${href}/`) || currentUrl.startsWith(`${href}?`);
}

export function readingLabel(minutes: number): string {
    return `${minutes} menit baca`;
}
```

- [ ] **Step 3: Create `resources/js/composables/useSiteSettings.ts`**

```ts
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import type { GeneralSettings, ServiceTime } from '@/types/site';
import { storageUrl } from '@/lib/site';

const DEFAULT_ADDRESS =
    'Kompleks Pasar Modern Paramount Blok L39, Jl. Boulevard Raya Gading Serpong, Curug Sangereng, Kelapa Dua, Kab. Tangerang, Banten 15810';

export function useSiteSettings() {
    const page = usePage<{ generalSettings: GeneralSettings | null; services: ServiceTime[] }>();
    const settings = computed<GeneralSettings>(() => page.props.generalSettings ?? {});

    return {
        churchName: computed(() => settings.value.church_name || 'Hebron Family Church'),
        logo: computed(() => storageUrl(settings.value.church_logo) ?? '/hebron.svg'),
        address: computed(() => settings.value.church_address || DEFAULT_ADDRESS),
        socials: computed(() => settings.value.social_media ?? []),
        mapsUrl: computed(() => settings.value.maps_url || 'https://maps.google.com/?q=GPdI+Hebron+Gading+Serpong'),
        livestreamUrl: computed(() => settings.value.livestream_url || null),
        services: computed<ServiceTime[]>(() => page.props.services ?? []),
    };
}
```

- [ ] **Step 4: Create the presentational components**

`resources/js/Components/site/BaseButton.vue`:

```vue
<script setup lang="ts">
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = withDefaults(
    defineProps<{
        href?: string | null;
        variant?: 'primary' | 'outline' | 'outline-light' | 'light' | 'accent' | 'link' | 'link-light';
        external?: boolean;
        type?: 'button' | 'submit';
    }>(),
    { href: null, variant: 'primary', external: false, type: 'button' },
);

const VARIANTS = {
    primary: 'bg-heading text-white hover:bg-brand-teal focus-visible:outline-brand-teal',
    outline: 'border border-heading text-heading hover:bg-heading hover:text-white focus-visible:outline-brand-teal',
    'outline-light': 'border border-white/70 text-white hover:bg-white hover:text-heading focus-visible:outline-brand-orange',
    light: 'bg-white text-heading hover:bg-brand-cream focus-visible:outline-brand-orange',
    accent: 'bg-brand-orange text-heading hover:bg-brand-orange/90 focus-visible:outline-white',
    link: '!min-h-0 !px-0 text-brand-teal hover:text-heading focus-visible:outline-brand-teal',
    'link-light': '!min-h-0 !px-0 text-white hover:text-brand-mint focus-visible:outline-brand-orange',
} as const;

const classes = computed(
    () =>
        `inline-flex min-h-[44px] items-center justify-center gap-2.5 rounded-button px-6 text-sm font-semibold transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 ${VARIANTS[props.variant]}`,
);
const internal = computed(() => !!props.href && !props.external && props.href.startsWith('/'));
</script>

<template>
    <Link v-if="internal" :href="href!" :class="classes"><slot /></Link>
    <a
        v-else-if="href"
        :href="href"
        :class="classes"
        :target="external ? '_blank' : undefined"
        :rel="external ? 'noopener noreferrer' : undefined"
        ><slot
    /></a>
    <button v-else :type="type" :class="classes"><slot /></button>
</template>
```

`resources/js/Components/site/SectionHeading.vue`:

```vue
<script setup lang="ts">
withDefaults(
    defineProps<{
        eyebrow: string;
        title: string;
        align?: 'left' | 'center';
        tone?: 'light' | 'dark';
        as?: 'h1' | 'h2';
    }>(),
    { align: 'left', tone: 'light', as: 'h2' },
);
</script>

<template>
    <div
        class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
        :class="align === 'center' ? 'md:justify-center' : ''"
    >
        <div class="flex flex-col gap-4 md:gap-5" :class="align === 'center' ? 'items-center text-center' : ''">
            <p
                class="flex items-center gap-4 text-[11px] font-semibold uppercase tracking-[0.2em] md:text-xs"
                :class="tone === 'dark' ? 'text-brand-orange' : 'text-brand-teal'"
            >
                <span aria-hidden="true" class="h-0.5 w-7 bg-decor md:w-10"></span>{{ eyebrow }}
            </p>
            <component
                :is="as"
                class="font-serif text-[40px] leading-[1.05] md:text-[64px] md:leading-[1.02]"
                :class="tone === 'dark' ? 'text-white' : 'text-heading'"
                >{{ title }}</component
            >
        </div>
        <div v-if="$slots.actions" class="flex items-center gap-6"><slot name="actions" /></div>
    </div>
</template>
```

`resources/js/Components/site/PageHero.vue`:

```vue
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { storageUrl } from '@/lib/site';

const props = withDefaults(
    defineProps<{
        image?: string | null;
        video?: string | null;
        eyebrow: string;
        title: string;
        subtitle?: string | null;
        size?: 'full' | 'page';
    }>(),
    { image: null, video: null, subtitle: null, size: 'page' },
);

const imageUrl = computed(() => storageUrl(props.image));
const videoUrl = computed(() => storageUrl(props.video));
const videoEl = ref<HTMLVideoElement | null>(null);
const playing = ref(false);

function play() {
    videoEl.value
        ?.play()
        .then(() => (playing.value = true))
        .catch(() => (playing.value = false));
}

onMounted(() => {
    if (videoEl.value && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) play();
});

function toggle() {
    if (!videoEl.value) return;
    if (videoEl.value.paused) {
        play();
    } else {
        videoEl.value.pause();
        playing.value = false;
    }
}
</script>

<template>
    <section
        class="relative isolate overflow-hidden bg-brand-deep text-white"
        :class="size === 'full' ? 'h-[100svh] min-h-[560px]' : 'h-[480px] md:h-[560px]'"
    >
        <img v-if="imageUrl" :src="imageUrl" alt="" class="absolute inset-0 -z-20 h-full w-full object-cover" />
        <video
            v-if="videoUrl"
            ref="videoEl"
            :src="videoUrl"
            :poster="imageUrl ?? undefined"
            muted
            playsinline
            loop
            preload="metadata"
            aria-hidden="true"
            class="absolute inset-0 -z-20 h-full w-full object-cover"
        ></video>
        <div class="absolute inset-0 -z-10 bg-brand-deeper/30"></div>

        <div class="absolute inset-x-5 bottom-10 flex max-w-3xl flex-col gap-4 md:inset-x-14 md:bottom-16 md:gap-5">
            <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-orange md:text-[11px]">{{ eyebrow }}</p>
            <h1
                class="font-serif leading-[1.02]"
                :class="size === 'full' ? 'text-[44px] md:max-w-[620px] md:text-[60px]' : 'text-[46px] md:text-[72px] md:leading-none'"
            >
                {{ title }}
            </h1>
            <p v-if="subtitle" class="text-[15px] leading-relaxed text-white/85 md:text-[17px]">{{ subtitle }}</p>
            <div v-if="$slots.default" class="mt-1 flex flex-wrap items-center gap-3 md:gap-7"><slot /></div>
        </div>

        <button
            v-if="videoUrl"
            type="button"
            class="absolute right-5 top-20 flex h-11 w-11 items-center justify-center rounded-full border border-white/60 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-orange md:bottom-16 md:right-14 md:top-auto"
            :aria-label="playing ? 'Jeda video latar' : 'Putar video latar'"
            @click="toggle"
        >
            <svg v-if="playing" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M9 6v12M15 6v12" /></svg>
            <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" /></svg>
        </button>
    </section>
</template>
```

`resources/js/Components/site/MobileMenu.vue`:

```vue
<script setup lang="ts">
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { NAV_ITEMS } from '@/lib/site';
import { useSiteSettings } from '@/composables/useSiteSettings';

const props = defineProps<{ open: boolean }>();
const emit = defineEmits<{ close: [] }>();
const { churchName, logo, services } = useSiteSettings();
const panel = ref<HTMLElement | null>(null);

function focusables(): HTMLElement[] {
    return Array.from(panel.value?.querySelectorAll<HTMLElement>('a[href], button:not([disabled])') ?? []);
}

function onKeydown(event: KeyboardEvent) {
    if (event.key === 'Escape') {
        event.preventDefault();
        emit('close');
        return;
    }
    if (event.key !== 'Tab') return;
    const items = focusables();
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
    }
}

watch(
    () => props.open,
    async (open) => {
        document.body.style.overflow = open ? 'hidden' : '';
        if (open) {
            await nextTick();
            focusables()[0]?.focus();
        }
    },
);

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') document.body.style.overflow = '';
});
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-200"
        enter-from-class="opacity-0"
        leave-active-class="transition-opacity duration-200"
        leave-to-class="opacity-0"
    >
        <div
            v-if="open"
            id="mobile-menu"
            ref="panel"
            role="dialog"
            aria-modal="true"
            aria-label="Menu"
            class="fixed inset-0 z-[60] flex flex-col bg-brand-deep text-white lg:hidden"
            @keydown="onKeydown"
        >
            <div class="flex h-16 items-center justify-between pl-5 pr-3">
                <Link href="/" class="flex items-center gap-2.5" @click="emit('close')">
                    <img :src="logo" alt="" class="h-7 w-auto" />
                    <span class="font-serif text-[19px]">{{ churchName }}</span>
                </Link>
                <button type="button" aria-label="Tutup menu" class="flex h-11 w-11 items-center justify-center" @click="emit('close')">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" /></svg>
                </button>
            </div>
            <nav aria-label="Utama" class="flex flex-col px-5 pt-10">
                <Link
                    v-for="item in NAV_ITEMS"
                    :key="item.href"
                    :href="item.href"
                    class="border-b border-white/15 py-4 font-serif text-[40px] leading-none"
                    @click="emit('close')"
                    >{{ item.name }}</Link
                >
            </nav>
            <div class="mt-auto flex flex-col gap-5 px-5 pb-10">
                <div v-if="services.length" class="flex flex-col gap-1.5 text-sm text-white/80">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange">Setiap Minggu</p>
                    <p>{{ services.map((s) => s.time).join(' · ') }} WIB</p>
                </div>
                <Link
                    href="/contact"
                    class="flex h-[52px] items-center justify-center rounded-button bg-white text-[15px] font-semibold text-heading"
                    @click="emit('close')"
                    >Kunjungi Ibadah Kami</Link
                >
            </div>
        </div>
    </Transition>
</template>
```

`resources/js/Components/site/SiteHeader.vue`:

```vue
<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { NAV_ITEMS, isActive } from '@/lib/site';
import { useSiteSettings } from '@/composables/useSiteSettings';
import MobileMenu from './MobileMenu.vue';

const page = usePage();
const { churchName, logo } = useSiteSettings();
const scrolled = ref(false);
const menuOpen = ref(false);
const menuButton = ref<HTMLButtonElement | null>(null);
const left = NAV_ITEMS.slice(0, 3);
const right = NAV_ITEMS.slice(3);

function onScroll() {
    scrolled.value = window.scrollY > 40;
}
onMounted(() => {
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
});
onBeforeUnmount(() => window.removeEventListener('scroll', onScroll));

function closeMenu() {
    menuOpen.value = false;
    menuButton.value?.focus();
}

function linkClass(href: string) {
    return [
        'border-b py-1.5 transition-colors hover:text-brand-mint focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-orange',
        isActive(page.url, href) ? 'border-decor' : 'border-transparent',
    ];
}
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-50 text-white transition-colors duration-300"
        :class="scrolled ? 'bg-brand-deep shadow-lg' : 'bg-transparent'"
    >
        <div class="flex h-16 items-center justify-between pl-5 pr-3 lg:grid lg:h-[88px] lg:grid-cols-[1fr_auto_1fr] lg:px-14">
            <nav aria-label="Utama" class="hidden gap-8 text-xs font-medium uppercase tracking-[0.16em] lg:flex">
                <Link
                    v-for="item in left"
                    :key="item.href"
                    :href="item.href"
                    :class="linkClass(item.href)"
                    :aria-current="isActive(page.url, item.href) ? 'page' : undefined"
                    >{{ item.name }}</Link
                >
            </nav>
            <Link href="/" class="flex items-center gap-2.5">
                <img :src="logo" alt="" class="h-7 w-auto lg:h-[30px]" />
                <span class="font-serif text-[19px] lg:text-xl">{{ churchName }}</span>
            </Link>
            <nav aria-label="Sekunder" class="hidden items-center justify-end gap-8 text-xs font-medium uppercase tracking-[0.16em] lg:flex">
                <Link
                    v-for="item in right"
                    :key="item.href"
                    :href="item.href"
                    :class="linkClass(item.href)"
                    :aria-current="isActive(page.url, item.href) ? 'page' : undefined"
                    >{{ item.name }}</Link
                >
                <Link
                    href="/contact"
                    class="rounded-button border border-white/70 px-4 py-2.5 transition-colors hover:bg-white hover:text-heading"
                    >Kunjungi Kami</Link
                >
            </nav>
            <button
                ref="menuButton"
                type="button"
                class="flex h-11 w-11 items-center justify-center lg:hidden"
                aria-label="Buka menu"
                aria-controls="mobile-menu"
                :aria-expanded="menuOpen"
                @click="menuOpen = true"
            >
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><path d="M4 8h16M4 16h16" /></svg>
            </button>
        </div>
    </header>
    <MobileMenu :open="menuOpen" @close="closeMenu" />
</template>
```

`resources/js/Components/site/SiteFooter.vue`:

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { MOTTO, NAV_ITEMS } from '@/lib/site';
import { useSiteSettings } from '@/composables/useSiteSettings';

const { churchName, logo, address, socials, services } = useSiteSettings();
const year = new Date().getFullYear();
</script>

<template>
    <footer class="bg-brand-deeper text-white">
        <div class="container-site flex flex-col gap-10 pb-8 pt-16 lg:gap-[72px] lg:pb-12 lg:pt-24">
            <div class="grid gap-10 lg:grid-cols-[2fr_1fr_1fr_1.4fr] lg:gap-16">
                <div class="flex flex-col gap-5">
                    <div class="flex items-center gap-3">
                        <img :src="logo" alt="" class="h-9 w-auto lg:h-11" />
                        <span class="font-serif text-2xl lg:text-[28px]">{{ churchName }}</span>
                    </div>
                    <p class="max-w-[360px] font-serif text-2xl italic leading-tight text-white/90 lg:text-[28px]">{{ MOTTO }}.</p>
                </div>
                <div class="grid grid-cols-2 gap-6 lg:contents">
                    <div class="flex flex-col gap-3 text-[15px]">
                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange lg:text-xs">Jelajahi</p>
                        <Link v-for="item in NAV_ITEMS" :key="item.href" :href="item.href" class="text-white/80 hover:text-white">{{ item.name }}</Link>
                    </div>
                    <div class="flex flex-col gap-3 text-[15px]">
                        <p class="mb-1 text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange lg:text-xs">Terhubung</p>
                        <a
                            v-for="social in socials"
                            :key="social.url"
                            :href="social.url"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-white/80 hover:text-white"
                            >{{ social.platform }}</a
                        >
                    </div>
                </div>
                <div class="flex flex-col gap-3 text-sm leading-relaxed text-white/80 lg:text-[15px]">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-orange lg:text-xs">Kunjungi</p>
                    <div class="rich-text" v-html="address"></div>
                    <p v-if="services.length">Setiap Minggu · {{ services.map((s) => s.time).join(' · ') }} WIB</p>
                </div>
            </div>
            <div class="flex flex-col gap-1.5 border-t border-white/15 pt-6 text-xs text-white/60 lg:flex-row lg:justify-between lg:pt-8 lg:text-[13px]">
                <span>© {{ year }} GPdI Hebron Gading Serpong. Hak cipta dilindungi.</span>
                <span>GPdI Hebron Production Team w/ 34 Creative Labs</span>
            </div>
        </div>
    </footer>
</template>
```

- [ ] **Step 5: Replace `resources/js/Layouts/DefaultLayout.vue`**

```vue
<script setup lang="ts">
import SiteHeader from '@/Components/site/SiteHeader.vue';
import SiteFooter from '@/Components/site/SiteFooter.vue';
</script>

<template>
    <a
        href="#konten"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[70] focus:bg-white focus:px-4 focus:py-2 focus:text-heading"
        >Lewati ke konten</a
    >
    <SiteHeader />
    <main id="konten" class="min-h-screen bg-white font-sans text-ink">
        <slot />
    </main>
    <SiteFooter />
</template>
```

- [ ] **Step 6: Build and check manually**

Run: `npm run build`
Expected: exits 0. The old pages still render inside the new shell. Styling mismatches inside pages are expected until Tasks 7–11.

Manual check. Run `php artisan serve` and `npm run dev` in two terminals, then open `http://127.0.0.1:8000/about`.
- **Header:**
  - It is transparent over the hero with the colour logo centred.
  - It turns deep teal after scrolling 40px.
  - "Tentang Kami" has a coral underline.
- **Phone width (390px):**
  - The menu button opens a full-screen deep-teal menu.
  - Tab stays inside the menu, and Esc closes it and returns focus to the menu button.
- **Footer:** matches the canvas (Shared components page). The credit reads "GPdI Hebron Production Team w/ 34 Creative Labs".
- **Review Focus #5:**
  - In Filament, set a hero video on Home, then open `/` with the OS "Reduce motion" setting on. The poster image shows, the video does not play, and the button's accessible name is "Putar video latar".
  - Press the button. The video plays and the name becomes "Jeda video latar".
  - Rename the video file on disk so it 404s and reload. The poster image stays visible with no broken area.

- [ ] **Step 7: Commit**

```bash
git add resources/js/types/site.d.ts resources/js/lib/site.ts resources/js/composables/useSiteSettings.ts resources/js/Components/site resources/js/Layouts/DefaultLayout.vue
git commit -m "feat(ui): shared site shell — header, mobile menu, footer, hero, buttons"
```

---

### Task 7: Homepage

**Files:**
- Create: `resources/js/Components/site/DivisionCard.vue`, `resources/js/Components/site/PostCard.vue`
- Modify: `resources/js/Pages/Welcome.vue` (full rewrite)
- Modify: `resources/js/types/home-page.d.ts`

**Interfaces:**
- Consumes: Task 6 exports. Props from Task 3: `divisions: DivisionItem[]` and `latestDevotions: DevotionSummary[]`, plus the shared `services`.
- Produces:
  - `<DivisionCard :division="DivisionItem">`, which links to `/division#{slug}`.
  - `<PostCard :post="DevotionSummary" variant="grid|featured">`. The grid variant renders as a thumbnail row below `md` and as a card from `md` up. Tasks 10 and 11 reuse it.

- [ ] **Step 1: Extend `resources/js/types/home-page.d.ts`**

- Add `video?: string;` to `HeroSectionData`.
- Add `livestream?: boolean;` to `ServiceItem`.
- In `HomePageProps`, add:

```ts
    divisions: import('./site').DivisionItem[];
    latestDevotions: import('./site').DevotionSummary[];
```

- [ ] **Step 2: Create `resources/js/Components/site/DivisionCard.vue`**

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { DivisionItem } from '@/types/site';
import { storageUrl } from '@/lib/site';

defineProps<{ division: DivisionItem }>();
</script>

<template>
    <Link :href="`/division#${division.slug}`" class="group flex w-[260px] shrink-0 snap-start flex-col gap-2.5 text-ink lg:w-[290px] lg:gap-3.5">
        <div class="mb-1.5 h-[300px] overflow-hidden bg-brand-mint/40 lg:h-[360px]">
            <img
                v-if="division.image"
                :src="storageUrl(division.image)!"
                :alt="division.title"
                loading="lazy"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            />
        </div>
        <p v-if="division.audience" class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-muted lg:text-xs">{{ division.audience }}</p>
        <p class="font-serif text-[28px] leading-[1.05] text-heading lg:text-[32px]">{{ division.title }}</p>
        <p v-if="division.time" class="text-sm font-semibold text-brand-teal lg:text-[15px]">{{ division.time }}</p>
        <p v-else class="text-sm font-medium text-ink-muted lg:text-[15px]">Jadwal akan diumumkan</p>
        <span class="mt-1 hidden border-t border-line pt-3.5 text-sm font-semibold text-brand-teal lg:block">Selengkapnya →</span>
    </Link>
</template>
```

- [ ] **Step 3: Create `resources/js/Components/site/PostCard.vue`**

```vue
<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import type { DevotionSummary } from '@/types/site';
import { CATEGORY_LABELS, formatDate, readingLabel, storageUrl } from '@/lib/site';

withDefaults(defineProps<{ post: DevotionSummary; variant?: 'grid' | 'featured' }>(), { variant: 'grid' });
</script>

<template>
    <Link
        v-if="variant === 'featured'"
        :href="`/devotion/${post.slug}`"
        class="group grid gap-6 text-ink lg:grid-cols-[7fr_5fr] lg:items-center lg:gap-[72px]"
    >
        <div class="h-[240px] overflow-hidden bg-brand-teal lg:h-[480px]">
            <img v-if="post.image" :src="storageUrl(post.image)!" :alt="post.title" class="h-full w-full object-cover" />
        </div>
        <div class="flex flex-col gap-3 lg:gap-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-brand-teal lg:text-xs">Terbaru · {{ CATEGORY_LABELS[post.category] }}</p>
            <p class="font-serif text-[34px] leading-[1.08] text-heading group-hover:underline lg:text-[52px] lg:leading-[1.05]">{{ post.title }}</p>
            <p v-if="post.snippet" class="text-[15px] leading-relaxed text-ink-muted lg:text-[17px]">{{ post.snippet }}</p>
            <p class="text-xs text-ink-muted lg:text-[13px]">{{ formatDate(post.created_at) }} · {{ readingLabel(post.reading_minutes) }}</p>
        </div>
    </Link>
    <Link v-else :href="`/devotion/${post.slug}`" class="group flex gap-4 border-b border-line py-5 text-ink md:flex-col md:gap-4 md:border-0 md:py-0">
        <div class="h-[104px] w-[104px] shrink-0 overflow-hidden bg-brand-mint/40 md:mb-1.5 md:h-[240px] md:w-full">
            <img v-if="post.image" :src="storageUrl(post.image)!" :alt="post.title" loading="lazy" class="h-full w-full object-cover" />
        </div>
        <div class="flex flex-col gap-1.5 md:gap-4">
            <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-brand-teal md:text-xs">{{ CATEGORY_LABELS[post.category] }} · {{ formatDate(post.created_at) }}</p>
            <p class="font-serif text-[22px] leading-[1.15] text-heading group-hover:underline md:text-[30px] md:leading-[1.12]">{{ post.title }}</p>
            <p v-if="post.snippet" class="hidden text-[15px] leading-relaxed text-ink-muted md:line-clamp-2">{{ post.snippet }}</p>
            <p class="text-xs text-ink-muted md:text-[13px]">{{ readingLabel(post.reading_minutes) }}</p>
        </div>
    </Link>
</template>
```

- [ ] **Step 4: Rewrite `resources/js/Pages/Welcome.vue`**

```vue
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import DivisionCard from '@/Components/site/DivisionCard.vue';
import PostCard from '@/Components/site/PostCard.vue';
import { useSiteSettings } from '@/composables/useSiteSettings';
import { MOTTO, storageUrl } from '@/lib/site';
import type { HomePageProps } from '@/types/home-page';

const props = defineProps<HomePageProps>();
const { address, mapsUrl, livestreamUrl, services, socials } = useSiteSettings();

const gallery = computed(() => (props.carousel ?? []).slice(0, 4));
const instagram = computed(() => socials.value.find((s) => s.platform.toLowerCase().includes('instagram'))?.url ?? null);
const steps = computed(() => [
    { n: '01', title: 'Pelayanan', desc: 'Layani Tuhan dengan talenta dan karunia yang Ia berikan.', href: '/ministry', image: props.carousel?.[4] ?? null },
    { n: '02', title: 'Renungan', desc: 'Renungan Firman untuk berjalan lebih dekat dengan Kristus setiap hari.', href: '/devotion', image: props.carousel?.[5] ?? null },
    { n: '03', title: 'Hubungi Kami', desc: 'Pertanyaan, permohonan doa, atau ingin berbincang dengan gembala kami.', href: '/contact', image: props.carousel?.[6] ?? null },
]);

const track = ref<HTMLElement | null>(null);
const progress = ref(0);
function updateProgress() {
    const el = track.value;
    if (!el) return;
    const max = el.scrollWidth - el.clientWidth;
    progress.value = max > 0 ? el.scrollLeft / max : 1;
}
function scrollDivisions(direction: 1 | -1) {
    const el = track.value;
    if (!el) return;
    el.scrollBy({ left: direction * el.clientWidth * 0.8, behavior: 'smooth' });
}
onMounted(updateProgress);
</script>

<template>
    <Head title="Beranda" />
    <DefaultLayout>
        <PageHero :image="hero?.image" :video="hero?.video" size="full" eyebrow="GPdI Hebron · Gading Serpong" :title="hero?.title || MOTTO">
            <BaseButton href="/contact" variant="light">Kunjungi Ibadah Kami</BaseButton>
            <BaseButton v-if="livestreamUrl" :href="livestreamUrl" external variant="link-light">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M10 8.5l5 3.5-5 3.5z" /></svg>
                Tonton Online
            </BaseButton>
        </PageHero>

        <!-- Ibadah Raya band -->
        <section v-if="services.length" class="bg-brand-deep text-white">
            <div class="container-site grid gap-6 py-12 lg:grid-cols-4 lg:items-center lg:gap-12 lg:py-14">
                <h2 class="font-serif text-[32px] leading-tight lg:text-4xl">Ibadah Raya</h2>
                <div
                    v-for="service in services"
                    :key="service.title"
                    class="flex items-center justify-between border-t border-white/20 pt-4 lg:flex-col lg:items-start lg:gap-1.5 lg:border-l lg:border-t-0 lg:pl-8 lg:pt-0"
                >
                    <div class="flex flex-col gap-1.5">
                        <p class="text-[11px] uppercase tracking-[0.18em] text-brand-orange lg:text-xs">{{ service.title }}</p>
                        <p v-if="service.livestream" class="flex items-center gap-2 text-xs font-semibold lg:order-last">
                            <span aria-hidden="true" class="h-2 w-2 rounded-full bg-decor"></span>Live Streaming
                        </p>
                    </div>
                    <p class="font-serif text-[32px] lg:text-[40px]">{{ service.time }} <span class="font-sans text-xs text-white/70 lg:text-sm">WIB</span></p>
                </div>
            </div>
        </section>

        <!-- Divisi carousel -->
        <section v-if="divisions.length" class="section-y">
            <div class="container-site">
                <SectionHeading :eyebrow="`${divisions.length} Divisi`" title="Ibadah untuk setiap generasi">
                    <template #actions>
                        <BaseButton href="/division" variant="link">Semua divisi →</BaseButton>
                        <div class="hidden gap-2 lg:flex">
                            <button type="button" aria-label="Divisi sebelumnya" class="flex h-12 w-12 items-center justify-center rounded-full border border-line-strong text-heading" @click="scrollDivisions(-1)">←</button>
                            <button type="button" aria-label="Divisi berikutnya" class="flex h-12 w-12 items-center justify-center rounded-full bg-heading text-white" @click="scrollDivisions(1)">→</button>
                        </div>
                    </template>
                </SectionHeading>
            </div>
            <div
                ref="track"
                class="mt-8 flex snap-x snap-mandatory gap-3.5 overflow-x-auto pl-5 pr-5 [scrollbar-width:none] lg:mt-14 lg:gap-6 lg:pl-[120px]"
                @scroll.passive="updateProgress"
            >
                <DivisionCard v-for="division in divisions" :key="division.id" :division="division" />
            </div>
            <div class="container-site mt-8 flex items-center gap-4">
                <div class="relative h-0.5 flex-grow bg-line">
                    <div class="absolute inset-y-0 left-0 bg-heading transition-[width]" :style="{ width: `${Math.max(progress, 1 / divisions.length) * 100}%` }"></div>
                </div>
            </div>
        </section>

        <!-- Visi & Misi -->
        <section v-if="visionMission?.visi" class="bg-mist lg:bg-white">
            <div class="container-site section-y grid gap-10 lg:grid-cols-[5fr_7fr] lg:gap-28">
                <div class="flex flex-col gap-6">
                    <SectionHeading eyebrow="Tentang Hebron" :title="visionMission.title || 'Visi & Misi'" />
                    <BaseButton href="/about" variant="link">{{ visionMission.about_button_text || 'Selengkapnya tentang Hebron' }} →</BaseButton>
                </div>
                <div class="flex flex-col">
                    <div class="flex flex-col gap-3 border-b border-line pb-10 lg:grid lg:grid-cols-[160px_1fr] lg:gap-8 lg:pb-12">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-muted lg:pt-3 lg:text-xs">{{ visionMission.visi.title }}</p>
                        <div class="rich-text font-serif text-[26px] leading-tight text-heading lg:text-[34px] lg:leading-snug" v-html="visionMission.visi.desc"></div>
                    </div>
                    <div class="flex flex-col gap-3 pt-10 lg:grid lg:grid-cols-[160px_1fr] lg:gap-8 lg:pt-12">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-ink-muted lg:pt-1.5 lg:text-xs">{{ visionMission.misi.title }}</p>
                        <div class="rich-text mission-list text-base leading-relaxed lg:text-lg" v-html="visionMission.misi.desc"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sambutan gembala -->
        <section v-if="pastorGreeting?.pastors_name" class="bg-white lg:bg-mist">
            <div class="container-site section-y grid gap-8 lg:grid-cols-[5fr_7fr] lg:items-center lg:gap-28">
                <div class="relative h-[400px] bg-brand-mint/40 lg:h-[620px]">
                    <img v-if="pastorGreeting.pastor_img" :src="storageUrl(pastorGreeting.pastor_img)!" :alt="pastorGreeting.pastors_name" loading="lazy" class="h-full w-full object-cover" />
                    <img v-if="pastorGreeting.pastor_signature" :src="storageUrl(pastorGreeting.pastor_signature)!" alt="" class="absolute bottom-4 right-4 h-24 lg:h-32" />
                </div>
                <div class="flex flex-col gap-6 lg:gap-8">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ pastorGreeting.subtitle || 'Sambutan dari gembala kami' }}</p>
                    <div class="rich-text font-serif text-[28px] italic leading-tight text-heading lg:text-[40px] lg:leading-snug" v-html="pastorGreeting.desc"></div>
                    <div class="flex flex-col gap-1">
                        <p class="font-serif text-2xl text-heading lg:text-[28px]">{{ pastorGreeting.pastors_name }}</p>
                        <p class="text-[13px] text-ink-muted lg:text-sm">Gembala Sidang</p>
                    </div>
                    <BaseButton href="/about#pastoral" variant="outline" class="self-stretch lg:self-start">{{ pastorGreeting.pastoral_button_text || 'Lihat tim pastoral Hebron' }} →</BaseButton>
                </div>
            </div>
        </section>

        <!-- Galeri -->
        <section v-if="gallery.length" class="pt-16 lg:pt-32">
            <div class="container-site">
                <SectionHeading eyebrow="Kehidupan di Hebron" title="Momen sebagai satu keluarga">
                    <template v-if="instagram" #actions>
                        <BaseButton :href="instagram" external variant="link">Ikuti kami di Instagram</BaseButton>
                    </template>
                </SectionHeading>
            </div>
            <div class="mt-8 grid grid-cols-2 gap-1.5 px-1.5 lg:mt-14 lg:h-[420px] lg:grid-cols-4 lg:gap-3 lg:px-3">
                <img
                    v-for="(image, index) in gallery"
                    :key="image"
                    :src="storageUrl(image)!"
                    alt=""
                    loading="lazy"
                    class="h-[200px] w-full object-cover lg:h-full"
                    :class="index % 2 ? 'lg:mt-14 lg:h-[calc(100%-56px)]' : ''"
                />
            </div>
        </section>

        <!-- Langkah selanjutnya -->
        <section class="bg-mist lg:bg-white">
            <div class="container-site section-y flex flex-col gap-10 lg:gap-[72px]">
                <SectionHeading eyebrow="Bertumbuh bersama kami" title="Temukan tempatmu di keluarga Hebron" align="center" />
                <div class="grid gap-10 lg:grid-cols-3">
                    <Link v-for="step in steps" :key="step.n" :href="step.href" class="group flex flex-col gap-3 text-ink">
                        <div class="h-[200px] bg-brand-mint/40 lg:h-[320px]">
                            <img v-if="step.image" :src="storageUrl(step.image)!" alt="" loading="lazy" class="h-full w-full object-cover" />
                        </div>
                        <span class="mt-2 text-xs font-semibold tracking-[0.2em] text-brand-teal">{{ step.n }}</span>
                        <span class="font-serif text-[30px] leading-none text-heading group-hover:underline lg:text-[38px]">{{ step.title }}</span>
                        <span class="text-[15px] leading-relaxed text-ink-muted lg:text-base">{{ step.desc }}</span>
                    </Link>
                </div>
            </div>
        </section>

        <!-- Renungan terbaru -->
        <section v-if="latestDevotions.length" class="bg-white lg:bg-mist">
            <div class="container-site section-y flex flex-col gap-8 lg:gap-14">
                <SectionHeading eyebrow="Renungan & Berita" title="Terbaru dari Firman">
                    <template #actions><BaseButton href="/devotion" variant="link">Lihat semua renungan →</BaseButton></template>
                </SectionHeading>
                <div class="border-t border-line md:grid md:grid-cols-3 md:gap-10 md:border-0">
                    <PostCard v-for="post in latestDevotions" :key="post.id" :post="post" />
                </div>
            </div>
        </section>

        <!-- Mari beribadah -->
        <section class="bg-brand-teal text-white">
            <div class="container-site grid gap-6 py-16 lg:grid-cols-[7fr_5fr] lg:items-center lg:gap-24 lg:py-[120px]">
                <div class="flex flex-col gap-6 lg:gap-7">
                    <span aria-hidden="true" class="h-[3px] w-12 bg-decor lg:w-14"></span>
                    <h2 class="font-serif text-[44px] leading-none lg:text-[80px]">Mari beribadah bersama kami</h2>
                    <p class="max-w-[520px] text-base leading-relaxed text-white/85 lg:text-lg">Baik pertama kali datang maupun kembali pulang, selalu ada tempat untukmu di keluarga Hebron.</p>
                    <div class="flex flex-col gap-3 sm:flex-row sm:gap-4">
                        <BaseButton :href="mapsUrl" external variant="light">Petunjuk arah</BaseButton>
                        <BaseButton href="/contact" variant="outline-light">Hubungi kami</BaseButton>
                    </div>
                </div>
                <div class="flex flex-col gap-8 lg:border-l lg:border-white/25 lg:pl-12">
                    <div class="flex flex-col gap-2.5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-cream">Alamat</p>
                        <div class="rich-text text-sm leading-relaxed text-white/85 lg:text-lg" v-html="address"></div>
                    </div>
                    <div v-if="services.length" class="flex flex-col gap-2.5">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-cream">Setiap Minggu</p>
                        <p class="text-sm lg:text-lg">{{ services.map((s) => s.time).join(' · ') }} WIB</p>
                        <p v-if="services.some((s) => s.livestream)" class="text-sm text-white/75">
                            {{ services.filter((s) => s.livestream).map((s) => `${s.title} (${s.time} WIB)`).join(', ') }} juga disiarkan secara live streaming.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
```

- [ ] **Step 5: Build**

Run: `npm run build`
Expected: exits 0.

- [ ] **Step 6: Compare with the canvas**

Manual: open `/` at 1440px and 390px, next to the canvas **Home** page (`Main.dc.html` and `HomeMobile.dc.html`). Check:
- The section order, with the band showing times and "WIB" (never "WIB WIB").
- The division carousel scrolls with the arrows, and the progress bar moves.
- An empty-time division shows "Jadwal akan diumumkan" in grey.
- The Visi/Misi mission list shows lower-roman teal numerals.
- The "Mari beribadah" labels are cream, not orange.

- [ ] **Step 7: Commit**

```bash
git add resources/js/Components/site/DivisionCard.vue resources/js/Components/site/PostCard.vue resources/js/Pages/Welcome.vue resources/js/types/home-page.d.ts
git commit -m "feat(home): rebuild homepage on the new design system"
```

---

### Task 8: Tentang Kami (About)

**Files:**
- Modify: `resources/js/Pages/AboutUs.vue` (full rewrite)
- Modify: `resources/js/types/about-page.d.ts`

**Interfaces:**
- Consumes: Task 6 exports, and the `beliefs` prop (Task 5).

- [ ] **Step 1: Extend `resources/js/types/about-page.d.ts`**

Add:

```ts
export interface BeliefItem {
    title: string;
    desc: string;
    verse?: string;
}

export interface BeliefsSectionData {
    title?: string;
    intro?: string;
    link?: string;
    items?: BeliefItem[];
}
```

Then add `beliefs: BeliefsSectionData | [];` to `AboutPageProps`.

- [ ] **Step 2: Rewrite `resources/js/Pages/AboutUs.vue`**

```vue
<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { storageUrl } from '@/lib/site';
import type { AboutPageProps, BeliefsSectionData } from '@/types/about-page';

const props = defineProps<AboutPageProps>();
const beliefs = computed<BeliefsSectionData>(() => (Array.isArray(props.beliefs) ? {} : props.beliefs ?? {}));
const beliefItems = computed(() => beliefs.value.items ?? []);
</script>

<template>
    <Head title="Tentang Kami" />
    <DefaultLayout>
        <PageHero :image="hero?.image" eyebrow="Tentang Kami" :title="hero?.title || 'Tentang Hebron'" :subtitle="hero?.subtitle" />

        <!-- Visi & Misi -->
        <section v-if="visionMission?.visi" class="container-site section-y grid gap-8 lg:grid-cols-[5fr_7fr] lg:items-center lg:gap-28">
            <div class="h-[380px] overflow-hidden rounded-t-[170px] rounded-b bg-brand-mint/40 lg:h-[600px] lg:rounded-t-[240px]">
                <img v-if="visionMission.image" :src="storageUrl(visionMission.image)!" alt="" loading="lazy" class="h-full w-full object-cover" />
            </div>
            <div class="flex flex-col gap-8 lg:gap-12">
                <div class="flex flex-col gap-3 border-b border-line pb-8 lg:gap-5 lg:border-0 lg:pb-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ visionMission.visi.title }}</p>
                    <div class="rich-text font-serif text-[28px] leading-tight text-heading lg:text-[40px]" v-html="visionMission.visi.desc"></div>
                </div>
                <div class="hidden h-px bg-line lg:block"></div>
                <div class="flex flex-col gap-3 lg:gap-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">{{ visionMission.misi.title }}</p>
                    <div class="rich-text mission-list text-base leading-relaxed lg:text-lg" v-html="visionMission.misi.desc"></div>
                </div>
            </div>
        </section>

        <!-- Nilai-nilai -->
        <section v-if="values?.length" class="bg-brand-deep text-white">
            <div class="container-site section-y flex flex-col gap-8 lg:gap-16">
                <SectionHeading eyebrow="Yang kami pegang" title="Nilai-nilai kami" tone="dark" />
                <div class="grid border-t border-white/20 lg:grid-cols-4">
                    <div
                        v-for="(value, index) in values"
                        :key="value.title"
                        class="flex flex-col gap-3 border-b border-white/20 py-6 lg:gap-5 lg:border-b-0 lg:py-0 lg:pr-8 lg:pt-10"
                        :class="index > 0 ? 'lg:border-l lg:pl-8' : ''"
                    >
                        <p class="text-xs font-semibold tracking-[0.16em] text-brand-orange lg:text-[13px]">{{ String(index + 1).padStart(2, '0') }}</p>
                        <Icon v-if="value.icon" :icon="value.icon" class="h-9 w-9 text-brand-mint" aria-hidden="true" />
                        <p class="font-serif text-[28px] leading-tight lg:text-[34px]">{{ value.title }}</p>
                        <div class="rich-text text-sm leading-loose text-white/75 lg:text-[15px] [&_a]:text-brand-mint" v-html="value.desc"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pengakuan Iman -->
        <section v-if="beliefItems.length" class="container-site section-y grid gap-8 lg:grid-cols-[5fr_7fr] lg:gap-28">
            <div class="flex flex-col gap-5 lg:gap-6">
                <SectionHeading eyebrow="Pengakuan Iman" :title="beliefs.title || 'Apa yang kami percaya'" />
                <div v-if="beliefs.intro" class="rich-text text-[15px] leading-relaxed text-ink-muted lg:text-[17px]" v-html="beliefs.intro"></div>
                <BaseButton v-if="beliefs.link" :href="beliefs.link" external variant="link" class="self-start">Baca Pengakuan Iman GPdI lengkap →</BaseButton>
            </div>
            <ol class="grid border-t border-line lg:grid-cols-2 lg:gap-x-12">
                <li v-for="(item, index) in beliefItems" :key="item.title" class="flex gap-4 border-b border-line py-5 lg:flex-col lg:gap-2.5 lg:py-7">
                    <span class="w-8 shrink-0 font-serif text-2xl leading-tight text-brand-teal lg:text-[28px]">{{ String(index + 1).padStart(2, '0') }}</span>
                    <div class="flex flex-col gap-1.5 lg:gap-2.5">
                        <p class="font-serif text-2xl leading-tight text-heading lg:text-[28px]">{{ item.title }}</p>
                        <p class="text-sm leading-relaxed text-ink-muted lg:text-[15px]">{{ item.desc }}</p>
                        <p v-if="item.verse" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-brand-teal lg:text-xs">{{ item.verse }}</p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- Tim Pastoral -->
        <section v-if="pastors?.length" id="pastoral" class="bg-mist">
            <div class="container-site section-y flex flex-col gap-8 lg:gap-16">
                <SectionHeading eyebrow="Kepemimpinan" title="Tim Pastoral" />
                <div class="grid grid-cols-2 gap-x-3.5 gap-y-7 lg:grid-cols-4 lg:gap-8">
                    <div v-for="pastor in pastors" :key="pastor.name" class="flex flex-col gap-2 lg:gap-3">
                        <div class="mb-1.5 h-[210px] overflow-hidden bg-brand-mint/40 lg:mb-2 lg:h-[360px]">
                            <img v-if="pastor.image" :src="storageUrl(pastor.image)!" :alt="pastor.name" loading="lazy" class="h-full w-full object-cover" />
                        </div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.16em] text-brand-teal lg:text-xs">{{ pastor.position }}</p>
                        <p class="font-serif text-[22px] leading-tight text-heading lg:text-[32px]">{{ pastor.name }}</p>
                    </div>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
```

- [ ] **Step 3: Build and compare**

Run: `npm run build`
Expected: exits 0.

Manual: open `/about` at 1440px and 390px next to the canvas **About** page. Check:
- There is no history section.
- The Values numbers are orange on deep teal, and the Bible references and links are mint.
- The Pengakuan Iman section only appears once items exist in the admin panel.
- The `/about#pastoral` anchor from the homepage scrolls to Tim Pastoral.

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/AboutUs.vue resources/js/types/about-page.d.ts
git commit -m "feat(about): rebuild Tentang Kami with values, Pengakuan Iman and pastoral team"
```

---

### Task 9: Divisi page (list and detail tabs, mobile accordion)

**Files:**
- Create: `resources/js/Components/site/DivisionTabs.vue`
- Modify: `resources/js/Pages/Division.vue` (full rewrite)

**Interfaces:**
- Consumes: `DivisionItem` (slug from Task 2), plus `PageHero` and `BaseButton`.
- Produces: `<DivisionTabs :divisions="DivisionItem[]">`. It reads and writes `location.hash` (`#{slug}`).

- [ ] **Step 1: Create `resources/js/Components/site/DivisionTabs.vue`**

```vue
<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import type { DivisionItem } from '@/types/site';
import { storageUrl } from '@/lib/site';
import BaseButton from './BaseButton.vue';

const props = defineProps<{ divisions: DivisionItem[] }>();
const selected = ref(0);
const openIndex = ref(0);
const tabs = ref<HTMLButtonElement[]>([]);
const current = computed(() => props.divisions[selected.value]);
const total = computed(() => String(props.divisions.length).padStart(2, '0'));
const num = (i: number) => String(i + 1).padStart(2, '0');

function indexFromHash(): number {
    const slug = decodeURIComponent(window.location.hash.replace(/^#/, ''));
    const found = props.divisions.findIndex((d) => d.slug === slug);
    return found >= 0 ? found : 0;
}

function select(i: number) {
    selected.value = i;
    openIndex.value = i;
    history.replaceState(null, '', `#${props.divisions[i].slug}`);
}

function toggle(i: number) {
    openIndex.value = openIndex.value === i ? -1 : i;
    if (openIndex.value === i) select(i);
}

function onKeydown(event: KeyboardEvent, i: number) {
    const n = props.divisions.length;
    const next = { ArrowDown: (i + 1) % n, ArrowUp: (i - 1 + n) % n, Home: 0, End: n - 1 }[event.key];
    if (next === undefined) return;
    event.preventDefault();
    select(next);
    tabs.value[next]?.focus();
}

onMounted(() => {
    if (!props.divisions.length) return;
    const i = indexFromHash();
    selected.value = i;
    openIndex.value = i;
});
</script>

<template>
    <p v-if="!divisions.length" class="text-ink-muted">Belum ada divisi.</p>

    <!-- Desktop: list + detail -->
    <div v-if="divisions.length" class="hidden gap-[72px] lg:grid lg:grid-cols-[5fr_7fr] lg:items-start">
        <div role="tablist" aria-label="Divisi" aria-orientation="vertical" class="flex flex-col border-t border-line">
            <button
                v-for="(division, i) in divisions"
                :id="`tab-${division.slug}`"
                :key="division.id"
                ref="tabs"
                type="button"
                role="tab"
                :aria-selected="i === selected"
                :aria-controls="`panel-${division.slug}`"
                :tabindex="i === selected ? 0 : -1"
                class="flex min-h-[88px] w-full items-center justify-between gap-4 border-b border-line px-5 py-4 text-left transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand-teal"
                :class="i === selected ? 'bg-mist shadow-[inset_3px_0_0_rgb(var(--color-decor))]' : 'hover:bg-mist/60'"
                @click="select(i)"
                @keydown="onKeydown($event, i)"
            >
                <span class="flex flex-col gap-1">
                    <span class="font-serif text-[28px] leading-[1.05] text-heading">{{ division.title }}</span>
                    <span v-if="division.audience" class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink-muted">{{ division.audience }}</span>
                </span>
                <span v-if="division.short_time" class="text-right text-[13px] font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.short_time }}</span>
            </button>
        </div>
        <div v-if="current" :id="`panel-${current.slug}`" role="tabpanel" :aria-labelledby="`tab-${current.slug}`" tabindex="0" class="flex flex-col gap-5 focus-visible:outline-none">
            <div class="h-[460px] overflow-hidden bg-brand-mint/40">
                <img v-if="current.image" :src="storageUrl(current.image)!" :alt="current.title" class="h-full w-full object-cover" />
            </div>
            <p class="mt-3 text-xs font-semibold uppercase tracking-[0.2em] text-ink-muted">{{ num(selected) }} / {{ total }}<template v-if="current.audience"> · {{ current.audience }}</template></p>
            <h2 class="font-serif text-[56px] leading-[1.02] text-heading">{{ current.title }}</h2>
            <p class="text-base font-semibold" :class="current.time ? 'text-brand-teal' : 'text-ink-muted'">{{ current.time || 'Jadwal akan diumumkan' }}</p>
            <p class="max-w-[560px] text-[17px] leading-relaxed text-ink-muted">{{ current.description }}</p>
            <BaseButton v-if="current.link" :href="current.link" :external="!current.link.startsWith('/')" class="self-start">Selengkapnya →</BaseButton>
        </div>
    </div>

    <!-- Mobile: accordion -->
    <div v-if="divisions.length" class="flex flex-col border-t border-line lg:hidden">
        <div
            v-for="(division, i) in divisions"
            :key="division.id"
            class="border-b border-line"
            :class="openIndex === i ? 'bg-mist shadow-[inset_3px_0_0_rgb(var(--color-decor))]' : ''"
        >
            <h2>
                <button
                    type="button"
                    :aria-expanded="openIndex === i"
                    :aria-controls="`acc-${division.slug}`"
                    class="flex min-h-[76px] w-full items-center justify-between gap-3 px-4 py-3.5 text-left"
                    @click="toggle(i)"
                >
                    <span class="flex flex-col gap-1">
                        <span class="font-serif text-2xl leading-[1.05] text-heading">{{ division.title }}</span>
                        <span v-if="division.audience" class="text-[10px] font-semibold uppercase tracking-[0.18em] text-ink-muted">{{ division.audience }}</span>
                    </span>
                    <span class="flex items-center gap-3">
                        <span v-if="division.short_time" class="text-xs font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.short_time }}</span>
                        <span aria-hidden="true" class="font-serif text-2xl leading-none text-brand-teal">{{ openIndex === i ? '−' : '+' }}</span>
                    </span>
                </button>
            </h2>
            <div v-show="openIndex === i" :id="`acc-${division.slug}`" class="flex flex-col gap-3 px-4 pb-5">
                <div class="h-[220px] overflow-hidden bg-brand-mint/40">
                    <img v-if="division.image" :src="storageUrl(division.image)!" :alt="division.title" loading="lazy" class="h-full w-full object-cover" />
                </div>
                <p class="mt-1 text-sm font-semibold" :class="division.time ? 'text-brand-teal' : 'text-ink-muted'">{{ division.time || 'Jadwal akan diumumkan' }}</p>
                <p class="text-[15px] leading-relaxed text-ink-muted">{{ division.description }}</p>
                <BaseButton v-if="division.link" :href="division.link" :external="!division.link.startsWith('/')">Selengkapnya</BaseButton>
            </div>
        </div>
    </div>
</template>
```

- [ ] **Step 2: Rewrite `resources/js/Pages/Division.vue`**

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import DivisionTabs from '@/Components/site/DivisionTabs.vue';
import type { DivisionItem } from '@/types/site';

defineProps<{ data: DivisionItem[]; hero: { title?: string; image?: string } | [] }>();
</script>

<template>
    <Head title="Divisi" />
    <DefaultLayout>
        <PageHero
            :image="Array.isArray(hero) ? null : hero?.image"
            eyebrow="Divisi"
            :title="(!Array.isArray(hero) && hero?.title) || 'Ibadah untuk setiap generasi'"
        />
        <section class="container-site pb-[72px] pt-10 lg:pb-36 lg:pt-28">
            <DivisionTabs :divisions="data" />
        </section>
    </DefaultLayout>
</template>
```

- [ ] **Step 3: Build**

Run: `npm run build`
Expected: exits 0.

- [ ] **Step 4: Check behaviour manually (Review Focus #3, front half)**

Open `/division` at 1440px:
1. The first division is selected. Press Tab to reach the list, then use ↓/↑/Home/End. Selection and focus move, and the URL hash updates.
2. Open `/division#dots` (for a division titled "DoT's"). That division is selected on load.
3. Open `/division#does-not-exist`. The first division is selected, with no error in the console.
4. A division with an empty time shows "Jadwal akan diumumkan" in grey, and its short time appears only if one is set.

At 390px: the rows open and close, one at a time, and the first is open by default. Compare with the canvas **Divisions** page (press Play to click through).

- [ ] **Step 5: Commit**

```bash
git add resources/js/Components/site/DivisionTabs.vue resources/js/Pages/Division.vue
git commit -m "feat(divisions): list + detail tabs with deep links, accordion on mobile"
```

---

### Task 10: Pelayanan (Ministries) and Kontak (Contact)

**Files:**
- Modify: `resources/js/Pages/Ministry.vue` (full rewrite)
- Modify: `resources/js/Pages/Contact.vue` (full rewrite)

**Interfaces:**
- Consumes: Task 6 exports, the existing `MinistryPageSections` types, the Contact props `hero` and `contactList[]` (`{title, desc, link?}`), and the shared `services`.

- [ ] **Step 1: Rewrite `resources/js/Pages/Ministry.vue`**

```vue
<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import SectionHeading from '@/Components/site/SectionHeading.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { storageUrl } from '@/lib/site';
import type { MinistryPageSections } from '@/types/ministry-page';

const props = defineProps<{ sections: MinistryPageSections }>();
const hero = computed(() => props.sections.hero);
const intro = computed(() => props.sections.intro);
const ministries = computed(() => props.sections.serve?.ministryList ?? []);
const faqs = computed(() => props.sections.faq?.questionsList ?? []);
</script>

<template>
    <Head title="Pelayanan" />
    <DefaultLayout>
        <PageHero :image="hero?.image" eyebrow="Pelayanan" :title="hero?.title || 'Pelayanan di Hebron Family Church'" />

        <section v-if="intro?.title || intro?.desc" class="container-site section-y grid gap-5 lg:grid-cols-[5fr_7fr] lg:gap-28">
            <h2 class="font-serif text-[34px] leading-[1.08] text-heading lg:text-[56px] lg:leading-[1.05]">{{ intro?.title }}</h2>
            <div class="rich-text text-base leading-relaxed text-ink lg:text-[19px]" v-html="intro?.desc"></div>
        </section>

        <section v-if="ministries.length" class="container-site flex flex-col gap-10 pb-16 lg:gap-14 lg:pb-36">
            <div class="border-t border-line pt-10 lg:pt-14">
                <SectionHeading eyebrow="Di mana saya bisa melayani?" :title="sections.serve?.title || 'Wadah pelayanan kami'" />
            </div>
            <div class="grid gap-11 lg:grid-cols-3 lg:gap-x-10 lg:gap-y-[72px]">
                <article v-for="(ministry, index) in ministries" :key="ministry.title" class="flex flex-col gap-2.5 lg:gap-4">
                    <div class="mb-1.5 h-[220px] overflow-hidden bg-brand-mint/40 lg:mb-2 lg:h-[280px]">
                        <img v-if="ministry.image" :src="storageUrl(ministry.image)!" :alt="ministry.title" loading="lazy" class="h-full w-full object-cover" />
                    </div>
                    <p class="text-xs font-semibold tracking-[0.16em] text-brand-teal lg:text-[13px]">{{ String(index + 1).padStart(2, '0') }}</p>
                    <h3 class="font-serif text-[28px] leading-[1.05] text-heading lg:text-[34px]">{{ ministry.title }}</h3>
                    <div class="rich-text text-[15px] leading-relaxed text-ink-muted" v-html="ministry.desc"></div>
                    <BaseButton v-if="ministry.link" :href="ministry.link" external variant="link" class="mt-auto self-start border-t border-line pt-3.5 lg:pt-3.5">Daftar melayani →</BaseButton>
                </article>
            </div>
        </section>

        <section v-if="faqs.length" class="bg-mist">
            <div class="container-site section-y grid gap-6 lg:grid-cols-[5fr_7fr] lg:gap-28">
                <div class="flex flex-col gap-5">
                    <SectionHeading eyebrow="Pertanyaan" :title="sections.faq?.title || 'Pertanyaan yang sering diajukan'" />
                    <BaseButton href="/contact" variant="link" class="self-start">Masih ragu? Hubungi kami</BaseButton>
                </div>
                <div class="flex flex-col border-t border-line-strong">
                    <details v-for="(qa, index) in faqs" :key="qa.question" :open="index === 0" class="group border-b border-line-strong py-5 lg:py-7">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-6 text-base font-semibold text-heading marker:hidden lg:text-[19px] [&::-webkit-details-marker]:hidden">
                            {{ qa.question }}
                            <span aria-hidden="true" class="font-serif text-[26px] leading-none text-brand-teal group-open:hidden lg:text-[32px]">+</span>
                            <span aria-hidden="true" class="hidden font-serif text-[26px] leading-none text-brand-teal group-open:inline lg:text-[32px]">−</span>
                        </summary>
                        <div class="rich-text mt-3 max-w-[620px] text-[15px] leading-relaxed text-ink-muted lg:mt-4 lg:text-base" v-html="qa.answer"></div>
                    </details>
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
```

- [ ] **Step 2: Rewrite `resources/js/Pages/Contact.vue`**

```vue
<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PageHero from '@/Components/site/PageHero.vue';
import BaseButton from '@/Components/site/BaseButton.vue';
import { useSiteSettings } from '@/composables/useSiteSettings';

defineProps<{
    hero: { title?: string; subtitle?: string | null; image?: string | null };
    contactList: { title: string; desc: string; link?: string }[];
}>();

const { address, mapsUrl, services } = useSiteSettings();
const MAP_EMBED =
    'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d9409.156729619113!2d106.62013527573178!3d-6.250669861203338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc76ad63a58b%3A0xb11a5c84cfc0f8db!2sGPdI%20Hebron%20Gading%20Serpong!5e1!3m2!1sen!2sid!4v1754075220198!5m2!1sen!2sid';
</script>

<template>
    <Head title="Kontak" />
    <DefaultLayout>
        <PageHero :image="hero?.image" eyebrow="Kontak & Lokasi" :title="hero?.title || 'Kami senang bertemu denganmu'" :subtitle="hero?.subtitle" />

        <section class="container-site section-y grid gap-11 lg:grid-cols-[5fr_7fr] lg:gap-24">
            <div class="flex flex-col gap-11 lg:gap-14">
                <div v-if="contactList.length" class="flex flex-col gap-3.5 lg:gap-5">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Hubungi Kami</p>
                    <div class="flex flex-col border-t border-line">
                        <div v-for="item in contactList" :key="item.title + item.desc" class="flex min-h-[56px] flex-col justify-center gap-1 border-b border-line py-3 lg:grid lg:grid-cols-[140px_1fr] lg:items-baseline lg:gap-6 lg:py-5">
                            <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-ink-muted lg:text-[13px]">{{ item.title }}</p>
                            <a v-if="item.link" :href="item.link" target="_blank" rel="noopener noreferrer" class="text-base font-medium text-heading hover:text-brand-teal lg:text-[17px]" v-html="item.desc"></a>
                            <div v-else class="text-base font-medium text-heading lg:text-[17px]" v-html="item.desc"></div>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col gap-3.5 lg:gap-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Alamat</p>
                    <div class="rich-text font-serif text-2xl leading-tight text-heading lg:text-[30px]" v-html="address"></div>
                    <BaseButton :href="mapsUrl" external variant="link" class="hidden self-start lg:inline-flex">Buka di Google Maps ↗</BaseButton>
                </div>
                <div v-if="services.length" class="flex flex-col gap-3.5 lg:gap-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-brand-teal lg:text-xs">Ibadah Raya</p>
                    <div v-for="service in services" :key="service.title" class="flex justify-between gap-4 text-[15px] lg:text-base">
                        <span>{{ service.title }}<template v-if="service.livestream"> · Live Streaming</template></span>
                        <span class="font-semibold">{{ service.time }} WIB</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col gap-3.5">
                <iframe :src="MAP_EMBED" title="Peta lokasi GPdI Hebron Gading Serpong" class="h-[260px] w-full border-0 lg:h-full lg:min-h-[720px]" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                <BaseButton :href="mapsUrl" external class="lg:hidden">Buka di Google Maps</BaseButton>
            </div>
        </section>
    </DefaultLayout>
</template>
```

On mobile, the details stack first, followed by the map with its "Buka di Google Maps" button directly below it. That matches `ContactMobile.dc.html`.

- [ ] **Step 3: Build and compare**

Run: `npm run build`
Expected: exits 0.

Manual: compare `/ministry` and `/contact` at both widths against the canvas **Ministries** and **Contact** pages. Also check:
- The first FAQ item is open, and +/− swap when you toggle.
- The contact rows link out when a link is set.
- `/contact` still renders on an empty database (this is covered by the automated test in Task 5).

- [ ] **Step 4: Commit**

```bash
git add resources/js/Pages/Ministry.vue resources/js/Pages/Contact.vue
git commit -m "feat(ministry,contact): rebuild Pelayanan and Kontak pages"
```

---

### Task 11: Renungan (list and post)

**Files:**
- Modify: `resources/js/Pages/DevotionList.vue` (full rewrite)
- Modify: `resources/js/Pages/Devotion.vue` (full rewrite)

**Interfaces:**
- Consumes:
  - The `DevotionList` props `posts: Paginated<DevotionSummary>` and `category: DevotionCategory | null` (Task 4).
  - The `Devotion` props `data: DevotionFull` and `related: DevotionSummary[]` (Task 4).
  - `PostCard` (Task 7), and `CATEGORY_LABELS`, `formatDate`, `readingLabel` and `storageUrl` (Task 6).

- [ ] **Step 1: Rewrite `resources/js/Pages/DevotionList.vue`**

```vue
<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PostCard from '@/Components/site/PostCard.vue';
import type { DevotionCategory, DevotionSummary, Paginated } from '@/types/site';

const props = defineProps<{ posts: Paginated<DevotionSummary>; category: DevotionCategory | null }>();

const tabs: { label: string; value: DevotionCategory | null }[] = [
    { label: 'Semua', value: null },
    { label: 'Renungan', value: 'devotion' },
    { label: 'Berita', value: 'news' },
    { label: 'Pengumuman', value: 'announcement' },
];
const tabHref = (value: DevotionCategory | null) => (value ? `/devotion?category=${value}` : '/devotion');
const featured = computed(() => (props.posts.current_page === 1 ? props.posts.data[0] : null));
const rest = computed(() => (featured.value ? props.posts.data.slice(1) : props.posts.data));
const pageLinks = computed(() => props.posts.links.slice(1, -1));
</script>

<template>
    <Head title="Renungan & Berita" />
    <DefaultLayout>
        <section class="bg-brand-deep pt-16 text-white lg:pt-[88px]">
            <div class="container-site flex flex-col gap-3.5 pt-12 lg:gap-5 lg:pt-24">
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-orange lg:text-[11px]">Renungan &amp; Berita</p>
                <h1 class="font-serif text-5xl leading-none lg:text-[80px]">Terbaru dari Firman</h1>
            </div>
            <nav aria-label="Filter kategori" class="container-site mt-9 flex gap-6 overflow-x-auto border-b border-white/15 lg:mt-16 lg:gap-10">
                <Link
                    v-for="tab in tabs"
                    :key="tab.label"
                    :href="tabHref(tab.value)"
                    :aria-current="category === tab.value ? 'page' : undefined"
                    preserve-scroll
                    class="shrink-0 border-b-2 pb-4 text-[13px] font-semibold tracking-[0.08em] lg:pb-5 lg:text-sm"
                    :class="category === tab.value ? 'border-brand-orange text-white' : 'border-transparent text-white/70 hover:text-white'"
                    >{{ tab.label }}</Link
                >
            </nav>
        </section>

        <p v-if="!posts.data.length" class="container-site section-y text-ink-muted">Belum ada tulisan di kategori ini.</p>

        <section v-if="featured" class="container-site border-b border-line py-10 lg:py-24">
            <PostCard :post="featured" variant="featured" />
        </section>

        <section v-if="rest.length || posts.last_page > 1" class="container-site flex flex-col gap-8 pb-14 pt-4 lg:gap-20 lg:pb-32 lg:pt-24">
            <div class="md:grid md:grid-cols-3 md:gap-x-10 md:gap-y-[72px]">
                <PostCard v-for="post in rest" :key="post.id" :post="post" />
            </div>

            <nav v-if="posts.last_page > 1" aria-label="Halaman" class="hidden items-center justify-center gap-2 text-sm font-semibold md:flex">
                <template v-for="link in pageLinks" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        :aria-current="link.active ? 'page' : undefined"
                        class="flex h-11 w-11 items-center justify-center"
                        :class="link.active ? 'bg-heading text-white' : 'border border-line-strong text-heading hover:border-heading'"
                        >{{ link.label }}</Link
                    >
                    <span v-else class="flex h-11 w-11 items-center justify-center text-ink-muted">{{ link.label }}</span>
                </template>
                <Link v-if="posts.next_page_url" :href="posts.next_page_url" class="flex h-11 items-center gap-2 border border-line-strong px-4 text-heading hover:border-heading">Berikutnya →</Link>
            </nav>
            <Link
                v-if="posts.next_page_url"
                :href="posts.next_page_url"
                class="flex h-[50px] items-center justify-center rounded-button border border-heading text-sm font-semibold text-heading md:hidden"
                >Muat lebih banyak</Link
            >
        </section>
    </DefaultLayout>
</template>
```

- [ ] **Step 2: Rewrite `resources/js/Pages/Devotion.vue`**

```vue
<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/DefaultLayout.vue';
import PostCard from '@/Components/site/PostCard.vue';
import { CATEGORY_LABELS, formatDate, readingLabel, storageUrl } from '@/lib/site';
import type { DevotionFull, DevotionSummary } from '@/types/site';

const props = defineProps<{ data: DevotionFull; related: DevotionSummary[] }>();
const shareUrl = ref('');
const copied = ref(false);

onMounted(() => {
    shareUrl.value = window.location.href;
});

async function copyLink() {
    try {
        await navigator.clipboard.writeText(shareUrl.value);
        copied.value = true;
        setTimeout(() => (copied.value = false), 2000);
    } catch {
        copied.value = false;
    }
}
</script>

<template>
    <Head :title="data.title" />
    <DefaultLayout>
        <section class="bg-brand-deep pb-[120px] pt-16 text-white lg:pb-60 lg:pt-[88px]">
            <div class="container-site flex flex-col gap-4 pt-10 lg:items-center lg:gap-6 lg:pt-24 lg:text-center">
                <Link href="/devotion" class="text-[13px] font-semibold text-white/70 hover:text-white">← Semua renungan</Link>
                <p class="text-[10px] font-semibold uppercase tracking-[0.24em] text-brand-orange lg:text-xs">{{ CATEGORY_LABELS[data.category] }}</p>
                <h1 class="max-w-[960px] font-serif text-[42px] leading-[1.04] lg:text-[76px] lg:leading-[1.02]">{{ data.title }}</h1>
                <p class="text-[13px] text-white/70 lg:text-sm">{{ formatDate(data.created_at) }} · {{ readingLabel(data.reading_minutes) }}</p>
            </div>
        </section>

        <div class="container-site -mt-[88px] lg:-mt-44">
            <div class="h-[240px] overflow-hidden bg-brand-teal lg:h-[600px]">
                <img v-if="data.image" :src="storageUrl(data.image)!" :alt="data.title" class="h-full w-full object-cover" />
            </div>
        </div>

        <article class="mx-auto max-w-[720px] px-5 pb-16 pt-10 lg:px-0 lg:pb-32 lg:pt-24">
            <div
                class="rich-text text-[17px] leading-[1.8] text-ink lg:text-lg [&>p:first-child]:font-serif [&>p:first-child]:text-2xl [&>p:first-child]:leading-snug [&>p:first-child]:text-heading lg:[&>p:first-child]:text-[30px]"
                v-html="data.content"
            ></div>
            <div class="mt-8 flex flex-col gap-3.5 border-t border-line pt-6 lg:flex-row lg:items-center lg:justify-between">
                <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-ink-muted lg:text-[13px]">Bagikan</p>
                <div class="grid grid-cols-3 gap-2 lg:flex">
                    <a :href="`https://wa.me/?text=${encodeURIComponent(`${data.title} ${shareUrl}`)}`" target="_blank" rel="noopener noreferrer" class="flex h-11 items-center justify-center border border-line-strong px-4 text-[13px] font-semibold text-heading">WhatsApp</a>
                    <a :href="`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`" target="_blank" rel="noopener noreferrer" class="flex h-11 items-center justify-center border border-line-strong px-4 text-[13px] font-semibold text-heading">Facebook</a>
                    <button type="button" class="flex h-11 items-center justify-center border border-line-strong bg-white px-4 text-[13px] font-semibold text-heading" @click="copyLink">
                        {{ copied ? 'Tersalin' : 'Salin tautan' }}
                    </button>
                </div>
            </div>
        </article>

        <section v-if="related.length" class="bg-mist">
            <div class="container-site flex flex-col gap-2 py-14 lg:gap-14 lg:pb-32 lg:pt-28">
                <div class="flex items-end justify-between">
                    <h2 class="font-serif text-[34px] leading-[1.05] text-heading lg:text-[52px]">Baca juga</h2>
                    <Link href="/devotion" class="hidden text-[15px] font-semibold text-brand-teal lg:inline">Lihat semua renungan</Link>
                </div>
                <div class="md:grid md:grid-cols-3 md:gap-10">
                    <PostCard v-for="post in related" :key="post.id" :post="post" />
                </div>
            </div>
        </section>
    </DefaultLayout>
</template>
```

- [ ] **Step 3: Build**

Run: `npm run build`
Expected: exits 0.

- [ ] **Step 4: Check manually (Review Focus #2, front half)**

With at least 10 devotions in your local database (for example, `php artisan tinker` → `App\Models\Devotion::factory()->count(12)->create()`), check:
- `/devotion` shows a featured post, 8 grid cards and page numbers.
- `/devotion?page=2` shows no featured post.
- The category tabs filter the list and keep the active tab underlined in orange.
- `/devotion?category=foo` shows "Semua" as active.
- A devotion with no image shows a teal or mint block, not a broken image.
- The post page:
  - The first paragraph is in the serif.
  - Blockquotes use the serif italic with a teal rule.
  - "Salin tautan" changes to "Tersalin".
  - "Baca juga" lists 3 other posts.

Compare with the canvas **Devotions** page.

- [ ] **Step 5: Commit**

```bash
git add resources/js/Pages/DevotionList.vue resources/js/Pages/Devotion.vue
git commit -m "feat(devotions): rebuild Renungan list and post pages"
```

---

### Task 12: Remove the old UI and run the final checks

**Files:**
- Delete: `resources/js/Components/Navbar.vue`, `resources/js/Components/Footer.vue`, `resources/js/Components/DivisionCard.vue`, `resources/js/Components/ServiceCard.vue`, `resources/js/Stores/navigationStore.js`
- Modify: `package.json` and `package-lock.json` (remove `vue3-marquee`)

**Interfaces:**
- Consumes: everything above. After this task, no page imports the deleted files.

- [ ] **Step 1: Confirm nothing references the old files**

Run: `grep -rn "Navbar\|Components/Footer\|Components/DivisionCard\|ServiceCard\|navigationStore\|vue3-marquee\|gsap\|Playfair" resources/js resources/css`
Expected: no matches except inside the files about to be deleted. If a page still matches, fix that page first.

- [ ] **Step 2: Delete the files and the dependency**

```bash
git rm resources/js/Components/Navbar.vue resources/js/Components/Footer.vue resources/js/Components/DivisionCard.vue resources/js/Components/ServiceCard.vue resources/js/Stores/navigationStore.js
npm uninstall vue3-marquee
```

(`gsap` stays installed, because removing it is outside this spec; it is simply no longer imported. If `grep -rn "gsap" resources/js` returns nothing, you may also run `npm uninstall gsap` in this step.)

- [ ] **Step 3: Full verification**

Run: `php artisan test`
Expected: PASS (every test from Tasks 0–5).

Run: `npm run build`
Expected: exits 0.

Run: `grep -rnE "#[0-9A-Fa-f]{6}\b" resources/js/Pages resources/js/Components/site resources/js/Layouts`
Expected: no matches (no hard-coded colours in components).

- [ ] **Step 4: Final manual pass**

At 390px and 1440px, visit `/`, `/about`, `/division`, `/ministry`, `/devotion`, a devotion post and `/contact`, with the keyboard only:
- Every link and button shows a visible focus ring, including card links. If a card link has none, add `focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-brand-teal` to it.
- The skip link "Lewati ke konten" appears on the first Tab.
- The mobile menu traps focus.
- No page shows English UI labels, except the motto and CMS content.

- [ ] **Step 5: Commit**

```bash
git add -A package.json package-lock.json
git commit -m "chore: remove old navbar, footer, cards, navigation store and vue3-marquee"
```
