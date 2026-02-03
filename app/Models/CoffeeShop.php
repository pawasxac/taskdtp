<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoffeeShop extends Model
{
    protected $fillable = [
        'nama',
        'daerah',
        'kecamatan',
        'alamat',
        'jam_buka',
        'jam_tutup',
        'harga_min',
        'harga_max',
        'rating',
        'deskripsi',
    ];
}

