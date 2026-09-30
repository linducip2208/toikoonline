<?php

namespace App\Services\Settings;

/**
 * Registry of known settings keys: group, type, default value and validation rules.
 *
 * Types: string | int | bool | json | encrypted
 * Groups: store, locale, currency, tax, checkout, order, email, seo, theme, security, home, ai
 */
class SettingDefinition
{
    /**
     * @return array<string, array{group: string, type: string, default: mixed, label: string, rules?: string|array}>
     */
    public static function all(): array
    {
        return [
            // ---- store ----
            'site_name' => ['group' => 'store', 'type' => 'string', 'default' => 'TokoOnline', 'label' => 'Nama Situs', 'rules' => 'required|string|max:255'],
            'site_description' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Deskripsi Situs', 'rules' => 'nullable|string|max:1000'],
            'contact_email' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Email Kontak', 'rules' => 'nullable|email|max:255'],
            'contact_phone' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Telepon', 'rules' => 'nullable|string|max:50'],
            'address' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Alamat', 'rules' => 'nullable|string|max:1000'],
            'maintenance_mode' => ['group' => 'store', 'type' => 'bool', 'default' => false, 'label' => 'Mode Maintenance', 'rules' => 'boolean'],

            // ---- locale ----
            'default_language' => ['group' => 'locale', 'type' => 'string', 'default' => 'id', 'label' => 'Bahasa Default', 'rules' => 'required|string|size:2'],
            'translation_default' => ['group' => 'locale', 'type' => 'string', 'default' => 'id', 'label' => 'Locale Default (translation)', 'rules' => 'nullable|string|size:2'],
            'translation_fallback' => ['group' => 'locale', 'type' => 'string', 'default' => 'en', 'label' => 'Locale Fallback', 'rules' => 'nullable|string|size:2'],

            // ---- currency ----
            'currency_code' => ['group' => 'currency', 'type' => 'string', 'default' => 'IDR', 'label' => 'Kode Mata Uang', 'rules' => 'required|string|size:3'],
            'currency_symbol' => ['group' => 'currency', 'type' => 'string', 'default' => 'Rp', 'label' => 'Simbol Mata Uang', 'rules' => 'required|string|max:10'],
            'currency_position' => ['group' => 'currency', 'type' => 'string', 'default' => 'before', 'label' => 'Posisi Simbol', 'rules' => 'required|in:before,after'],

            // ---- tax ----
            'tax_enabled' => ['group' => 'tax', 'type' => 'bool', 'default' => false, 'label' => 'Pajak Aktif', 'rules' => 'boolean'],
            'tax_rate_default' => ['group' => 'tax', 'type' => 'int', 'default' => 0, 'label' => 'Tarif Pajak Default (%)', 'rules' => 'nullable|integer|min:0|max:100'],
            'tax_included' => ['group' => 'tax', 'type' => 'bool', 'default' => false, 'label' => 'Harga Sudah Termasuk Pajak', 'rules' => 'boolean'],

            // ---- checkout ----
            'checkout_guest_enabled' => ['group' => 'checkout', 'type' => 'bool', 'default' => true, 'label' => 'Checkout Tamu Diizinkan', 'rules' => 'boolean'],
            'checkout_min_order' => ['group' => 'checkout', 'type' => 'int', 'default' => 0, 'label' => 'Minimal Belanja (IDR)', 'rules' => 'nullable|integer|min:0'],
            'checkout_notes_enabled' => ['group' => 'checkout', 'type' => 'bool', 'default' => true, 'label' => 'Catatan Pembeli Aktif', 'rules' => 'boolean'],
            'warehouse_city_id' => ['group' => 'checkout', 'type' => 'string', 'default' => '', 'label' => 'Kota Gudang (ID)', 'rules' => 'nullable|string|max:50'],
            'warehouse_area_id' => ['group' => 'checkout', 'type' => 'string', 'default' => '', 'label' => 'Area Gudang (ID)', 'rules' => 'nullable|string|max:50'],

            // ---- order ----
            'order_code_prefix' => ['group' => 'order', 'type' => 'string', 'default' => 'ORD', 'label' => 'Prefix Kode Order', 'rules' => 'required|string|max:20'],
            'invoice_prefix' => ['group' => 'order', 'type' => 'string', 'default' => 'INV', 'label' => 'Prefix Invoice', 'rules' => 'required|string|max:20'],
            'invoice_footer' => ['group' => 'order', 'type' => 'string', 'default' => '', 'label' => 'Footer Invoice', 'rules' => 'nullable|string|max:2000'],

            // ---- email ----
            'smtp_host' => ['group' => 'email', 'type' => 'string', 'default' => '', 'label' => 'SMTP Host', 'rules' => 'nullable|string|max:255'],
            'smtp_port' => ['group' => 'email', 'type' => 'int', 'default' => 587, 'label' => 'SMTP Port', 'rules' => 'nullable|integer|min:1|max:65535'],
            'smtp_user' => ['group' => 'email', 'type' => 'string', 'default' => '', 'label' => 'SMTP Username', 'rules' => 'nullable|string|max:255'],
            'smtp_pass' => ['group' => 'email', 'type' => 'encrypted', 'default' => '', 'label' => 'SMTP Password', 'rules' => 'nullable|string|max:500'],
            'smtp_encryption' => ['group' => 'email', 'type' => 'string', 'default' => 'tls', 'label' => 'Enkripsi SMTP', 'rules' => 'nullable|in:tls,ssl,none'],
            'mail_from_address' => ['group' => 'email', 'type' => 'string', 'default' => '', 'label' => 'From Address', 'rules' => 'nullable|email|max:255'],
            'mail_from_name' => ['group' => 'email', 'type' => 'string', 'default' => '', 'label' => 'From Name', 'rules' => 'nullable|string|max:255'],

            // ---- seo ----
            'seo_meta_title' => ['group' => 'seo', 'type' => 'string', 'default' => '', 'label' => 'Meta Title Default', 'rules' => 'nullable|string|max:255'],
            'seo_meta_description' => ['group' => 'seo', 'type' => 'string', 'default' => '', 'label' => 'Meta Description Default', 'rules' => 'nullable|string|max:1000'],
            'google_analytics' => ['group' => 'seo', 'type' => 'string', 'default' => '', 'label' => 'Google Analytics ID', 'rules' => 'nullable|string|max:500'],
            'facebook_pixel' => ['group' => 'seo', 'type' => 'string', 'default' => '', 'label' => 'Facebook Pixel ID', 'rules' => 'nullable|string|max:2000'],
            'google_maps_api' => ['group' => 'seo', 'type' => 'string', 'default' => '', 'label' => 'Google Maps API Key', 'rules' => 'nullable|string|max:500'],

            // ---- theme ----
            'theme_active' => ['group' => 'theme', 'type' => 'string', 'default' => 'default', 'label' => 'Tema Aktif', 'rules' => 'required|string|max:60'],

            // ---- security ----
            'google_recaptcha_site' => ['group' => 'security', 'type' => 'string', 'default' => '', 'label' => 'reCAPTCHA Site Key', 'rules' => 'nullable|string|max:500'],
            'google_recaptcha_secret' => ['group' => 'security', 'type' => 'encrypted', 'default' => '', 'label' => 'reCAPTCHA Secret Key', 'rules' => 'nullable|string|max:500'],

            // ---- home (storefront section toggles, type=storefront in DB) ----
            'best_selling' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Best Selling', 'rules' => 'boolean'],
            'coupon_system' => ['group' => 'home', 'type' => 'bool', 'default' => false, 'label' => 'Coupon System', 'rules' => 'boolean'],
            'flash_deal' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Flash Deal', 'rules' => 'boolean'],
            'todays_deal' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => "Today's Deal", 'rules' => 'boolean'],
            'featured_products' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Featured Products', 'rules' => 'boolean'],
            'featured_categories' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Featured Categories', 'rules' => 'boolean'],
            'newsletter' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Newsletter', 'rules' => 'boolean'],
            'top_brands' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Top Brands', 'rules' => 'boolean'],
            'new_products' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'New Products', 'rules' => 'boolean'],
            'home_banner1' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Home Banner 1', 'rules' => 'boolean'],
            'home_banner2' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Home Banner 2', 'rules' => 'boolean'],
            'home_banner3' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Home Banner 3', 'rules' => 'boolean'],
            'category_products' => ['group' => 'home', 'type' => 'bool', 'default' => true, 'label' => 'Category Products', 'rules' => 'boolean'],

