# Hebron Family Church — Website Redesign

**Date:** 2026-09-29
**Status:** Approved design, ready for implementation plan
**Design canvas:** https://claude.ai/artifact/1xWRNWzBBX3nkRozWYiTew (desktop 1440px + mobile 390px boards for every page)

## 1. Goal

Replace the current layout, which is messy and inconsistent, with a design that is formal and elegant yet modern. Headings use a serif and body text uses a sans-serif. The site is built around the church motto, *"United as One Family to Live like Christ"*.

The design takes cues from jpcc.org, passioncitychurch.com, vouschurch.com and bethel.com:

- a cinematic full-bleed hero
- small uppercase labels above section titles
- generous whitespace
- a restrained palette with one accent
- alternating light and dark sections

Success means:

- Every public page follows one visual system.
- The page reads well at 390px and at 1440px.
- All existing admin-panel content still renders.
- The church can change brand colours and the heading font later without touching components.

## 2. Scope

**In scope:**

- Public pages:
  - Home (`/`)
  - Tentang Kami (`/about`)
  - Divisi (`/division`)
  - Pelayanan (`/ministry`)
  - Renungan list (`/devotion`)
  - Renungan post (`/devotion/{slug}`)
  - Kontak (`/contact`)
- Shared layout: header, mobile menu and footer.
- Small admin-panel (Filament) additions needed by the new design (§7).

**Out of scope:**

- Auth pages and the Filament admin UI styling.
- A multi-language (ID/EN) switch.
- A contact form.
- A newsletter.
- Search.
- Changing the stack. It stays Laravel, Inertia, Vue 3 and Filament.

## 3. Language

All UI labels are **Indonesian**. Content stays in whatever language editors write in the admin panel. The motto stays in English because it is the church's official motto. There is no i18n library; labels are written directly in the Vue templates (§8 lists them).

Times use 24-hour WIB, as the live site does (`07.30 WIB`).

## 4. Approach

The site is restyled in place. The routes, controllers, models and Filament pages all stay. The Vue pages and layout components are rewritten on top of a small set of shared presentational components and design tokens.

Rebuilding on a new framework was rejected. It would cost weeks and give no benefit, and it would force a content migration.

## 5. Design tokens

### 5.1 Colours are CSS variables (not final)

The brand colours are **not final**, so every colour is a CSS custom property defined once. Components never hard-code hex values.

In `resources/css/app.css`, inside `@layer base`, space-separated RGB channels keep Tailwind's opacity modifiers working:

```css
:root {
  /* Brand — change here only (palette v2: teal / cream / coral / orange / mint) */
  --brand-deep: 23 63 85;        /* #173F55  headings, dark bands (deepened teal) */
  --brand-deeper: 16 48 63;      /* #10303F  footer */
  --brand-teal: 39 97 127;       /* #27617F  primary — links, labels, buttons */
  --brand-mint: 131 204 197;     /* #83CCC5  highlights on dark only */
  --brand-orange: 249 150 11;    /* #F9960B  accent — small labels and CTA fill on deep/deeper only */
  --brand-coral: 250 90 35;      /* #FA5A23  decorative only — rules, dots, active underline */
  --brand-cream: 248 246 234;    /* #F8F6EA  warm off-white */

  /* Neutrals */
  --ink: 22 48 61;               /* #16303D  body text */
  --ink-muted: 82 99 110;        /* #52636E  secondary text (≥4.5:1 on white and cream) */
  --mist: var(--brand-cream);    /*          alternate section background */
  --line: 233 229 214;           /* #E9E5D6  hairlines */
  --line-strong: 217 211 191;    /* #D9D3BF  borders on cream */
  --surface: 255 255 255;

  /* Role aliases — components use these, not the brand names */
  --color-accent: var(--brand-orange);
  --color-decor: var(--brand-coral);
  --color-primary: var(--brand-teal);
  --color-heading: var(--brand-deep);

  /* Type */
  --font-serif: 'Instrument Serif', 'Times New Roman', serif;
  --font-sans: 'Montserrat', system-ui, sans-serif;

  /* Shape */
  --radius-button: 2px;
}
```

