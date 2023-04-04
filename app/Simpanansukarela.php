<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Simpanansukarela extends Model
{
    protected $table = 'simpanan_sukarela';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nik',
        'bulan',
        'tahun',
        'nominal',
        'nomortransaksi',
        'cost',
        
  
    ];

    function user(){
		return $this->belongsTo('App\User','nik','username');
    }
    function anggota(){
		  return $this->belongsTo('App\Anggota','nik','nik');
    }
}
