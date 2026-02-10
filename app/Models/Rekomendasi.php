<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekomendasi extends Model
{
    protected $table = 'rekomendasi';
    public $timestamps = false;
    protected $fillable = [
        'id_user',
        'id_wisata',
    ];
}