In `tailwind.config.js`, map the variables under `theme.extend.colors`, for example `brand: { deep: 'rgb(var(--brand-deep) / <alpha-value>)', … }` plus `ink`, `mist`, `line`, `accent`, `decor`, `primary`, `heading`. Point `fontFamily.serif` and `fontFamily.sans` at `var(--font-serif)` and `var(--font-sans)`.

Keep the existing shadcn HSL variables. They are only used by the `ui/` primitives (accordion, card). Once pages stop using those primitives, they can be removed in a later cleanup.

Contrast rules (checked against the palette above):

- Orange and mint text appear only on `--brand-deep` or `--brand-deeper` (orange 5.0:1, mint 6.1:1 on deep). On `--brand-teal` bands, use cream or white text instead; orange is only 3.0:1 there.
- Coral never carries text. It fails 4.5:1 on every background in the palette, so use it only for rules, dots, underlines and shapes.
- Teal (`--color-primary`) is safe for text on white and cream (about 6.8:1).
- The CTA on dark backgrounds is an orange fill with `--brand-deep` text.
- Accent colour means `--color-accent`, so swapping the accent is a one-line change.

### 5.2 Typography

- **Headings:** `--font-serif`, which is currently **Instrument Serif** (weight 400, italic available), loaded from Google Fonts.
  - The target font is *Times New Roman MT Condensed*, which is waiting for a Monotype **web** licence.
  - Once the licence is bought, self-host the `.woff2` with `@font-face` and change only `--font-serif`.
- **Body:** Montserrat 400/500/600. It is already loaded. Remove the Playfair Display import.

| Style | Desktop | Mobile | Notes |
|---|---|---|---|
| Display (page H1) | 72px / 1.0 | 46px | Home hero: 60px desktop / 44px mobile |
| Section H2 | 56–64px / 1.02 | 36–42px | |
| Card title | 28–38px / 1.05 | 22–30px | |
| Eyebrow | 12px, 600, `tracking-[0.2em]`, uppercase | 11px | Primary blue on light backgrounds, accent on dark |
| Body | 17–19px / 1.65 | 15–16px | Max measure about 70ch |
| Article body | 18px / 1.8, 720px column | 17px | |

### 5.3 Layout

- Content width is 1200px, with 120px side padding at desktop and 20px on mobile.
- Section vertical padding is about 128–144px on desktop and 56–72px on mobile.
- Buttons are square-cornered (`--radius-button: 2px`) and at least 44px tall.
  - Primary: deep-teal fill.
  - Secondary: deep-teal outline.
  - Text link: blue with an arrow icon.

## 6. Components

New components go in `resources/js/Components/site/`. Each one is presentational and receives data via props.

| Component | Purpose |
|---|---|
| `SiteHeader.vue` | Transparent overlay header. Nav links sit left and right of a centred logo; the "Kunjungi Kami" outline button is at the right. The active page gets a coral underline. It turns solid deep teal after scrolling. Below `lg` it shows the logo and a menu button. |
| `MobileMenu.vue` | Full-screen deep-teal overlay with the links in large serif type. It also shows the Sunday times and a "Kunjungi Ibadah Kami" button. It closes on Esc or a link click and traps focus while open. |
| `SiteFooter.vue` | Deeper-teal footer: logo, motto in serif italic, Jelajahi / Terhubung / Kunjungi columns, and a copyright row ("© {year} GPdI Hebron Gading Serpong. Hak cipta dilindungi." · "GPdI Hebron Production Team w/ 34 Creative Labs"). Data comes from `generalSettings`. |
| `PageHero.vue` | Full-bleed image hero: eyebrow, H1 and optional subtitle at the bottom-left. Props: `image`, `video?`, `eyebrow`, `title`, `subtitle?`, `height` (`full` for home, `page` for about 560–640px). |
| `SectionHeading.vue` | Eyebrow with a short rule, plus an H2. Props: `eyebrow`, `title`, `align`, `tone` (`light`/`dark`). An optional slot on the right holds actions. |
| `BaseButton.vue` | `variant: primary \| outline \| outline-light \| light \| link`, rendered as `<a>` or `<button>`. |
| `DivisionCard.vue` (rewrite) | Two variants. `compact` is a carousel card with audience, name and time. `DivisionTabs.vue` is a separate component that renders the Divisi page's list + detail / accordion (below). When `time` is empty it shows the "Jadwal akan diumumkan" state. |
| `PostCard.vue` | Devotion card. Variants: `grid`, `featured`, `row` (the mobile thumbnail row). |

