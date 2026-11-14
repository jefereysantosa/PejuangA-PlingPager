<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>> */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'price',
        'description',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_available' => 'boolean',
            'description' => 'string',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
