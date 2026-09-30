# SEO

Existing: `SitemapController::urlSet()` (hreflang id/en + lastmod, shared by live `/sitemap.xml` and `sitemap:generate`), `SeoController` (best-category, alternatives, compare, buy-source-code, faqSchema, canonical), `IndexNowService` (+ `indexnow:submit`, sitemap ping). Untouched by this wave.

## Product lifecycle → index hooks (integrator snippets)

Product model is integrator-owned; dispatch the new events via observer:

```php
// app/Observers/ProductObserver.php (integrator to create + register)
class ProductObserver
{
    public function created(Product $p): void { event(new ProductCreated($p)); }
    public function updated(Product $p): void { event(new ProductUpdated($p)); }
}
```

Listeners already fan out to outbound webhooks (`product.created/updated`) — wire IndexNow there or via automation rule (event `product.updated` + action webhook to your indexer URL).

## Stock-low snippet (InventoryService owner)

```php
// after stock decrement (Agent 2, InventoryService):
if ($qtyAfter <= $product->low_stock_qty) {
    event(new \App\Events\StockLow($product, $qtyAfter));
}
```

`NotifyStockLow` logs + can be extended to admin notifications; `stock.low` is also a webhook/automation event.