`DefaultLayout.vue` renders `SiteHeader`, the slot and `SiteFooter`. It drops the `font-serif` default on `<main>`, because body text is sans and headings opt into serif.

Remove:

- `Navbar.vue`, `Footer.vue` and `ServiceCard.vue` once they are unused.
- The `vue3-marquee` dependency.
- The GSAP scroll animations on the home and about pages.

## 7. Data and backend changes

All the new fields are optional, so the site keeps rendering while editors fill them in.

1. **Divisions**
   - A migration adds `audience` (string, nullable), `short_time` (string, nullable, e.g. "Sab 13.30") and `sort` (unsigned int, default 0).
   - The Filament `DivisionResource` gets an Audience input and `->reorderable('sort')`.
   - `Division::orderBy('sort')` is used everywhere divisions are listed.
   - `time` stays free text. An empty value renders "Jadwal akan diumumkan".
2. **Home schedule:** each service in the `scheduleData.services` repeater gets a `livestream` toggle (boolean). A service with the flag shows the "Live Streaming" badge.
3. **Home hero:**
   - Add an optional `heroData.video` upload (mp4/webm, stored in `home-page-hero`).
   - `heroData.image` becomes the poster and fallback.
   - The hero title stays editable; its placeholder and default is the motto.
4. **General settings:** add `maps_url` (the Google Maps link for "Petunjuk arah" and "Buka di Google Maps") and `livestream_url` (for "Tonton Online").
5. **HomeController:** also pass `divisions` (ordered by `sort`) and `latestDevotions` (the 3 most recent, with `snippet`).
6. **DevotionController@all:**
   - Accept an optional `?category=devotion|news|announcement` filter.
   - Return `Devotion::latest()->paginate(9)` with `snippet`.
   - The first item on page 1 renders as the featured post.
   - An invalid category falls back to "Semua".
7. **DevotionController@single:** also pass `related`, the 3 most recent other posts, for "Baca juga".
8. **About page, beliefs section:** add a Filament section `beliefs` to `AboutPage`:
   - `beliefsData.title`, `beliefsData.intro` (rich text) and `beliefsData.link`.
   - A repeater `beliefsData.items` of `{title, desc, verse}`, orderable.
   - `AboutController` passes it as `beliefs`.
9. **Categories:** the existing values `devotion`, `news` and `announcement` map to the labels Renungan, Berita and Pengumuman.

## 8. Pages

Section order follows the canvas. Unless noted, every item comes from the existing admin data.

**Home**
1. Full-screen hero:
   - Video or image with a 28% deep-teal scrim, and the header overlaid.
   - Eyebrow "GPdI Hebron · Gading Serpong".
   - H1 from the hero title.
   - "Kunjungi Ibadah Kami" (links to Kontak) and "Tonton Online" (links to `livestream_url`).
   - A pause button, 44px, with the aria-label "Jeda video latar".
2. Deep-teal "Ibadah Raya" band: one column per service, showing the title, time + "WIB", and the livestream badge.
3. Divisi carousel:
   - Eyebrow "{n} Divisi", heading "Ibadah untuk setiap generasi".
   - Prev/next buttons with a progress bar.
   - 4 cards visible, with the 5th partly cut off.
   - Mobile uses horizontal scroll-snap.
