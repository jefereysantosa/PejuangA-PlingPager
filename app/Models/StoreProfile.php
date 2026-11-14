<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @use HasFactory<\Illuminate\Database\Eloquent\Factories\Factory<static>> */
class StoreProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'is_open',
        'bank_name',
        'account_number',
        'account_owner_name',
        'meta_phone_number_id',
        'meta_waba_id',
        'meta_access_token',
    ];

    protected function casts(): array
    {
        return [
            'is_open' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isMetaConfigured(): bool
    {
        return ! empty($this->meta_phone_number_id) && ! empty($this->meta_access_token);
    }

    public function isComplete(): bool
    {
        return ! empty($this->store_name) && $this->isMetaConfigured();
    }
}
