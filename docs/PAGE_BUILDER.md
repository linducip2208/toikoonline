# Page Builder

Implementasi aktual: kolom `pages.blocks` (json) di-render oleh partial
`resources/views/storefront/partials/page-blocks.blade.php`
(variabel `$blocks`).

## Tipe blok

`hero`, `html`, `banner_grid`, `product_grid` (query `Product::published()`
sumber featured/best_seller/latest), `testimonial`, `faq`, `countdown`,
`newsletter`, `contact_form` (disimpan sebagai subscriber newsletter),
`gallery`, `video` (embed iframe), `pricing`, **`global`** (baru).

## Blok `global`

Form PageResource → Builder → "Blok Global (reusable)" → field `key`
(select dari `global_blocks` aktif). Renderer:

```blade
{{-- di dalam loop $blocks --}}
@elseif(($block['type'] ?? '') === 'global')
  {{-- ambil GlobalBlock aktif by key, render inner blocks, abaikan nested global --}}
```

Aturan: referensi hilang/dinonaktifkan → blok dilewati diam-diam;
maks 1 level nesting (inner bertipe `global` difilter) sehingga tidak ada
rekursi tak terbatas.

## Membuat blok global baru

Filament → Blok Global → Create: isi `key` (alpha_dash, cth `footer_cta`),
`title`, susun Builder (hero/html/banner_grid/newsletter/gallery/video),
aktifkan. Lalu sisipkan blok `global` di halaman mana pun dengan key tsb.
