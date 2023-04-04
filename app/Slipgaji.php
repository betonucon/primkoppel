<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Slipgaji extends Model
{
    protected $table = 'slip_gaji';
    public $timestamps = false;
    protected $fillable = [
        'nik',
        'gapok',
        'cutitahunan',
        'lembur',
        'rapelan',
        'perumahan',
        'transport',
        'makan',
        'shift',
        'pengalaman',
        'profesi',
        'risiko',
        'total',
        'total_upah',
        'jht',
        'simpanan_wajib',
        'tabungan',
        'ket_pinjaman',
        'pinjaman',
        'bpjs',
        'bpjs_pensiun',
        'total_potongan',
        'bpjs_kesehatan',
        'jht_jamsostek',
        'jkk_jamsostek',
        'kematian',
        'pensiun',
        'comp',
        'dibayar',
        'bulan',
        'tahun',
        'id',


    ];

    function usernya(){
		  return $this->belongsTo('App\User','nik','username');
    }
}