4. Visi & Misi.
5. Sambutan gembala: photo, quote, name and "Gembala Sidang", plus "Lihat tim pastoral Hebron", which links to `/about#pastoral`.
6. "Momen sebagai satu keluarga": a 4-photo grid using the first 4 carousel images.
7. "Temukan tempatmu di keluarga Hebron": three static cards, 01 Pelayanan, 02 Renungan and 03 Hubungi Kami.
8. "Terbaru dari Firman": the 3 latest devotions. The section is hidden when there are none.
9. Blue "Mari beribadah bersama kami" band: address, Sunday times, "Petunjuk arah" and "Hubungi kami".

**Tentang Kami:**

- Hero.
- Visi & Misi beside an arch-topped image.
- Values on deep teal: 4 columns, each showing a number, name, Bible reference and points rendered from the rich text.
- **Pengakuan Iman ("Apa yang kami percaya")**, which replaces the history timeline:
  - The title, an intro paragraph and a "Baca Pengakuan Iman GPdI lengkap" link sit on the left.
  - A 2-column list of numbered belief items (title, one-sentence summary, verse reference) sits on the right. It is a single column on mobile.
  - The section is hidden when there are no items.
  - The History section is no longer rendered. Its admin data is kept but unused.
- Tim Pastoral grid (`#pastoral`).

**Divisi:**

- Hero.
- **List + detail layout (desktop, `lg` and up):**
  - A 5/7 grid.
  - Left is a vertical tab list of every division. Each row shows the name (serif), the audience, and a short time on the right, e.g. "Sab 13.30" or "TBA".
  - The selected row gets a cream background and a coral left bar.
  - Right is the detail panel for the selected division: image, `{n} / {total} · AUDIENCE`, name, full time or "Jadwal akan diumumkan", description, and a "Selengkapnya" button (to `link`).
  - It follows the WAI-ARIA tabs pattern: `role="tablist"` with `aria-orientation="vertical"`, `role="tab"` / `aria-selected`, `role="tabpanel"`, and Up/Down/Home/End keys to move between tabs.
  - The first division is selected by default.
  - The URL hash (`#seeds-of-truth`, from a slug of the title) selects a division, so the homepage cards can deep-link to it.
- **Accordion (below `lg`):**
  - The same list, where each row is a `<button aria-expanded>` that opens that division's details inline (image, time, description, button).
  - One item is open at a time, and the first is open by default.
- **Short time label:** comes from a new optional `short_time` field. If it is empty, show nothing on the list row (the full `time` still shows in the detail).

**Pelayanan:**

- Hero.
- Intro, with the title on the left and the paragraph on the right.
- "Wadah pelayanan kami": a 3-column grid of cards (image, number, title, description as rich text, "Daftar melayani" linking to `link`).
- FAQ using native `<details>`/`<summary>`, with the first item open.

**Renungan (list):**

- Deep-teal header band with the category tabs Semua / Renungan / Berita / Pengumuman. The tabs are links, not client-side state, so the filter works without JavaScript.
- The featured post.
- A 3-column grid.
- Pagination. Mobile uses a "Muat lebih banyak" link to the next page.

**Renungan (post):**

- Deep-teal title band with "← Semua renungan", the category, the title, and the date + "{n} menit baca".
- The featured image overlaps the band.
- A 720px article column with typography styles for `p`, `h2`, `blockquote` (serif italic with a blue left rule) and `cite`.
- Share buttons: WhatsApp, Facebook, and "Salin tautan" using the Clipboard API.
- "Baca juga".

**Kontak:**

- Hero.
- Left column:
  - Contact list (label and value, linked when a `link` exists).
  - Address in the serif.
  - "Buka di Google Maps".
  - Ibadah Raya times.
- Right column: the existing Google Maps iframe. On mobile the map sits above the button.

### UI label reference (Indonesian)

