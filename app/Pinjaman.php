<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nik',
        'waktu',
        'nominal',
        'tujuanpinjaman_id',
        'tgl_pengajuan',
        'sts_pinjaman',
        'nomorpinjaman',
        'tahun',
        'total_bunga',
        'bunga',
        'cicilan',
        'cost',

    ];

    function user(){
		  return $this->belongsTo('App\User','nik','username');
    }
}
