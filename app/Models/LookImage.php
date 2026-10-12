<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LookImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'look_id',
        'image',
        'sort_order',
    ];

    public function look(): BelongsTo
    {
        return $this->belongsTo(Look::class);
    }
}