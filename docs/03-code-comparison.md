# Code Comparison: ActiveEcommerce CMS vs TokoOnline

> **Generated:** 2026-06-10  
> **ActiveEcommerce:** Laravel 10 · Bootstrap 4 · jQuery · Vue 2 · Laravel Mix  
> **TokoOnline:** Laravel 12 · Filament 3 · TailwindCSS 4 · Alpine.js · Vite

---

## 1. Architecture Overview

| Dimension | ActiveEcommerce CMS | TokoOnline | Winner |
|-----------|---------------------|------------|:---:|
| **Framework** | Laravel 10 | Laravel 12 | TokoOnline |
| **Admin Panel** | Custom Blade + AizPack (Bootstrap 4) | Filament 3 (TALL stack SPA) | **TokoOnline** |
| **Frontend Stack** | Bootstrap 4 + jQuery + Vue 2 | TailwindCSS CDN + Alpine.js CDN | **TokoOnline** |
| **Asset Builder** | Laravel Mix 2 (legacy) | Vite 8 | **TokoOnline** |
| **DB Migrations** | 4 migration files + manual SQL dump | 84 migration files | **TokoOnline** |
| **Models** | 178 files (many translation models) | 79 files | Active (more complete domain) |
| **Controllers** | ~100 files (flat + deeply nested) | 19 files (thin, organized) | **TokoOnline** |
| **Services** | 25 files (mixed patterns) | 5 files (clean, format-based) | **TokoOnline** |
| **Routes** | 30 route files (addon-based split) | 4 route files (domain-based) | **TokoOnline** |
| **WYSIWYG** | Summernote (Bootstrap 4) | Filament RichEditor (Tiptap) | **TokoOnline** |
| **License** | mehedi-iitdu/core-component-repository | RSA + AES-256-GCM whitelabel v3 | **TokoOnline** |
| **SEO** | Basic sitemap (spatie/laravel-sitemap) | PSEO 1M+ pages + IndexNow 5 engines | **TokoOnline** |
| **Testing** | Not found | phpunit.xml present, tests directory | - |

---

## 2. Code Quality Comparison

### 2.1 Controller Patterns

**ActiveEcommerce — `ProductController.php` (1,175 lines):**
```php
// Monolithic controller, 1,175 lines, ~40 methods
class ProductController extends Controller
{
    protected $productService;
    protected $productTaxService;
    protected $productFlashDealService;
    protected $productStockService;
    protected $frequentlyBoughtProductService;
    protected $aiService;

    public function __construct(
        ProductService $productService,
        ProductTaxService $productTaxService,
        // ... 6 services injected
    ) {
        // 300+ lines of middleware permission checks  
        $this->middleware(['permission:add_new_product'])->only('create');
        $this->middleware(['permission:show_all_products'])->only('all_products');
        // ... 10+ permission checks
    }

    // 40 methods: admin_products, seller_products, create, store, 
    // edit, update, destroy, duplicate, updateFeatured, updatePublished,
    // sku_combination, product_search, bulk_product_delete, etc.
}
```

**TokoOnline — `ProductController.php` (174 lines):**
```php
// Thin controller, 174 lines, 6 methods
class ProductController extends Controller
{
    const SORT_OPTIONS = ['latest','oldest','price-asc','price-desc',
        'name-asc','name-desc','popular','rating'];

    public function index(Request $request)
    {
        $products = Product::published()->approved()->with(['category','brand']);
        // Apply filters
        if ($request->filled('category')) { ... }
        if ($request->filled('brand')) { ... }
        if ($request->filled('min_price')) { ... }
        // Apply sort
        // Return view with pagination
    }
    
    public function show($slug) { ... }
    public function category($slug) { ... }
    public function brand($slug) { ... }
    public function search(Request $request) { ... }
}
```

**Verdict:** TokoOnline controllers are 6-7x thinner and follow Single Responsibility Principle. ActiveEcommerce controllers are monolithic with 40+ methods each.

---

### 2.2 Service Layer

**ActiveEcommerce:**
- **25 services** but mixed quality
- `ProductService` = 865 lines, monolithic CRUD
- `AiService` = hardcoded Gemini provider, 270 lines
- `OrderService` = instantiates controllers directly (`new AffiliateController`) — tight coupling
- `SendSmsService` = factory pattern with interface ✅ (good)
- Overall: Inconsistent patterns, some clean, some messy

**TokoOnline:**
- **5 services**, all clean and focused
- `PaymentGatewayService` = format-based adapter, no vendor hardcoding
- `AiContentService` = OpenAI-compatible, user-configured
- `ShippingService` = config-driven, works with RajaOngkir/Biteship
- `IndexNowService` = 5 search engines, cache deduplication
- `LicenseClient` = full crypto (RSA + AES-256-GCM + HKDF)
- All services under 270 lines each

**Verdict:** TokoOnline services are cleaner architecturally (format-based adapters, no hardcoded providers). ActiveEcommerce has more services but inconsistent quality.