| Context | Label |
|---|---|
| Nav | Tentang Kami · Divisi · Pelayanan · Renungan · Kontak |
| Header CTA / hero CTA | Kunjungi Kami / Kunjungi Ibadah Kami |
| Watch | Tonton Online |
| Services | Ibadah Raya · Ibadah Raya 1/2/3 · Live Streaming · Setiap Minggu |
| Generic | Selengkapnya · Lihat semua … · Hubungi kami · Petunjuk arah · Alamat |
| Division empty time | Jadwal akan diumumkan |
| Devotions | Renungan & Berita · Terbaru dari Firman · Semua / Renungan / Berita / Pengumuman · Baca renungan · Baca juga · Bagikan · Salin tautan · Muat lebih banyak · {n} menit baca |
| Ministries | Di mana saya bisa melayani? · Wadah pelayanan kami · Daftar melayani · Pertanyaan yang sering diajukan · Masih ragu? Hubungi kami |
| Footer | Jelajahi · Terhubung · Kunjungi · Hak cipta dilindungi. |
| ARIA | Buka menu · Tutup menu · Jeda video latar / Putar video latar · Divisi sebelumnya / berikutnya |

## 9. Accessibility and behaviour

- Colour contrast is at least 4.5:1 for body text and at least 3:1 for text 24px and larger. The tokens in §5.1 already meet this in their assigned roles.
- The hero video is `muted playsinline loop`. It autoplays only when `prefers-reduced-motion: no-preference`; otherwise the poster image shows. The pause/play toggle is always present when a video exists.
- Every interactive element is a real `<a>` or `<button>`, with a visible focus ring (2px accent or blue outline, offset 2px). Touch targets are at least 44px.
- The mobile menu uses a focus trap, closes on Esc, and restores focus to the menu button.
- Images get `alt` text from the title fields where available and `alt=""` when decorative. Everything below the fold uses `loading="lazy"`.
- `<html lang="id">`.

## 10. Error handling and empty states

- Missing admin data renders nothing for that section rather than broken UI. For example, the devotion section is hidden when there are no posts, and the gallery is hidden when there are no carousel images.
- A missing hero image falls back to a solid deep-teal background, and text stays legible.
- An unknown devotion slug returns 404, as today. An unknown category returns the unfiltered list.

## 11. Testing

- **Pest feature tests:**
  - `/` renders with `divisions` ordered by `sort` and at most 3 `latestDevotions`.
  - `/devotion?category=news` returns only news, paginated at 9.
  - An invalid category returns all posts.
  - `/devotion/{slug}` includes `related`, which excludes the current post.
  - `/division` is ordered by `sort`.
- `npm run build`, which runs `vue-tsc`, passes.
- **Manual visual check** of each page at 390px and 1440px against the canvas:
  - keyboard-only navigation of the header and mobile menu
  - reduced-motion behaviour of the hero

## 12. Content tasks for the church (outside the code)

- **Divisions:** enter all 7, with their audience and times.

  | Division | Audience | Time |
  |---|---|---|
  | Seeds of Truth | Anak | Minggu 10.00 |
  | Descendants of Truth | Remaja | Sabtu 13.30 — the live admin data says 14.00, so it needs correcting |
  | Speaker of Truth | Mahasiswa | Sabtu 16.30 |
  | Pillars of Truth | Profesional Muda | Minggu dua pekan sekali 14.00 |
  | Family of Christ Community | Keluarga Muda | TBA |
  | Men of God | Kaum Pria | TBA |
  | Women of God | Kaum Wanita | TBA |

- **Values:** fix the Evangelism value's points. They currently duplicate Ministry's.
- **Beliefs:** write the Pengakuan Iman items (title, one-sentence summary, verse) and the intro sentence. The pastor should confirm the wording. Add the link to the full GPdI confession.
- **New fields:** add the hero video (optional), `maps_url` and `livestream_url`, and set the livestream flag on Ibadah Raya 2.
- **Photos:** upload photography for the heroes, divisions and ministries.
- **Font:** buy a Monotype web licence for Times New Roman MT Condensed if it is still wanted.
