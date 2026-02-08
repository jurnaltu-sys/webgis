<?php

namespace App\Models;

use App\Models\FotoWisata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wisata extends Model
{
    protected $table = 'wisata';

    protected $fillable = [
        'nama',
        'slug',
        'kategori_id',
        'latitude',
        'longitude',
        'deskripsi',
        'fasilitas',
        'jam_buka',
        'rating_avg',
        'jml_rating',
    ];

    protected $casts = [
        'kategori_id' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'fasilitas' => 'array',
        'rating_avg' => 'decimal:2',
        'jml_rating' => 'integer',
    ];

    public $timestamps = false;

    public function foto(): HasMany
    {
        return $this->hasMany(FotoWisata::class, 'wisata_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
