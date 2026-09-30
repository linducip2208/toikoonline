# Marketing: Promotions, Search, Automation

## Promotion rules engine

Table `promotions`: name, type, config json, priority (lower runs first), starts_at/ends_at, is_active. Admin: Filament "🎫 Promo".

Types & config:

| Type | Config example |
|---|---|
| auto_category_percent | `{"category_id": 3, "percent": 10, "max_discount": 20000}` (category_id may be array) |
| auto_bogo | `{"product_id": 5}` or `{"category_id": 2}` — cheapest unit free per pair |
| auto_tier | `{"tiers": [{"min_subtotal": 100000, "percent": 5, "max_discount": 25000}, {"min_subtotal": 500000, "amount": 50000}]}` — best tier wins, pro-rata per line |
| auto_free_shipping | `{"min_subtotal": 150000, "discount_amount": 10000}` — sets free-shipping flag + cover |

API: `PromotionService::evaluate($lines, $userId, $subtotal, $rules = null)` where `$lines = [['product_id','category_id','price' (int IDR),'qty']]`. Returns `['line_discounts'=>[i=>int], 'free_shipping'=>bool, 'shipping_discount'=>int, 'total_discount'=>int, 'applied'=>[names]]`. Pure, unit-testable, no checkout wiring. Composable by priority, clamped per line (never negative).

Checkout hook for the integrator (Agent 2 owns CheckoutService — 5 lines):

```php
$eval = app(PromotionService::class)->evaluate($lines, $user->id, $subtotal);
$discount += $eval['total_discount'];
if ($eval['free_shipping']) { $shipping = max(0, $shipping - ($eval['shipping_discount'] ?: $shipping)); }
$grandTotal = max(0, $subtotal - $discount + $shipping);
```

## Search engine

`SearchService` (facade) → `SearchDriverInterface` → default `DatabaseDriver` (zero dependency):

- Tokenize ID/EN (`SearchService::tokenize`, pure).
- SKU/barcode exact-match boost (100 pts), name exact-word 20 / substring 10, category/brand 6, tags 4, slug 3.
- Typo tolerance: levenshtein ≤ 2 on name words (tokens ≥ 4 chars), capped 500-candidate set.
- Filters: category/brand slug, min_price/max_price, in_stock. Sorts: relevance/price(=cheapest)/latest/cheapest/expensive/popular/rating.
- API: `GET /api/v1/products?search=&q=&sort=relevance&...` + `GET /api/v1/products/suggest?q=` (web `suggest` endpoint untouched).

Scout swap (Algolia/Meilisearch): bind the interface in a provider:

```php
$this->app->bind(SearchDriverInterface::class, MeilisearchDriver::class);
```

where `MeilisearchDriver implements SearchDriverInterface` (new file, not shipped — DatabaseDriver stays the fallback). Env keys: `SEARCH_DRIVER`, `MEILISEARCH_HOST/KEY`, `ALGOLIA_APP_ID/SECRET`.

Web ProductController snippet (integrator-owned):

```php
$result = app(SearchService::class)->search($request->q, [...filters], $request->sort ?? 'relevance');
$products = Product::with([...])->whereIn('id', $result['ids'])->paginate(20);
```

## Automation rules

Table `automation_rules`: name, event, conditions json (AND of {field dot-path, operator =/!=/>/>=/</<=/contains, value}), actions json, is_active, last_run_at. Admin builder uses selects (no free code). Supported action kinds: notify, email, coupon, webhook.

- No `tag` action: the users table has no tags column — honestly skipped.
- Runner: `AutomationRunner::handle($event, $payload, $subject)` — per-rule try/catch, never throws, updates last_run_at.
- Trigger: `TriggerAutomation` listener on all lifecycle events; `carts:abandoned-dispatch` command emits `cart.abandoned`.
- Email actions use OrderMail (TemplateRenderer path guarded by class_exists; OrderMail is the single send path).
