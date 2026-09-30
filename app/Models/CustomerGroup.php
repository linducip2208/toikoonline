<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    protected $fillable = ['name', 'discount_percent', 'description', 'is_active'];

    protected $casts = ['discount_percent' => 'decimal:2', 'is_active' => 'boolean'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'customer_group_user');
    }
}
