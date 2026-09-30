# Shipping

Providers (Biteship / RajaOngkir / local-flat) are intact — see `ShippingManager::quote`. Table rates are an additional source merged on top.

## Zones & methods

- `shipping_zones`: name + wildcard patterns (comma-separated, `*` = any) for country/state/city/postcode + is_active + sort_order. First match order = sort_order.
- `shipping_methods`: zone_id, provider_format (default `table-rate`), courier, service, name, base_rate (IDR), per_kg (IDR per extra kg after first), weight_tiers json (`[{"max_weight_kg":1,"rate":9000}]` — tier wins when matched), free_min_subtotal (nullable → 0 cost), eta, is_active, sort.

Quote math (`ShippingMethod::quoteFor($weightGram, $subtotal)`, pure): free-shipping threshold first → weight tiers → base + ceil(kg)-1 × per_kg. Never negative.

Admin: Filament groups "🚚 Pengiriman" → Zona + Metode.

## Checkout quote endpoint

`POST /api/v1/shipping/quote` and `POST /api/v1/checkout/quote` merge table rates with live provider quotes, sorted by cost. Extra optional fields: `postcode, state, country (default ID), subtotal` (subtotal unlocks free-shipping thresholds).

Response rows: `{provider, courier, service, description, cost, etd, source?}` where `source: "table-rate"` marks zone rows.

Web-checkout snippet (integrator-owned view):

```blade
@foreach ($quotes as $q)
  <label>
    <input type="radio" name="shipping_method" value="{{ $q['courier'] }}-{{ $q['service'] }}" data-cost="{{ $q['cost'] }}">
    {{ $q['provider'] }} — {{ $q['description'] }} (Rp{{ number_format($q['cost']) }}, {{ $q['etd'] }})
  </label>
@endforeach
```

Biteship/RajaOngkir/local-flat rows keep their existing shape (no `source` key).
