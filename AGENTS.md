<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

</laravel-boost-guidelines>

---

# Project Rules — هنرستان جوار شرکت پالایش نفت بندرعباس

> The block above (`<laravel-boost-guidelines>`) is generated by Laravel Boost and may be
> regenerated by `php artisan boost:update`. Everything below this line is hand-written
> project policy — never delete or auto-format it.

## 1. Project Overview

- **Type**: public school introduction website (وب‌سایت معرفی هنرستان فنی پسرانه), RTL Persian.
- **Stack**: Laravel 13 (PHP 8.4), Blade server-rendered pages, Tailwind CSS v4, Vite, SQLite/MySQL.
- **Content**: static-seeded school content (majors, grades, achievements, news, gallery, contact).
- **Audience**: students, parents, and prospective registrants; the site must look authoritative,
  modern, and trustworthy — it is the school's public face.

## 2. Architecture

### Laravel 13 layered architecture with proper concerns
Follow Laravel 13 best practices and place logic in its proper concern: Controllers, Models,
Migrations, Observers, Services, Actions, DTOs, Jobs/Queues, Events, Enums, Form Requests,
Notifications, Policies, Providers, Rules, Config, Console commands. No fat controllers and no
misplaced logic.

- Controllers stay thin: validate → delegate to an action/service → return a response.
- Put business rules in Actions/Services, not inside route closures.
- Use Form Requests for validation; Enums for fixed value sets (grade level, major, status).
- Use named routes and `route()` for links. Never hardcode URLs.

### Search docs before deciding
When an API, behavior, or version-specific detail is uncertain, search version-specific docs via
`search-docs` (and web research when needed) before choosing. Never guess at library behavior.

## 3. Frontend & UI/UX — highest priority

### UI/UX quality outranks everything
The frontend must be worked on with extreme care. Best-in-class UI/UX is the top priority of this
project — ahead of speed of delivery, ahead of feature count, ahead of backend convenience.
A page that is functionally correct but visually weak is a defect.

- Every page is designed, not just assembled: clear hierarchy, intentional spacing, consistent
  rhythm, restrained motion (subtle, purposeful, never bouncy).
- No generic "AI look": no purple/violet gradients, no purple-tinted shadows, no generic
  glassmorphism, no emoji-as-icons, no stock gradient blobs, no oversized rounded everything.
- Verify the result in both light and dark mode before calling it done.

### Gradients: use minimally
- Gradient usage must be **rare and purposeful** (at most one subtle accent surface per view).
- **Purple/violet gradients are forbidden.** They read as templated AI output.
- Prefer solid colors, layered surfaces, hairline borders, and tonal steps for depth.

### Typography & RTL
- Persian-first typography: a proper Persian webfont (Vazirmatn), correct line-height (≥1.8 for
  body copy), letter-spacing 0 for Persian text, `dir="rtl"` on `<html>`.
- Use tabular numerals (`font-variant-numeric`) for statistics and phone numbers.
- Numbers, dates, and Latin technical terms stay LTR inside RTL flow (`dir="ltr"` / `bdi`).

### Dark & light mode
- Every UI must fully support both dark and light modes (Tailwind `dark:` variants) with tested
  contrast in both. No light-only or dark-only surface. Dark mode is the default when the OS
  prefers it and can be toggled by the user; the choice is persisted.

### Component reuse
- Maximize Blade component reuse: put shared UI in `resources/views/components/**` and reuse it.
  Never duplicate the same markup inline in several pages.
- Use partials (`@include`) only for page-local fragments; shared UI belongs in components.

### Responsive behavior
- Every layout is mobile-first and verified at ~360px, 768px, and 1440px.
- Tables must always be responsive: horizontal scroll, card layout, or collapsible rows — never
  overflow the viewport. Use proper `<thead>`/`<tbody>` and scoped column classes.

