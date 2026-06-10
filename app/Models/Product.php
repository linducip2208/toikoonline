<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'added_by',
        'user_id',
        'category_id',
        'brand_id',
        'photos',
        'thumbnail_img',
        'short_video',
        'short_video_thumbnail',
        'video_provider',
        'video_link',
        'tags',
        'description',
        'unit_price',
        'purchase_price',
        'variant_product',
        'attributes',
        'choice_options',
        'colors',
        'variations',
        'todays_deal',
        'published',
        'approved',
        'stock_visibility',
        'unit',
        'min_qty',
        'max_qty',
        'low_stock_qty',
        'discount',
        'discount_type',
        'discount_start_date',
        'discount_end_date',
        'tax',
        'tax_type',
        'shipping_type',
        'shipping_cost',
        'num_of_sale',
        'meta_title',
        'meta_description',
        'meta_img',
        'slug',
        'rating',
        'barcode',
        'digital',
        'file_name',
        'file_path',
        'external_link',
        'external_link_btn',
        'wholesale_product',
        'gst_enable',
        'gst_rate',
        'cash_on_delivery',
        'warranty_id',
        'warranty_note_id',
        'refund_note_id',
        'shipping_note_id',
        'delivery_note_id',
        'is_quantity_multiplied',
        'est_shipping_days',
        'weight',
        'height',
        'width',
        'length',
    ];

    protected function casts(): array
    {
        return [
            'variant_product' => 'boolean',
            'published' => 'boolean',
            'approved' => 'boolean',
            'todays_deal' => 'boolean',
            'digital' => 'boolean',
            'wholesale_product' => 'boolean',
            'gst_enable' => 'boolean',
            'cash_on_delivery' => 'boolean',
            'is_quantity_multiplied' => 'boolean',
            'unit_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'tax' => 'decimal:2',
            'rating' => 'decimal:2',
            'weight' => 'decimal:2',
            'height' => 'decimal:2',
            'width' => 'decimal:2',
            'length' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function taxes()
    {
        return $this->hasMany(ProductTax::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function flashDealProducts()
    {
        return $this->hasMany(FlashDealProduct::class);
    }

    public function frequentlyBoughtProducts()
    {
        return $this->hasMany(FrequentlyBoughtProduct::class);
    }

    public function productQueries()
    {
        return $this->hasMany(ProductQuery::class);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function wholesalePrices()
    {
        return $this->hasMany(WholesalePrice::class);
    }

    public function auction()
    {
        return $this->hasOne(Auction::class);
    }

    public function lastViewedProducts()
    {
        return $this->hasMany(LastViewedProduct::class);
    }

    public function thumbnail()
    {
        return $this->belongsTo(Upload::class, 'thumbnail_img');
    }

    public function warranty()
    {
        return $this->belongsTo(Warranty::class);
    }

    public function sizeChart()
    {
        return $this->belongsTo(SizeChart::class);
    }

    public function scopePublished(Builder $query): void
    {
        $query->where('published', true);
    }

    public function scopeApproved(Builder $query): void
    {
        $query->where('approved', true);
    }

    public function scopePhysical(Builder $query): void
    {
        $query->where('digital', false);
    }

    public function scopeDigital(Builder $query): void
    {
        $query->where('digital', true);
    }

    public function scopeTodaysDeal(Builder $query): void
    {
        $query->where('todays_deal', true);
    }
}
