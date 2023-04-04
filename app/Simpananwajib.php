<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simpananwajib extends Model
{
    protected $table = 'simpanan_wajib';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nomortransaksi',
        'nik',
        'bulan',
        'tahun',
        'nominal',
        'cost',
        'sts',
        
  
    ];

    function user(){
		  return $this->belongsTo('App\User','nik','username');
    }
    function anggota(){
		  return $this->belongsTo('App\Anggota','nik','nik');
    }
}
