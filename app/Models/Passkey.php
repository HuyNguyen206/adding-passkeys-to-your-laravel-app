<?php

namespace App\Models;

use App\Casts\Base64;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passkey extends Model
{
    use HasFactory;

    protected $casts = [
        'data' => 'json',
        'credential_id' => Base64::class
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
