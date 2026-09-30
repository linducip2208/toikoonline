# Payments

Money: integer IDR. Secrets are never logged (keys, signatures, raw bodies).

## Gateway formats

| Format | Driver | Credential fields (payment_gateway_configs) |
|---|---|---|
| midtrans-snap | SnapRedirectAdapter | api_key_encrypted=server key, base_url |
| midtrans-core | CoreApiAdapter | api_key_encrypted=server key, base_url |
| xendit-invoice | XenditInvoiceAdapter | api_key_encrypted=secret key |
| tripay-closed | TripayClosedAdapter | api keys |
| stripe-pi | StripePaymentIntentAdapter | api_key_encrypted=secret key (sk_test_/sk_live_), webhook_config={webhook_secret: whsec_...}, base_url default https://api.stripe.com/v1, is_sandbox=true |
| paypal-order | PayPalOrderAdapter | api_key_encrypted=Client ID, api_secret_encrypted=Secret, webhook_config={webhook_id}, base_url (sandbox default when is_sandbox), is_sandbox=true |
| duitku-redirect | GenericRedirectAdapter | base_url + keys |
| oyindonesia-api, ipaymu-api, faspay-api, doku-api, esiapay-api | GenericApiAdapter | base_url + keys |
| manual transfer / COD | (no adapter) | config={payment_fee, payment_instructions} |

Sandbox-first: keep `is_sandbox=true` until go-live; Stripe test keys start `sk_test_`, PayPal sandbox base is `https://api-m.sandbox.paypal.com`.

## Stripe (PaymentIntent)

1. Create PaymentIntent server-side via `PaymentGatewayService::createIntent` (idempotent per order code).
2. Confirm on the client with the returned `client_secret` (Stripe.js).
3. Webhook `POST /payment/webhook/{gatewayId}`: event types mapped — `payment_intent.succeeded` → paid, `payment_intent.payment_failed`/`canceled` → failed. Signature: `Stripe-Signature` header, HMAC-SHA256 of `timestamp.rawBody` with webhook secret, 300s tolerance. Pure helper: `StripePaymentIntentAdapter::signatureValid($rawBody, $header, $secret)`.

## PayPal (Orders v2)

1. `createIntent` creates an order, returns `redirect_url` (approve link).
2. Buyer approves on paypal.com, capture happens via webhook `CHECKOUT.ORDER.COMPLETED` / `PAYMENT.CAPTURE.COMPLETED` → paid. Verification is server-side (`/v1/notifications/verify-webhook-signature` with webhook_id).

## Fees & instructions (all gateways, incl. manual/COD)

`config` json keys on the gateway row:

```json
{"payment_fee": 4000, "payment_instructions": "Transfer ke BCA 1234567890 a.n. TokoOnline"}
```

or percent fee: `{"payment_fee": {"percent": 2.9, "cap": 10000}}`. Helpers: `PaymentFees::feeForConfig`, `PaymentGatewayInterface::feeFor($amount)` / `instructions()`.

Web-checkout snippet (integrator-owned view — Agent 2 owns CheckoutService, wire fee there):

```blade
{{-- checkout.blade.php --}}
@if ($gatewayInstructions = $gateway->config['payment_instructions'] ?? null)
  <div class="alert alert-info">{{ $gatewayInstructions }}</div>
@endif
<p>Biaya layanan: Rp{{ number_format($paymentFee) }}</p>
```

```php
// CheckoutService hook (5-line shape for integrator):
$adapter = app(PaymentGatewayService::class)->getAdapter($gateway);
$fee = $adapter instanceof PaymentGatewayInterface ? $adapter->feeFor($subtotal) : 0;
$grandTotal = max(0, $subtotal - $discount + $shipping + $fee);
```

## Refunds

`RefundService` supports midtrans-snap, stripe-pi, paypal-order. Success dispatches `refund.created` (listeners notify + webhook). Others return `unsupported` message.