### Performance & perceived speed
- Minimize critical CSS, defer non-critical JS, lazy-load images and below-the-fold content.
- Aim for sub-second initial paint on the home page.
- Non-critical slow sections load via AJAX with skeleton placeholders, then swap in the real
  content without layout shift.

## 4. Blade-first with AJAX-enhanced UX

The project uses Blade for server-side rendering — no Inertia, no SPA framework. All pages are
Blade templates.

- Blade is the source of truth; AJAX is layered on top for responsiveness.
- Use AJAX (fetch/XHR) for: form submissions, data loading, partial updates, dynamic content.
- Validate in JS before submit; show inline Persian errors; keep server-side validation as the
  real guard.
- File uploads use progressive upload with a styled progress bar.
- Slow sections use skeleton placeholders, replaced by AJAX when the data arrives.
- Progressive enhancement: every AJAX feature must still work if JS is disabled (no dead buttons).

## 5. Docker & Build Workflow

### All tooling runs inside Docker containers
The site runs inside Docker containers. Run `artisan`, `composer`, and `npm` commands inside the
appropriate container instead of the host machine.

- Only when the required container is **not available** may a command run at the project root.
- This repository ships `docker-compose.yml`. Use the matching service for every command:
  `docker compose exec app php artisan …`, `docker compose exec app composer …`,
  `docker compose exec app npm run build`. The `node` service (Vite dev server) and the `db`
  service (MySQL) are optional profiles. The project root is only the fallback when the
  containers are not running at all.

### Always rebuild the frontend after any change
After **every** change to the project — PHP, Blade, JS, CSS, config, or any other file — run
`npm run build` inside the Docker container (or at the project root if no container is available)
before finishing the work. This applies even to backend-only changes, because Blade, JS, and CSS
all feed the Vite manifest.

- Never run `npm run dev` as the final state; the production build must be in sync with the code.

### No optimize or cache commands — anywhere
Never run: `php artisan optimize`, `config:cache`, `view:cache`, `route:cache`, `event:cache`,
`icons:cache`, or any other cache/optimize command — in local, staging, or production, inside or
outside Docker. Cached artifacts in Docker are ephemeral and do not reflect filesystem changes.

- Only `clear` variants are permitted: `config:clear`, `view:clear`, `route:clear`, `event:clear`,
  `icon:clear`, etc.
- Never hand-write or commit generated caches (`bootstrap/cache/*`, compiled views, manifest maps).

### Restart containers after config changes
After any config or Horizon supervisor change, restart the relevant container so the new
configuration is picked up.

### ImageMagick is the only image engine
`config/media-library.php` selects `imagick`, and the `app` image installs ImageMagick with the
jpeg, png, gif, webp, tiff and heic/avif delegates plus the PECL `imagick` extension, then asserts
both during the build. PDF input goes through `ghostscript`, which the image installs and re-enables
for reading in ImageMagick's coder policy, with `spatie/pdf-to-image` doing the conversion.
**GD is not installed**, so it must never come back as the driver, as a fallback or as a new
dependency: new image work extends the ImageMagick path instead.

### Continuous integration and releases
`.github/workflows` holds four workflows: `QA` (the test suite on every push and pull request — code
style is not part of it), `OSV-Scanner` (dependency scan), `Auto-assign` (a new issue or pull request
is assigned to the maintainer) and `Docker Release` (manual only: type a version, the run pushes that
tag plus `latest` to GHCR and keeps the newest 8 versions). Never create release tags or push images
by hand.

## 6. Git Workflow

### Session-start git check (mandatory, every session)
Before reading code, planning, or editing, run `git branch --show-current`, `git status --short`,
`git log --oneline -5`, and check pending pushes / open PRs (`git status -sb`,
`git log origin/dev..HEAD` when online). The branch may have changed since the last session, so
never assume the previous branch or a clean tree. Always `git fetch --prune` first — `dev` moves
while you work.

### Branching strategy — one branch per task
- **NEVER push to `dev`, `main`, or `master`.** Pushing to those three branches is forbidden in
  every situation, without exception. They are integration/protected branches, not delivery
  targets.
