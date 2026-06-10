<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Brand;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Models\Currency;
use App\Models\EmailTemplate;
use App\Models\Language;
use App\Models\Page;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Slider;
use App\Models\Tax;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'customer', 'guard_name' => 'web']);
        Role::create(['name' => 'staff', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin TokoOnline',
            'email' => 'admin@tokoonline.test',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
            'phone' => '08123456789',
        ]);
        $admin->assignRole('super_admin');

        $customer = User::create([
            'name' => 'Budi Pelanggan',
            'email' => 'customer@tokoonline.test',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'email_verified_at' => now(),
            'phone' => '08781234567',
            'balance' => 500000,
        ]);
        $customer->assignRole('customer');

        Currency::create([
            'name' => 'Indonesian Rupiah',
            'symbol' => 'Rp',
            'exchange_rate' => 1,
            'status' => 1,
            'code' => 'IDR',
        ]);

        Language::create([
            'name' => 'Bahasa Indonesia',
            'code' => 'id',
            'app_lang_code' => 'id',
            'rtl' => 0,
            'status' => 1,
        ]);

        Language::create([
            'name' => 'English',
            'code' => 'en',
            'app_lang_code' => 'en',
            'rtl' => 0,
            'status' => 1,
        ]);

        Tax::create([
            'name' => 'PPN 11%',
            'tax_status' => 1,
        ]);

        $categories = [
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'featured' => 1, 'top' => 1],
            ['name' => 'Fashion', 'slug' => 'fashion', 'featured' => 1, 'top' => 1],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'featured' => 1, 'top' => 1],
            ['name' => 'Kesehatan', 'slug' => 'kesehatan', 'featured' => 1, 'top' => 1],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'featured' => 0, 'top' => 1],
            ['name' => 'Otomotif', 'slug' => 'otomotif', 'featured' => 0, 'top' => 1],
            ['name' => 'Buku', 'slug' => 'buku', 'featured' => 0, 'top' => 0],
            ['name' => 'Mainan', 'slug' => 'mainan', 'featured' => 0, 'top' => 0],
        ];
        foreach ($categories as $cat) {
            Category::create($cat);
        }

        $brands = [
            ['name' => 'Samsung', 'slug' => 'samsung', 'top' => 1],
            ['name' => 'Apple', 'slug' => 'apple', 'top' => 1],
            ['name' => 'Nike', 'slug' => 'nike', 'top' => 1],
            ['name' => 'Adidas', 'slug' => 'adidas', 'top' => 1],
            ['name' => 'Sony', 'slug' => 'sony', 'top' => 0],
            ['name' => 'Panasonic', 'slug' => 'panasonic', 'top' => 0],
        ];
        foreach ($brands as $brand) {
            Brand::create($brand);
        }

        $products = [
            [
                'name' => 'Samsung Galaxy S24',
                'category_id' => 1,
                'brand_id' => 1,
                'unit_price' => 14999000,
                'description' => 'Smartphone flagship Samsung terbaru dengan AI.',
                'slug' => 'samsung-galaxy-s24',
                'published' => 1,
                'approved' => 1,
                'tags' => 'samsung,galaxy,smartphone',
                'unit' => 'pcs',
            ],
            [
                'name' => 'iPhone 15 Pro',
                'category_id' => 1,
                'brand_id' => 2,
                'unit_price' => 19999000,
                'description' => 'iPhone terbaru dengan chip A17 Pro.',
                'slug' => 'iphone-15-pro',
                'published' => 1,
                'approved' => 1,
                'tags' => 'iphone,apple,smartphone',
                'unit' => 'pcs',
            ],
            [
                'name' => 'Nike Air Max',
                'category_id' => 2,
                'brand_id' => 3,
                'unit_price' => 2199000,
                'description' => 'Sepatu Nike Air Max original.',
                'slug' => 'nike-air-max',
                'published' => 1,
                'approved' => 1,
                'tags' => 'nike,sepatu,sneakers',
                'unit' => 'pasang',
            ],
            [
                'name' => 'Blender Philips',
                'category_id' => 3,
                'brand_id' => 6,
                'unit_price' => 499000,
                'description' => 'Blender serbaguna Philips.',
                'slug' => 'blender-philips',
                'published' => 1,
                'approved' => 1,
                'tags' => 'blender,philips,dapur',
                'unit' => 'pcs',
            ],
            [
                'name' => 'Raket Badminton',
                'category_id' => 5,
                'brand_id' => 4,
                'unit_price' => 350000,
                'description' => 'Raket badminton ringan.',
                'slug' => 'raket-badminton',
                'published' => 1,
                'approved' => 1,
                'tags' => 'raket,badminton,olahraga',
                'unit' => 'pcs',
            ],
        ];
        foreach ($products as $p) {
            $product = Product::create($p + [
                'user_id' => 1,
                'added_by' => 'admin',
                'photos' => '[]',
                'attributes' => '[]',
                'meta_title' => $p['name'],
            ]);
            $product->categories()->attach($p['category_id']);
            ProductStock::create([
                'product_id' => $product->id,
                'variant' => 'Default',
                'sku' => strtoupper(substr($p['slug'], 0, 4)) . '-001',
                'price' => $p['unit_price'],
                'qty' => rand(10, 100),
            ]);
        }

        Slider::create([
            'title' => 'Promo Gajian',
            'subtitle' => 'Diskon hingga 70%',
            'link' => '/products',
            'position' => 1,
            'status' => 1,
        ]);

        Slider::create([
            'title' => 'Brand Terbaik',
            'subtitle' => 'Produk original 100%',
            'link' => '/brands',
            'position' => 2,
            'status' => 1,
        ]);

        Page::create([
            'type' => 'about',
            'title' => 'Tentang Kami',
            'slug' => 'tentang-kami',
            'content' => '<h2>Tentang TokoOnline</h2><p>TokoOnline adalah platform e-commerce modern yang menyediakan jutaan produk berkualitas dari ribuan penjual terpercaya di seluruh Indonesia. Kami berkomitmen memberikan pengalaman belanja online yang aman, nyaman, dan menyenangkan.</p><p>Didirikan dengan visi mempermudah akses masyarakat Indonesia terhadap produk berkualitas, TokoOnline terus berinovasi menghadirkan fitur-fitur terbaik untuk kenyamanan berbelanja Anda.</p>',
        ]);

        Page::create([
            'type' => 'terms',
            'title' => 'Syarat & Ketentuan',
            'slug' => 'syarat-ketentuan',
            'content' => '<h2>Syarat dan Ketentuan</h2><p>Dengan menggunakan layanan TokoOnline, Anda menyetujui syarat dan ketentuan yang berlaku. Harap membaca dengan saksama sebelum melakukan transaksi.</p><h3>Akun Pengguna</h3><p>Anda bertanggung jawab menjaga kerahasiaan akun dan kata sandi. Semua aktivitas yang terjadi di bawah akun Anda adalah tanggung jawab Anda.</p><h3>Transaksi</h3><p>Semua transaksi yang dilakukan melalui TokoOnline bersifat mengikat setelah pembayaran dikonfirmasi.</p>',
        ]);

        Page::create([
            'type' => 'privacy',
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
            'content' => '<h2>Kebijakan Privasi</h2><p>Kami menjaga kerahasiaan data Anda dengan standar keamanan tertinggi. Data pribadi yang dikumpulkan hanya digunakan untuk meningkatkan layanan dan tidak akan dibagikan kepada pihak ketiga tanpa izin.</p><h3>Data yang Kami Kumpulkan</h3><p>Informasi akun, riwayat transaksi, dan data penggunaan platform.</p><h3>Keamanan Data</h3><p>Kami menggunakan enkripsi dan protokol keamanan standar industri untuk melindungi data Anda.</p>',
        ]);

        $blogCat = BlogCategory::create([
            'name' => 'Tips Belanja',
            'slug' => 'tips-belanja',
        ]);

        Blog::create([
            'category_id' => $blogCat->id,
            'user_id' => 1,
            'title' => 'Cara Belanja Online yang Aman',
            'slug' => 'cara-belanja-online-aman',
            'short_description' => 'Tips belanja online aman untuk menghindari penipuan.',
            'content' => '<p>Belanja online semakin populer di Indonesia. Namun, ada beberapa risiko yang perlu diwaspadai. Berikut tips aman berbelanja online:</p><h3>1. Cek Rating Toko</h3><p>Selalu periksa rating dan reputasi toko sebelum membeli. Toko dengan rating tinggi dan ulasan positif umumnya lebih terpercaya.</p><h3>2. Baca Ulasan Produk</h3><p>Jangan hanya melihat gambar — baca ulasan dari pembeli sebelumnya untuk mengetahui kualitas produk sebenarnya.</p><h3>3. Gunakan Metode Pembayaran Aman</h3><p>Gunakan rekening bersama (escrow) atau pembayaran melalui platform untuk keamanan transaksi.</p><h3>4. Jangan Tergiur Harga Terlalu Murah</h3><p>Jika harga terlalu murah dibandingkan harga pasaran, waspadalah — bisa jadi itu penipuan.</p>',
            'is_published' => 1,
            'published_at' => now(),
        ]);

        BusinessSetting::create([
            'type' => 'general',
            'key' => 'site_name',
            'value' => 'TokoOnline',
        ]);

        BusinessSetting::create([
            'type' => 'general',
            'key' => 'site_description',
            'value' => 'Platform E-Commerce Terlengkap',
        ]);

        BusinessSetting::create([
            'type' => 'general',
            'key' => 'contact_email',
            'value' => 'support@tokoonline.test',
        ]);

        BusinessSetting::create([
            'type' => 'general',
            'key' => 'contact_phone',
            'value' => '08123456789',
        ]);

        BusinessSetting::create([
            'type' => 'general',
            'key' => 'address',
            'value' => 'Jl. Sudirman No. 123, Jakarta',
        ]);

        BusinessSetting::create([
            'type' => 'social',
            'key' => 'facebook',
            'value' => 'https://facebook.com/tokoonline',
        ]);

        BusinessSetting::create([
            'type' => 'social',
            'key' => 'instagram',
            'value' => 'https://instagram.com/tokoonline',
        ]);

        EmailTemplate::create([
            'identifier' => 'order_placed',
            'email_type' => 'customer',
            'subject' => 'Pesanan Anda Telah Dibuat',
            'default_text' => 'Terima kasih, pesanan Anda #{order_code} telah dibuat.',
            'status' => 1,
        ]);

        EmailTemplate::create([
            'identifier' => 'order_confirmed',
            'email_type' => 'customer',
            'subject' => 'Pesanan Dikonfirmasi',
            'default_text' => 'Pesanan Anda #{order_code} telah dikonfirmasi.',
            'status' => 1,
        ]);

        $this->seedPaymentGateways();
    }

    protected function seedPaymentGateways(): void
    {
        $gateways = [
            [
                'name' => 'Midtrans Snap',
                'gateway_format' => 'midtrans-snap',
                'base_url' => 'https://app.sandbox.midtrans.com/snap/v1',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 1,
                'config' => ['channels' => ['credit_card', 'bank_transfer', 'gopay', 'shopeepay', 'qris', 'cstore']],
            ],
            [
                'name' => 'Midtrans Core API',
                'gateway_format' => 'midtrans-core',
                'base_url' => 'https://api.sandbox.midtrans.com/v2',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Xendit Invoice',
                'gateway_format' => 'xendit-invoice',
                'base_url' => 'https://api.xendit.co',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Tripay Closed',
                'gateway_format' => 'tripay-closed',
                'base_url' => 'https://tripay.co.id/api',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 4,
                'config' => ['merchant_code' => ''],
            ],
            [
                'name' => 'Duitku',
                'gateway_format' => 'duitku-redirect',
                'base_url' => 'https://sandbox.duitku.com/webapi/api/merchant',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'OY! Indonesia',
                'gateway_format' => 'oyindonesia-api',
                'base_url' => 'https://api-stg.oyindonesia.com/api',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'iPaymu',
                'gateway_format' => 'ipaymu-api',
                'base_url' => 'https://sandbox.ipaymu.com/api/v2',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Faspay',
                'gateway_format' => 'faspay-api',
                'base_url' => 'https://faspay.co.id/api',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'DOKU',
                'gateway_format' => 'doku-api',
                'base_url' => 'https://api.doku.com',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 9,
            ],
            [
                'name' => 'ESIA Pay',
                'gateway_format' => 'esiapay-api',
                'base_url' => 'https://esiapay.com/api',
                'is_active' => false,
                'is_sandbox' => true,
                'sort_order' => 10,
            ],
        ];

        foreach ($gateways as $g) {
            \App\Models\PaymentGatewayConfig::create($g);
        }
    }
}
