# Panduan CMS

Implementasi aktual di repo ini (Laravel 12 + Filament 3).

## Pengaturan (Settings Engine)

- Penyimpanan tunggal: tabel `business_settings` (`type`, `key`, `value` string).
- Akses lama tetap berlaku: `BusinessSetting::getValue('site_name', 'default')`.
- Akses baru bertipe: `App\Services\Settings\Settings::get('tax_rate_default')`,
  `Settings::set('tax_enabled', true)`, `Settings::group('checkout')`.
- Tipe: `string|int|bool|json|encrypted`. `encrypted` memakai `Crypt`
  (nilai plaintext lama tetap terbaca sebagai fallback).
- Cache: `Cache::remember('settings.key.{key}', 3600)`; di-flush otomatis
  saat `set()` maupun saat model `BusinessSetting` disimpan/dihapus
  (hook `booted()` di model).
- Definisi kunci + default aman-seeder: `SettingDefinition::all()`
  (grup: store, locale, currency, tax, checkout, order, email, seo, theme,
  security, home, ai, social).
- Halaman admin: Filament → Pengaturan Bisnis (`BusinessSettings`),
  tab: Umum, Sosial Media, SMTP Email, Google & Tracking, Invoice & Order,
  Locale & Currency, Pajak & Checkout, Tema & AI, Lainnya.
  Toggle boolean dinormalisasi ke `'1'/'0'` saat simpan.

## Menu

- Tabel `menus`: `location` (header/footer_shop/footer_help/mobile),
  `label`, `url`, `icon`, `parent_id` (submenu rekursif), `sort_order`,
  `badge_text`, `badge_color`, `visibility` (json: `{"auth":true}` /
  `{"guest":true}`), `language_code` (`null` = semua bahasa), `mega_menu`.
- `Menu::forLocation('header')` → hanya aktif + fallback bahasa
  (bahasa X dulu, lalu default) + filter visibilitas user login.
- Relasi: `parent`, `children`, `childrenRecursive`.
- Render 5 baris (nested/mega), di layout masing-masing:

```blade
@foreach(\App\Models\Menu::forLocation('header')->where('parent_id', null) as $m)
  <a href="{{ $m->url }}">{{ $m->label }}</a>
  @if($m->mega_menu) {{-- panel mega: kolom childrenRecursive --}} @endif
  @foreach($m->childrenRecursive as $c) <a href="{{ $c->url }}">{{ $c->label }}</a> @endforeach
@endforeach
```

## Blok Global (reusable)

- Tabel `global_blocks`: `key` unik, `title`, `blocks` json (skema Builder
  sama persis dengan `pages.blocks`), `is_active`.
- Admin: Filament → Blok Global (buat/edit + Builder isi).
- Pakai di Page Builder: tambah blok **"Blok Global (reusable)"** → pilih key.
- Render: partial `storefront/partials/page-blocks.blade.php` cabang `global`
  mengambil blok aktif by key lalu me-render inner blocks-nya; referensi
  hilang/nonaktif = tidak tampil; blok `global` di dalam global diabaikan
  (maks 1 level, anti-rekursi).

## Blog

- Kolom: `category_id`, `user_id` (penulis via relasi `user`,
  tampil via `$blog->authorName()`), `tags` (json, input TagsInput),
  `is_published` + `published_at` (jadwal: `null` = langsung tampil).
- Scope: `published()` (flag saja, tidak diubah) dan `visible()`
  (flag + `published_at <= now`).
- Related posts & arsip (tanpa ubah controller — integrator yang pasang):

```php
$related  = \App\Services\Content\RelatedService::relatedPosts($blog, 4);
$archives = \App\Services\Content\RelatedService::archives(); // [['year','month','count']]
```

## Template Email/SMS Multilingual

- Kolom `locale` (`id` default) di `email_templates` & `sms_templates`;
  form admin + kolom tabel locale.
- Render: `TemplateRenderer::render($identifier, $locale, $vars, $safeKeys)`
  → `[$subject, $body]`. Placeholder `{{var}}`; tanpa eval; var tak dikenal
  dibiarkan utuh; nilai di-escape HTML kecuali key di `$safeKeys`.
- Integrasi mailer (milik Agent 3 — tempel di call site):

```php
[$subject, $body] = \App\Services\Content\TemplateRenderer::render(
    'order_created', app()->getLocale(), ['name' => $user->name, 'code' => $order->code]
);
```

Lookup: locale diminta → `id` → baris mana pun (identifier sama).

## Media

- Aturan `App\Rules\SafeUpload`: tolak ekstensi berbahaya
  (php/exe/sh/bat/dll/js/html… termasuk ekstensi ganda `foto.jpg.php`),
  allowlist MIME gambar/video/audio/dokumen/arsip, tolak SVG berisi
  `<script>`/event-handler, tolak path traversal (`..`).
- Terpasang di: PageResource (semua FileUpload `cms-pages` + `meta_image`),
  UploadResource (`cms-media`), GlobalBlockResource.
- Satu baris untuk resource lain:

```php
->acceptedFileTypes([...])->maxSize(10240)->rules([new \App\Rules\SafeUpload])
```

- Semua FileUpload memakai `directory('...')` (terkurung direktori disk
  `public`, tanpa path absolut dari input user).
