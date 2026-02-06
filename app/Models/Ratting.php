<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ratting extends Model
{
    protected $table = 'rattings';

    protected $fillable = [
        'user_id',
        'wisata_id',
        'ratting',
        'ulasan',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'wisata_id' => 'integer',
        'ratting' => 'integer',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class, 'wisata_id');
    }
}
