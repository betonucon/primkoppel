<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pembayaranpinjaman extends Model
{
    protected $table = 'pembayaran_pinjaman';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nik',
        'bulan',
        'tahun',
        'total',
        'name',
        'kategori',
        'pinjaman',
        'margin',
        'cost',
        'tanggal',
        'pinjaman_id',
        'angsuran',
        'sts',
    ];

    function anggota(){
		return $this->belongsTo('App\Anggota','nik','nik');
    }

}