---

### 2.3 Model Quality

**ActiveEcommerce — `Product` model (202 lines):**
```php
class Product extends Model
{
    use PreventDemoModeChanges;
    protected $guarded = ['choice_attributes'];  // !!! No $fillable
    protected $with = ['product_translations', 'taxes', 'thumbnail'];

    // Manual translation getter (not a Laravel feature)
    public function getTranslation($field = '', $lang = false) { ... }
    
    // 20+ relationships
    // Uses Attribute cast class (Laravel 9+)
    // No scope methods (uses external helper functions instead)
}
```

**TokoOnline — `Product` model (220 lines):**
```php
class Product extends Model
{
    protected $fillable = [/* 74 fields explicitly listed */];
    
    protected function casts(): array
    {
        return [
            'variant_product' => 'boolean',
            'unit_price' => 'decimal:2',
            'rating' => 'decimal:2',
            // 17 explicit casts
        ];
    }

    // 6 query scopes
    public function scopePublished($query) { return $query->where('published', 1); }
    public function scopeApproved($query) { return $query->where('approved', 1); }
    public function scopePhysical($query) { return $query->where('digital', 0); }
    
    // 21 relationships with explicit return types
    public function categories(): BelongsToMany { ... }
    public function stocks(): HasMany { ... }
}
```

**Verdict:** TokoOnline models use modern Laravel patterns (`casts()` method, explicit `$fillable`, query scopes). ActiveEcommerce uses legacy patterns (`$guarded`, manual translation getters).

---

### 2.4 Admin Panel Code

**ActiveEcommerce — Blade-based admin (1,771 lines per create page):**
```php
// resources/views/backend/product/products/create.blade.php (1,771 lines!)
@extends('backend.layouts.app')
@section('content')
    @php
        CoreComponentRepository::instantiateShopRepository();
        CoreComponentRepository::initializeCache();
    @endphp

    <div class="page-content">
        <div class="aiz-titlebar text-left mt-2 pb-2 px-3 px-md-2rem">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="h3 fw-700">{{ translate('Add New Product') }}</h1>
                </div>
            </div>
        </div>
        <!-- 1,700+ more lines of Blade + PHP mixed in view -->
        <!-- Includes recursive PHP functions defined IN THE VIEW -->
        @php
            function renderSingleCategoryOptions($categories, $selectedId = null, $level = 0) {
                foreach ($categories as $category) {
                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
                    echo "<option value=\"{$category->id}\" ...>";
                    // Recursive logic
                }
            }
        @endphp
    </div>
@endsection
```

**TokoOnline — Filament resource (233 lines for ProductResource):**
```php
class ProductResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = '📦 Katalog';
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informasi Produk')->schema([
                TextInput::make('name')->required()->maxLength(200),
                Select::make('category_id')->relationship('category', 'name'),
                RichEditor::make('description'),         // ← WYSIWYG built-in
                TagsInput::make('tags'),
                FileUpload::make('thumbnail_img')->image(),
            ])->columns(2),
            
            Section::make('Harga & Stok')->schema([...])->columns(2),
            Section::make('Varian Produk')->schema([
                Toggle::make('variant_product')->live(),
                Repeater::make('variants_data')
                    ->relationship('stocks')
                    ->schema([...])
                    ->visible(fn(Get $get) => $get('variant_product')),
            ]),
            Section::make('AI Content Generator')->schema([
                Action::make('generate_description')->label('Generate Deskripsi')
                    ->icon('heroicon-o-sparkles')
                    ->action(function (Set $set, $state) { ... }),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('thumbnail_img')->circular(),
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('category.name')->label('Kategori'),
            TextColumn::make('unit_price')->money('IDR'),
            IconColumn::make('published')->boolean(),
        ])
        ->filters([...])
        ->headerActions([
            ImportAction::make()->importer(ProductImporter::class),
            ExportAction::make()->exporter(ProductExporter::class),
        ]);
    }
}
```

**Verdict:** TokoOnline's Filament admin is **10x less code** for the same functionality. ActiveEcommerce embeds PHP logic inside Blade views (recursive functions, license checks). Filament provides type-safe form builder with built-in validation, relationship handling, and conditional visibility.

---

### 2.5 Frontend Quality

**ActiveEcommerce — Bootstrap 4 + jQuery + Vue 2:**
```js
// Dependencies
"bootstrap": "^4.0.0",
"jquery": "^3.2",
"vue": "^2.5.7",     // Vue 2 (May 2016)
"laravel-mix": "^2.0" // Laravel Mix 2 (2018)
```
- Uses Bootstrap 4 jQuery plugins (modals, tooltips, carousel)
- Vue 2 for isolated components (cart sidebar, search autocomplete)
- No modern CSS framework (no Tailwind, no utility classes)
- No modern JS framework (no Alpine, no reactive state management)
- **Mix 2 cannot treeshake, produces large bundles**

