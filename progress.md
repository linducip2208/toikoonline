# Progress — TokoOnline

> **Standalone Ecommerce** · Laravel 12 + Filament 3 + MySQL  
> Based on ActiveEcommerce CMS · Target: Indonesia Market

---

## Status: MVP COMPLETE ✅

**Dev server running at:** http://127.0.0.1:8765  
**Admin panel:** http://127.0.0.1:8765/admin  
**Demo login:** admin@tokoonline.test / password

---

## Setup ✅
- [x] Laravel 12 scaffold
- [x] Filament 3 admin panel (11 resources generated + customized)
- [x] Spatie Permission RBAC (4 roles: super_admin, admin, customer, staff)
- [x] MySQL database `tokoonline` (67 tables migrated)
- [x] DomPDF, Excel, Intervention Image, Sanctum
- [x] Custom Filament theme.css (premium responsive)
- [x] 11 Filament Resources: User, Category, Brand, Product, Order, Coupon, Review, Blog, Page, Slider, FlashDeal
- [x] Database seeded: 5 products, 8 categories, 6 brands, 2 users, 3 pages, 1 blog post, settings

---

## Database — 67 Tables ✅

| Phase | Tables | Status |
|-------|--------|:---:|
| Auth & User | users, roles, permissions, staff, password_resets, personal_access_tokens, registration_verification_codes | ✅ |
| Location | zones, countries, states, cities, areas, addresses | ✅ |
| Catalog | categories, brands, products, product_categories, product_stocks, product_taxes, attributes, attribute_values, colors, uploads, taxes, warranties, size_charts, size_chart_details, measurement_points, custom_labels, notes, frequently_bought_products | ✅ |
| Orders | orders, order_details, carts, payments, payment_methods, wallets, transactions, commission_histories, refund_requests, delivery_histories | ✅ |
| Shipping | carriers, carrier_range_prices, pickup_points, shipping_box_sizes | ✅ |
| Promo | coupons, coupon_usages, flash_deals, flash_deal_products, user_coupons, custom_sale_alerts | ✅ |
| Engagement | reviews, wishlists, compares, conversations, messages, support_tickets, ticket_replies, product_queries, last_viewed_products, searches | ✅ |
| Marketing | blogs, blog_categories, pages, subscribers, sliders, banners, dynamic_popups, email_templates, sms_templates, notification_types, custom_alerts | ✅ |
| Affiliate & Loyalty | affiliates, affiliate_logs, club_points, club_point_details | ✅ |
| System | business_settings, translations, currencies, languages | ✅ |

---

## Models — 60+ ✅
All Eloquent models with `$fillable`, `casts()`, relationships, and scopes.

| Module | Models |
|--------|--------|
| Catalog | Category, Brand, Product, ProductStock, ProductTax, Attribute, AttributeValue, Color, Upload, Tax, Currency, Language, Warranty, MeasurementPoint, CustomLabel, Note, SizeChart, SizeChartDetail, FrequentlyBoughtProduct, ProductQuery |
| Commerce | Address, Cart, Order, OrderDetail, Payment, PaymentMethod, Wallet, Transaction, CommissionHistory, RefundRequest, DeliveryHistory |
| Shipping | Carrier, CarrierRangePrice, PickupPoint, ShippingBoxSize |
| Promo | Coupon, CouponUsage, FlashDeal, FlashDealProduct |
| Engagement | Review, Wishlist, Compare, Conversation, Message, SupportTicket, TicketReply, LastViewedProduct, Search |
| Marketing | Blog, BlogCategory, Page, Subscriber, Slider, Banner, DynamicPopup, EmailTemplate, SmsTemplate, NotificationType, CustomSaleAlert |
| Affiliate | Affiliate, AffiliateLog, ClubPoint, ClubPointDetail |
| System | Zone, Country, State, City, Area, BusinessSetting, Translation, UserCoupon |

---

## Filament Admin Panel ✅

| Resource | Navigation | Icons |
|----------|-----------|-------|
| CategoryResource | 📦 Katalog (sort 1) | heroicon-o-folder |
| BrandResource | 📦 Katalog (sort 2) | heroicon-o-tag |
| ProductResource | 📦 Katalog (sort 11) | heroicon-o-shopping-bag |
| OrderResource | 🛒 Pesanan (sort 31) | heroicon-o-shopping-cart |
| CouponResource | 🎫 Promo | heroicon-o-ticket |
| ReviewResource | 👥 Pelanggan | heroicon-o-star |
| BlogResource | 📢 Marketing | heroicon-o-newspaper |
| PageResource | 📢 Marketing | heroicon-o-document-text |
| SliderResource | 📢 Marketing | heroicon-o-photo |
| FlashDealResource | 🎫 Promo | heroicon-o-bolt |
| UserResource | ⚙️ Sistem | heroicon-o-user |

Theme: Custom CSS with gradient indigo-violet, glass topbar, responsive breakpoints (1024px/640px), print mode, reduced motion.

---

