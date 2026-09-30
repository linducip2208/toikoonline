# API v1

Base: `{APP_URL}/api/v1`. Rate limit: `throttle:60,1`. Auth: Sanctum Bearer token. Money: integer IDR. Messages: simple English strings (machine-readable).

## Auth

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| POST | `/register` | no | `{name, email, password, password_confirmation, phone?}` → `{token}` |
| POST | `/login` | no | `{email, password}` → `{token}` |
| GET | `/me` | yes | Current user |
| POST | `/logout` | yes | Revoke current token |

Example:

```
POST /api/v1/login
{"email":"buyer@example.com","password":"secret123"}

Authorization: Bearer <token>
```

## Catalog (public)

| Method | Endpoint | Description |
|---|---|---|
| GET | `/products?category=&brand=&search=&sort=latest\|cheapest\|expensive\|popular\|rating&min_price=&max_price=&per_page=` | Paginated products |
| GET | `/products?search=&q=&sort=relevance&in_stock=&min_price=&max_price=` | Scored search (SKU exact boost, typo tolerance; `sort=relevance\|price\|latest\|...`; response adds `search:{query,total}`) |
| GET | `/products/suggest?q=` | Autocomplete (8 rows: id,name,slug,thumbnail_url,price_formatted) |
| GET | `/products/{slug}` | Product detail + reviews |
| GET | `/categories`, `/categories/{slug}` | Categories |
| GET | `/brands` | Brands |
| GET | `/reviews?product_id=` | Approved reviews |
| GET | `/pages/{slug}` | CMS page |
| GET | `/blogs`, `/blogs/{slug}` | Blogs (content only on detail) |
| POST | `/shipping/quote` | `{destination, weight, origin?, couriers?, postcode?, state?, country?, subtotal?}` → live rates + table-rate zones merged by cost + pickup_points |
| POST | `/checkout/quote` | Same merged quote (auth) |

## Customer (auth)

| Method | Endpoint | Description |
|---|---|---|
| GET/POST `/cart`, PUT/DELETE `/cart/{id}` | Cart CRUD (`product_id, quantity, variation?`) |
| POST | `/checkout/quote` | Shipping quote (auth) |
| POST | `/checkout/place` | `{shipping_address, shipping_method, courier?, shipping_cost?, payment_gateway_id, coupon_code?, ...}` → order + payment redirect |
| GET | `/payment/{code}` | Payment status (reconciles pending intents) |
| GET | `/orders`, `/orders/{id}` | Own orders only (owner check) |
| GET/POST `/wishlist`, DELETE `/wishlist/{id}` | Wishlist |
| POST | `/reviews` | `{product_id, rating 1-5, comment}` |
| POST | `/coupons/validate` | `{code}` → discount for current cart subtotal |

Notes:

- Owner checks: orders/cart/wishlist/review-creation are scoped to `auth()->user()->id`.
- Checkout reuses `CouponService::apply` + `PaymentGatewayService::createIntent` (same as web flow; see CheckoutController docblock for convergence note).
- BOGO evaluation in CouponService is NOT implemented — snippet for Agent 2/owner: extend `CouponService::apply()` with a `type === 'bogo'` branch returning free-qty discount.
- New lifecycle events (Agent 3): `order.created` fires on `POST /checkout/place`; `payment.failed` on failed gateway webhooks; `refund.created` via RefundService; `review.created` on `POST /reviews`; `cart.abandoned` via `carts:abandoned-dispatch` command. All fan out to database notifications + mail + outbound webhooks + automation rules.
- Stripe (`stripe-pi`, PaymentIntent + `Stripe-Signature`) and PayPal (`paypal-order`, Orders v2 + server-side verification) are registered gateway formats; webhooks at `/payment/webhook/{gatewayId}`.
- v2 is NOT built (no stub routes); version via `/api/v1` prefix only.

## API v2 strategy (no v2 built — policy only)

- Version via URL (`/api/v2`), never via header negotiation; v1 routes stay frozen once v2 ships.
- Resources inherit v1 (`namespace Api\V2 extends/implements V1 contracts`); only breaking fields get new transformers.
- Sunset policy: v1 deprecated-but-served for 6 months after v2 stable, with `Sunset` + `Deprecation` response headers and changelog entry; no silent field removal.
- robots.txt is static at `public/robots.txt` (references `/sitemap.xml`); left as-is.