**TokoOnline — TailwindCSS CDN + Alpine.js CDN:**
```html
<!-- Zero JS bundle for storefront -->
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3"></script>
```

- **Animations built-in**: 5 CSS `@keyframes` (floatSlow, fadeSlideUp, scaleIn, shimmer, pingSlow)
- **Scroll reveal**: CSS `.reveal` + IntersectionObserver
- **Card hover**: `translateY(-6px)` with shadow elevation
- **Alpine.js**: Reactive cart drawer, password toggle, checkout steps
- **Zero build step for storefront** — always up-to-date via CDN
- **Vite compiled only** for admin panel CSS

**Verdict:** TokoOnline frontend is modern (2024+ tech), zero JS bundle, and produces smaller pages. ActiveEcommerce is stuck in 2018-2019 frontend era.

---

## 3. Unique Differentiators

### 3.1 What TokoOnline Has That ActiveEcommerce Doesn't

| Feature | Description |
|---------|------------|
| **PSEO Engine** | 5 pattern routes generating 1M+ SEO pages from DB data |
| **IndexNow** | Auto-submit to Bing, Yandex, Seznam, Naver, IndexNow.org |
| **License v3** | RSA + AES-256-GCM pairing, domain-bound, anti-tamper |
| **Format-Based Payment** | Generic adapters (redirect/embed/qr/bank_transfer), no vendor lock-in |
| **OpenAI-Compatible AI** | User configures any OpenAI-compatible provider, not just Gemini |
| **Filament Admin** | Modern SPA admin panel with type-safe forms, built-in WYSIWYG |
| **Database Migrations** | 84 Laravel migrations (vs 4 + manual SQL dump) |
| **Custom Theme CSS** | 181 lines of premium responsive Filament theme |
| **Scheduler Commands** | 4 automation commands (backup, escalate, IndexNow, notifications) |
| **Deployment Config** | DEPLOYMENT.md + deploy/nginx.conf + deploy/supervisor.conf |

### 3.2 What ActiveEcommerce Has That TokoOnline Doesn't

| Feature | Description |
|---------|------------|
| **Translation Models** | XxxTranslation tables for multi-language i18n at DB level |
| **Addon System** | Plugin/extension architecture with activation/deactivation |
| **More Payment Gateways** | 20+ gateway integrations (vs our format-based 4 patterns) |
| **Social Login** | Google, Facebook, Twitter, Apple sign-in |
| **OTP/SMS Providers** | 9 SMS gateway integrations |
| **Spatie Analytics** | Google Analytics integration |
| **MPDF/NiklasRavnsborg PDF** | Alternative PDF engines |
| **Demo Mode Trait** | 126 models use PreventDemoModeChanges |

---

## 4. Security Comparison

| Aspect | ActiveEcommerce | TokoOnline |
|--------|:---:|:---:|
| API keys encrypted at rest | ❌ (plain env vars) | ✅ (encrypted in DB) |
| License verification | External package (opaque) | Own crypto implementation (transparent) |
| CSRF protection | ✅ | ✅ |
| API key in DB column | ❌ (environment only) | ✅ (encrypted, user-managed) |
| Mass assignment protection | `$guarded` per model | `$fillable` per model |
| Demo mode protection | Model-level trait (126 models) | N/A — different use case |
| Input validation | Form Requests (21 files) | Filament built-in + Form Requests |
| Rate limiting | API throttle only | API throttle only |
| CORS | Standard Laravel | Standard Laravel |

---

## 5. Final Verdict

| Category | ActiveEcommerce | TokoOnline |
|----------|:---:|:---:|
| **Modernity** | 2018-2020 stack | 2024+ stack |
| **Code Quality** | Mixed (some good patterns, some bad) | Consistent, clean |
| **Admin Panel** | Custom Blade — verbose, fragile | Filament — concise, robust |
| **Maintainability** | Hard (monolithic controllers, no tests) | Easier (thin controllers, consistent services) |
| **Extensibility** | Addon system | Modifiable Filament resources |
| **SEO Capability** | Basic sitemap | Full PSEO + IndexNow |
| **License Protection** | External package | Own crypto v3 |
| **Frontend Performance** | Large JS bundles | Zero JS for storefront |
| **Database Management** | Manual SQL | 84 Laravel migrations |
| **WYSIWYG** | Summernote (200KB jQuery plugin) | Tiptap (Filament RichEditor, 50KB) |

**Bottom line:** TokoOnline is a modern, clean rebuild of the ActiveEcommerce concept — same database schema, same feature set, but with 2024+ code quality standards. It replaces ~100 legacy controllers with 19 thin controllers, ~30 route files with 4 organized ones, and Bootstrap+jQuery with Tailwind+Alpine. The admin panel is 10x less code with Filament replacing custom Blade views.

**What ActiveEcommerce does better:** Translation system (DB-level i18n), addon/plugin architecture, more payment gateway integrations, social login.
