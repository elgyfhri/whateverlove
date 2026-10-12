<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getItemsCountAttribute(): int
    {
        return (int) $this->items->sum('qty');
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function (CartItem $item) {
            return (float) $item->product->price * $item->qty;
        });
    }
}