- **Every task gets its own branch.** Before starting work, inspect the existing branches —
  `git branch -a` and `gh branch list` — and look for a branch that already covers the same
  objective.
  - If a similar branch exists, **continue the work in that branch** (check it out, keep its
    commits) instead of creating a duplicate.
  - If no similar branch exists, create one with a descriptive name:
    `git checkout -b feat/<short-topic>` (also `fix/`, `docs/`, `chore/`, `refactor/`).
- `dev` is the integration branch: work arrives there through a pull request, never by pushing.
- `main`/`master` is protected: no commits, pushes, or direct edits, and it is only updated by
  merging the reviewed `dev` branch.

### Delivery loop for every task (mandatory)
1. Commit the finished work on the task branch — descriptive commits, one task per commit.
2. `git push -u origin <branch>` — the task branch is the only thing that is ever pushed.
3. **Immediately** open a pull request from the task branch into `dev` with the GitHub CLI:
   ```sh
   gh pr create --base dev --head <branch> --title "<type>: <summary>" --body "<description>"
   ```
   The PR title follows Conventional Commits (`feat:`, `fix:`, `docs:`, `chore:`, `refactor:`),
   and the body states what changed, why, and how it was verified.
4. Never leave a PR in a conflicted state: integrate `dev` into the task branch
   (`git merge origin/dev`, or rebase when the history is still local) and resolve the conflicts
   yourself before pushing.
5. One task = one branch = one PR. Do not stack unrelated tasks on the same branch.

### Commit guidelines
- Descriptive commit messages describing the exact change; never vague.
- One task per commit; keep commits small and focused.
- After finishing any code change, inspect `git status`/`git diff` and commit on the task branch
  before pushing. Stage only in-scope files; leave unrelated pre-existing modifications and
  temp/untracked junk uncommitted.

### Destructive git safety
- No irreversible commands without explicit approval: `git checkout -- <path>`, `git restore`,
  `git reset --hard`, `git clean -fd`, `git push --force`, or any equivalent history-rewriting or
  change-discarding command.
- Out-of-scope files are off-limits: if a file has uncommitted changes outside the task scope,
  ask first and do not touch it.
- Ask before any git state change that discards work or alters files outside the task scope.
- `git push --force` and `git push --force-with-lease` are forbidden even on a task branch;
  rewrite local history with `git rebase` before the first push instead.
- Merging is preferred over rebasing while the project has a single developer.

## 7. Artisan & Console Commands

- Use `php artisan make:` commands to create new files (migrations, controllers, models, jobs,
  etc.). List commands with `php artisan list`; check parameters with `php artisan [command] --help`.
- Use `php artisan make:class` for generic PHP classes that are not Laravel artifacts.
- Always pass `--no-interaction` to every Artisan command.
- Inspect routes with `php artisan route:list` (filter with `--method`, `--name`, `--path`,
  `--except-vendor`, `--only-vendor`). Read config with `php artisan config:show` (dot notation).
- Run `vendor/bin/pint --dirty --format agent` after modifying PHP files; never run
  `vendor/bin/pint --test`.

## 8. Configuration

### No container resolution inside config files
Config files load during `LoadConfiguration`, before the container has the `env` binding (and
before providers boot). Never call `app()`, `resolve()`, `app()->isProduction()`, or any other
container binding inside `config/*.php` — it fatals the entire boot (locally
`Target class [env]`, on the server surfacing as `Target class [view]` from the error handler).

- Use `env('APP_ENV', 'production') === 'production'` instead.

### Environment-specific config
Environment checks inside config files use `env('APP_ENV', 'production')`, never container
resolution.

## 9. Database & Performance

### Prevent N+1 queries
Always eager-load relationships with `with()` when accessing related models in loops or
collections. Never iterate a collection and touch a relationship inside the loop — fetch
everything upfront with nested `with()` calls.

