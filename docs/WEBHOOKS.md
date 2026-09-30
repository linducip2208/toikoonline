# Webhooks (outbound)

Dispatcher: `App\Services\Webhook\WebhookDispatcher` — HMAC-SHA256 (`X-Webhook-Signature: sha256=...`), per-subscription secret, retry backoff 5m/30m/2h/12h, `webhook:retry` command. Secrets never logged.

## Events

order.created, order.paid, order.shipped, order.delivered, payment.failed, refund.created, stock.low, cart.abandoned, review.created, product.created, product.updated.

Admin: Filament "💳 Pembayaran" → Webhook subscriptions (event select is driven by `supportedEvents()`, test-send included).

## Payload envelopes

- order.*: `{order_code, order_id, user_id, total (int IDR), currency: IDR, paid_at|created_at}`
- payment.failed: order envelope + `{reason, failed_at}`
- refund.created: `{order_code, order_id, user_id, amount, currency, refunded_at}`
- stock.low: `{product_id, qty}` (via StockLow event)
- cart.abandoned: `{user_id, email, items, abandoned_at}`
- review.created: `{review_id, product_id, user_id, rating, created_at}`
- product.created/updated: `{product_id, slug, created_at|updated_at}`

## Verify example (receiver, PHP)

```php
$sig = $request->header('X-Webhook-Signature'); // "sha256=..."
$ok = hash_equals('sha256='.hash_hmac('sha256', $request->getContent(), $secret), $sig);
```
