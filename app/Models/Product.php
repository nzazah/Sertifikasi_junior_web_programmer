<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'category_id', 'name', 'description', 'price', 'stock', 'image', 'status'])]
class Product extends Model
{
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isTersedia(): bool
    {
        return $this->status === 'tersedia';
    }

    /** Produk yang boleh tampil di katalog pembeli. */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia');
    }
}