### No unnecessary database pressure
- Cache frequently-read, rarely-changed data (`Cache::remember`).
- `select()` only the columns you need; never `Model::all()`/`Model::get()` without a column
  list.
- Use `chunk()`/`lazy()`/`cursor()` for large datasets instead of loading everything into memory.
- Avoid `COUNT(*)` on large tables without indexes; index every column used in `WHERE`,
  `ORDER BY`, and `JOIN`.

### Eager loading is mandatory
Every query that reaches a relationship must declare its eager-loading strategy. If a view
accesses `$course->lessons`, the query must use `Course::with('lessons')`. A missing eager load
is a performance bug, not an oversight.

## 10. Error Messages (Persian, three-part)

### All user-facing error/warning text is Persian
Every string a user can see — error pages, validation messages, flash messages, `abort()` texts,
toasts, inline JS errors — must be Persian (RTL). Never surface English or raw technical text
(`Master playlist not found`, `Invalid path`, exception `getMessage()` output). Log the technical
detail server-side; show the user a Persian sentence.

### Every error/warning answers up to three questions, in order
Plain, calm Persian. No jargon, no error codes in the headline, no blame. Every message MUST
contain parts 1 and 2; add part 3 only when it genuinely matters.

1. **What happened?** — one simple sentence naming the failure in user terms.
   Good: `اتصال به سرور ناموفق بود.` Bad: `خطای 500` / `Request failed`.
2. **What can the user do now?** — one concrete, immediate action.
   Good: `اتصال اینترنت خود را بررسی کنید و از خاموش بودن VPN مطمئن شوید.`
   Good: `صفحه را یک‌بار تازه‌سازی کنید و دوباره تلاش کنید.`
3. **Where to get help?** — ONLY for errors the user cannot resolve alone (payment charge
   unverifiable, account blocked, repeated failure of a critical action). Render as a real link to
   the contact page (`route('contact.index')` in Blade; absolute `/contact` href in JS toasts where
   HTML is allowed). NEVER add part 3 to self-service errors (offline, validation, expired session,
   wrong credentials) — it trains users to ignore the help link.

### Fixed wordings (use exactly)
- **Expired session in AJAX/fetch (HTTP 419):** `خطایی در پردازش رخ داد؛ لطفاً لحظاتی دیگر دوباره تلاش کنید یا صفحه را تازه‌سازی کنید.` — no part 3. If the user was logged out, append:
  `اگر از حساب خارج شده‌اید، دوباره وارد شوید.`
- **Lost connection / network failure:** `اتصال به سرور ناموفق بود. اتصال اینترنت خود را بررسی کنید و از خاموش بودن VPN مطمئن شوید.` — no part 3.
- **Full-page 419 view** offers «تلاش دوباره» and «صفحهٔ اصلی» only. This is a public site with
  no login, so there is no re-login button; and an expired session is a self-service error, so the
  view must **not** offer a contact link.

### Implementation rules
- **Validation:** keep all `messages()`/`attributes()` Persian so `:attribute` never renders a raw
  English field name. Add `attributes()` entries for every new field. Missing `lang/fa` keys
  silently fall back to English — audit new keys in both `lang/fa` and `lang/en`.
- **Controllers/middleware:** wrap failures in Persian flash/abort messages; never pass
  `e->getMessage()` to `with('error')`. Translate English aborts to Persian.
- **JS:** every `fetch`/`XHR` client needs an explicit 419 branch (session toast above, no
  generic error). Replace blocking `alert()` with a toast. Route all `catch` paths to a Persian
  toast; `console.*` alone is not user feedback.
- **Error views:** every page under `resources/views/errors/` extends the shared error layout,
  states what happened + what to do, and links contact support in its action buttons — except the
  full-page 419 view, which offers «تلاش دوباره» and «صفحهٔ اصلی» only (see the fixed wording
  above): an expired session is a self-service error, so it must not carry a contact link.

## 11. Testing

- The project uses Pest. Create tests with `php artisan make:test --pest {name}`; do not
  include the test-suite directory in the name (`SomeFeatureTest`, not `Feature/SomeFeatureTest`).