## Public Storefront ✅

| Page | Route | Status |
|------|-------|:---:|
| Homepage (hero, flash deals, featured, brands) | `/` | ✅ |
| Product Listing (filters, sort, pagination) | `/products` | ✅ |
| Product Detail (gallery, variants, reviews, tabs) | `/products/{slug}` | ✅ |
| Cart (qty selector, coupon, summary) | `/cart` | ✅ |
| Checkout (4-step: address → shipping → payment → confirm) | `/checkout` | ✅ |
| Checkout Success | `/checkout/success/{order}` | ✅ |
| Blog Listing | `/blog` | ✅ |
| Blog Detail (article + JSON-LD) | `/blog/{slug}` | ✅ |
| Blog Category | `/blog/category/{slug}` | ✅ |
| Static Pages | `/page/{slug}` | ✅ |
| Search | `/search?q=` | ✅ |
| Category Filter | `/categories/{slug}` | ✅ |
| Brand Filter | `/brands/{slug}` | ✅ |

---

## Customer Portal ✅

| Page | Route | Status |
|------|-------|:---:|
| Dashboard (stats + recent orders) | `/account` | ✅ |
| Orders List (status tabs) | `/account/orders` | ✅ |
| Order Detail (timeline + actions) | `/account/orders/{order}` | ✅ |
| Wishlist | `/account/wishlist` | ✅ |
| Profile (edit + update) | `/account/profile` | ✅ |

---

## Authentication ✅

| Page | Route | Status |
|------|-------|:---:|
| Login (two-column branded) | `/login` | ✅ |
| Register (two-column branded) | `/register` | ✅ |
| Forgot Password | `/forgot-password` | ✅ |

---

## PSEO Engine ✅

| Pattern | Route | Status |
|---------|-------|:---:|
| Best Category Top 10 | `/best-{category}` | ✅ |
| Best Category by Year | `/best-{category}-{year}` | ✅ |
| Product Alternatives | `/alternatif-{product}` | ✅ |
| Compare Products | `/bandingkan/{a}-vs-{b}` | ✅ |
| Buy Source Code Landing | `/beli-aplikasi-toko-online` | ✅ |
| Sitemap XML | `/sitemap.xml` | ✅ |
| Robots.txt | `/robots.txt` | ✅ |
| IndexNow Service | `app/Services/Seo/IndexNowService.php` | ✅ |
| IndexNow Command | `php artisan seo:indexnow` | ✅ |
| Blog RSS Feed | TBD | ⏳ |

---

## Controllers — 15 ✅

| Namespace | Controllers |
|-----------|------------|
| Storefront | HomeController, ProductController, CartController, CheckoutController, BlogController, PageController |
| Customer | DashboardController, OrderController, ProfileController, WishlistController |
| Auth | LoginController, RegisterController, ForgotPasswordController |
| SEO | SeoController, SitemapController |

---

## Infrastructure ✅

| File | Purpose | Status |
|------|---------|:---:|
| `DEPLOYMENT.md` | Step-by-step production setup | ✅ |
| `deploy/nginx.conf` | Nginx server config | ✅ |
| `deploy/supervisor.conf` | Queue worker config | ✅ |
| `.env.example` | All environment variables | ✅ |
| `tests/Feature/StorefrontTest.php` | 9 feature tests | ✅ |
| `progress.md` | This file | ✅ |

---

## Remaining (Phase 2+)

- [ ] License pairing (whitelabel v3)
- [ ] Playwright screenshots (21 pages desktop + mobile)
- [ ] npm install + Vite build for Filament theme
- [ ] Blog RSS feed
- [ ] Payment gateway integration (Midtrans, Xendit adapters)
- [ ] Shipping API integration (RajaOngkir, Biteship)
- [ ] Product variants UI in admin
- [ ] AI content generation for products
- [ ] Bulk product import/export
- [ ] Notification queues (email, FCM push)
- [ ] Scheduler cron: backup, IndexNow, overdue escalation
- [ ] `/docs` page with real screenshots

---

**Total files created:** ~130+  
**Database tables:** 67  
**Models:** 60+  
**Views:** 25+  
**Controllers:** 15  
**Routes:** 40+  

*Last updated: 2026-06-10 · Dev server running on :8765*

---

## Code Comparison Report
See `docs/03-code-comparison.md` for full detailed analysis.

**Key findings:**
- TokoOnline: Laravel 12 + Filament 3 + TailwindCSS 4 + Alpine.js (2024+ stack)
- ActiveEcommerce: Laravel 10 + Bootstrap 4 + jQuery + Vue 2 + Laravel Mix (2018 stack)
- Admin panel: 10x less code (Filament vs custom Blade)
- Controllers: 19 thin vs ~100 monolithic (6-7x thinner)
- Routes: 4 organized files vs 30 addon-based files
- Frontend: Zero JS bundle (CDN) vs large jQuery+Vue bundles
- Services: Clean format-based adapters vs mixed patterns + hardcoded providers
