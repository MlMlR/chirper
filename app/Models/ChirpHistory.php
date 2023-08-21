<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChirpHistory extends Model
{
    use HasFactory;

    public function chirp(): BelongsTo
    {
        return $this->belongsTo(Chirp::class);
    }

    protected $fillable = [
        'message',
        'user_id',
    ];
}