- Every code change needs a test: cover the changed behavior and its important failure modes,
  and nothing beyond them. Run the affected tests and ensure they pass.
- Use factories, not manual model creation. Check the factory for custom states before
  hand-building models. Use `$this->faker` or `fake()` per existing conventions.
- Read the `testing-best-practices` skill before writing tests.
- Run the narrowest set of tests that covers the change: `php artisan test --compact` with a file
  path or `--filter=testName`; `vendor/bin/pest` accepts the same arguments. Rerun a test after
  each change to it.
- SQLite-backed runs in Docker need `-e DB_DATABASE=:memory:` (plus `APP_ENV=testing`,
  `DB_CONNECTION=sqlite`); otherwise the sqlite path falls back to the MySQL `DB_DATABASE` value
  and a stray sqlite file appears at the repo root.
- After the feature tests pass, ask the user to run the complete suite.
- Never delete tests or test files without approval.

## 12. PDF Export & DomPDF Fonts (when PDF export exists)

### DomPDF font cache belongs in storage, not the repo
`PdfExporter::dompdfOptions()` points `font_dir` at `storage/framework/dompdf-fonts`, so DomPDF
writes its generated files there: a normalized `.ttf` copy per face, the matching `.ufm` Adobe
metrics (required for Persian shaping), and `installed-fonts.json`. None of these are source —
never commit them. `.gitignore` covers them as a safety net; do not hand-write
`installed-fonts.json` to hide them.

### The host font cache must never enter the Docker image
`.dockerignore` excludes `storage/framework/dompdf-fonts/*`. Keep it that way whenever the list
of runtime-generated storage paths changes. `Dockerfile` uses `COPY . .`, and
`installed-fonts.json` stores absolute paths — a host-built map baked into the image makes the
container resolve Windows paths that do not exist there. It fails **silently**:
`Adapter\GD::getCharMap()` calls `file_exists("$font.ufm")`, returns an empty char map on miss,
and Persian glyphs simply vanish rather than raising an error. Each platform builds its own cache
on first render.

### Cache filenames are environment-derived — do not try to pin them
DomPDF names the cache file `family_style_<md5>.ext`, but the hash is **not** of the CSS `src`.
`Stylesheet::_parse_font_face()` resolves the src with `Helpers::build_url()` and hands the
**resolved absolute** `file://` URL to `FontMetrics::registerFont()`, which hashes that. A
relative `src` does not change this. Windows and the container therefore produce different names
for the same font. Do not chase a "stable" filename and do not commit a fixed
`installed-fonts.json` to fix it.

`PdfExporter::pruneForeignFontCache()` is the runtime safety net: it deletes the map when an
entry points outside the configured `font_dir`, letting DomPDF re-register the faces. Two details
it must respect — entries are **extension-less stems** (never probe them with `file_exists()`,
compare the directory instead), and DomPDF mixes bare filenames with absolute ones in the same
file.

### `@font-face` src should stay chroot-relative
Declare faces as `src: url('resources/fonts/Vazirmatn/Vazirmatn-Regular.ttf')`, not
`{{ base_path(...) }}`. It does not change the cache hash, but it keeps the template free of
absolute paths and DomPDF resolves it against `chroot`, which `PdfExporter` sets to `base_path()`.

## 13. Known Framework Constraints

### Horizon has no timezone or localization customization
Horizon offers no `timezone` config option and no i18n framework — the dashboard is a Vue app
with English strings only. Customizing it (e.g. Persian labels, Tehran time) requires
publishing Horizon assets and overriding the Vue components. Keep this in mind before promising
a localized Horizon dashboard.

## 14. Deployment

- Deploy via Laravel Cloud (https://cloud.laravel.com/) — fastest path to production and scaling.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud
  environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.
- No optimize or cache commands in any environment (see §5).
- Restart containers after config changes so the new configuration is picked up.

