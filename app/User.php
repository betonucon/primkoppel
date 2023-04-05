<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
  
class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','status','email', 'password','username','tokennya','role_id','sts_anggota','sts_password','aktif','poscode','cost'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function statusanggota(){
		  return $this->belongsTo('App\Statusanggota','sts_anggota','id');
    }
    function role(){
		  return $this->belongsTo('App\Role','role_id','id');
    }
    function anggota(){
		  return $this->belongsTo('App\Anggota','username','nik');
    }
}
