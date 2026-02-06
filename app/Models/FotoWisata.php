<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoWisata extends Model
{
    protected $table = 'foto_wisata';

    protected $fillable = [
        'wisata_id',
        'url',
        'is_cover',
    ];

    public $timestamps = false;
}
