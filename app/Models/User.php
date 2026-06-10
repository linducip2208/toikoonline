<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name', 'email', 'password',
        'phone', 'address', 'city', 'postal_code', 'country',
        'balance', 'banned', 'referral_code', 'referred_by',
        'provider', 'provider_id', 'avatar', 'avatar_original',
        'device_token', 'remaining_uploads', 'user_type',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
            'banned' => 'boolean',
        ];
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class)->orderBy('created_at', 'desc');
    }

    public function clubPoint()
    {
        return $this->hasOne(ClubPoint::class);
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function affiliateLogs()
    {
        return $this->hasMany(AffiliateLog::class);
    }

    public function uploads()
    {
        return $this->hasMany(Upload::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'sender_id');
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function productQueries()
    {
        return $this->hasMany(ProductQuery::class, 'customer_id');
    }

    public function lastViewedProducts()
    {
        return $this->hasMany(LastViewedProduct::class);
    }

    public function compares()
    {
        return $this->hasMany(Compare::class);
    }

    public function userCoupon()
    {
        return $this->hasOne(UserCoupon::class);
    }
}