            // ---- social (stored under type=social, kept here for group listing) ----
            'facebook' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Facebook URL', 'rules' => 'nullable|url|max:500'],
            'instagram' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Instagram URL', 'rules' => 'nullable|url|max:500'],
            'twitter' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'Twitter URL', 'rules' => 'nullable|url|max:500'],
            'tiktok' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'TikTok URL', 'rules' => 'nullable|url|max:500'],
            'youtube' => ['group' => 'store', 'type' => 'string', 'default' => '', 'label' => 'YouTube URL', 'rules' => 'nullable|url|max:500'],

            // ---- ai ----
            'ai_api_url' => ['group' => 'ai', 'type' => 'string', 'default' => '', 'label' => 'AI API URL', 'rules' => 'nullable|url|max:500'],
            'ai_api_key' => ['group' => 'ai', 'type' => 'encrypted', 'default' => '', 'label' => 'AI API Key', 'rules' => 'nullable|string|max:1000'],
            'ai_model' => ['group' => 'ai', 'type' => 'string', 'default' => 'gpt-4o-mini', 'label' => 'AI Model', 'rules' => 'nullable|string|max:100'],
        ];
    }

    public static function groups(): array
    {
        $groups = [];
        foreach (static::all() as $key => $def) {
            $groups[$def['group']][] = $key;
        }

        return $groups;
    }

    public static function definition(string $key): ?array
    {
        return static::all()[$key] ?? null;
    }
}
