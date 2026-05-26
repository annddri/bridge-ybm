<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    protected $table = 'inventaris';
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'nama_barang',
        'kode_barang',
        'jumlah',
        'kondisi',
        'keterangan',
        'created_by',
        'created_by_nibs',
        'updated_by',
        'updated_by_nibs',
    ];
}