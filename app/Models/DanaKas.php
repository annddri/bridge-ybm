<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanaKas extends Model
{
    protected $table = 'dana_kas';
    protected $primaryKey = 'id_kas';

    protected $fillable = [
        'tanggal',
        'jenis_transaksi',
        'nominal',
        'keterangan',
        'created_by_id',
        'created_by',
        'created_by_nibs',
    ];
}