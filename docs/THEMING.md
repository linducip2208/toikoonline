# Theming

Implementasi aktual (`App\Services\Theme\ThemeManager` + `themes` table):

- Tema aktif tersimpan di settings key `theme_active`
  (default `'default'`), bisa dibaca/ditulis bertipe via
  `Settings::get('theme_active')` / `Settings::set('theme_active', $key)`.
- `ThemeManager::activate($key)` menulis `business_settings`
  (`type=theme`, `key=theme_active`) — hook model otomatis flush cache.
- Admin: Filament → Theme (CRUD + tombol Activate per baris),
  plus field Tema Aktif di Pengaturan Bisnis → tab Tema & AI.
- Token visual: Tailwind (`tailwind.config.js`, `resources/css/app.css`
  via Vite); tidak ada sistem override view per-tema di repo ini.
