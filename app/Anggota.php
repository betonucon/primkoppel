<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Anggota extends Model
{
    protected $table = 'anggota';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nik',
        'name',
        'karyawan',
        'cost',
        'alamat',
        'no_hp',
        'tgl_masuk',
        'status',
        'update',
        'nominal',
    ];
}

