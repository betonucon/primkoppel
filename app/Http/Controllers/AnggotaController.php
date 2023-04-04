<?php

namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Vanggota;
use App\Anggota;
use App\User;
class AnggotaController extends Controller
{
    public function index(request $request){
        $menu='Daftar Anggota';
        return view('anggota.index',compact('menu'));
    }

    public function cari_anggota(request $request){
        $data=Anggota::where('nik',$request->nik)->first();
        echo $data['nik'].'@'.$data['name'].'@'.$data['nominal'].'@'.uang($data['nominal']).'@'.uang(saldo_simpananwajib($request->nik));
    }
    public function tambah(request $request){
        error_reporting(0);
        $id=$request->id;
        if($id!=0){
            $read='readonly';
        }else{
            $read='';
        }
        $data=Vanggota::where('nik',$request->id)->first();
        return view('Anggota.tambah',compact('data','id','read'));
    }

    public function get_data(request $request){
        $data=Vanggota::where('cost',Auth::user()['cost'])->get();
       
        return  Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function($data){
                    return'<span class="btn btn-yellow btn-xs" onclick="tambah('.$data['nik'].')"><i class="fas fa-pencil-alt fa-fw"></i></span>';
                })
                ->rawColumns(['action'])
                ->make(true);
    }

    public function hapus_data(request $request){
        error_reporting(0);
        $sum=count($request->id);
        for($x=0;$x<$sum;$x++){
            $deluser=User::where('username',$request->id[$x])->delete();
            $delanggota=Anggota::where('nik',$request->id[$x])->delete();
        }
    }
    public function save_data(request $request){
        error_reporting(0);
        
        $rules = [
            'username'=> 'required|numeric',
            'name'=> 'required',
            'email'=> 'required|email',
            'alamat'=> 'required',
            'no_hp'=> 'required|numeric',
            'tgl_masuk'=> 'required',
            'nominal'=> 'required|numeric',
        ];

        $messages = [
            'username.required'=> 'Isi NIK Anggota', 
            'username.numeric'=> 'NIK Anggota Berupa angka', 
            'name.required'=> 'Isi Nama Anggota', 
            'email.required'=> 'Isi Email Anggota',   
            'email.email'=> 'Format Email Salah',   
            'alamat.required'=> 'Isi Alamat Tempat Tinggal',   
            'no_hp.required'=> 'Isi Nomor Handphone',   
            'no_hp.numeric'=> 'Nomor Handphone berupa angka',   
            'tgl_masuk.required'=> 'Isi Tanggal Masuk',    
            'nominal.required'=> 'Isi Nominal Simpanan Wajib',    
            'nominal.numeric'=> 'Isi Nominal Simpanan Wajib',   
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        $val=$validator->Errors();


        if ($validator->fails()) {
            echo'<div style="padding:1%;background:#c0f783">Error !<br>';
            foreach(parsing_validator($val) as $value){
                foreach($value as $isi){
                    echo'-&nbsp;'.$isi.'<br>';
                }
            }
            echo'</div>';
        }else{
            if($request->id==0){
                $cek=User::where('username',$request->username)->count();
                if($cek>0){
                    echo'sudah terdaftar';
                }else{
                    $save=User::create([
                        'username'=>$request->username,
                        'name'=>$request->name,
                        'email'=>$request->email,
                        'password'=>Hash::make($request->username),
                        'cost'=>Auth::user()['cost'],
                        'role_id'=>4,
                        'status'=>'Organik',
                        'sts_anggota'=>4,
                    ]);
                    if($save){
                        $data=Anggota::create([
                            'nik'=>$request->username,
                            'name'=>$request->name,
                            'karyawan'=>Auth::user()['cost'],
                            'cost'=>Auth::user()['cost'],
                            'alamat'=>$request->alamat,
                            'no_hp'=>$request->no_hp,
                            'tgl_masuk'=>$request->tgl_masuk,
                            'status'=>$request->status,
                            'nominal'=>$request->nominal,
                            'update'=>date('Y-m-d H:i:s'),
                        ]);
                        echo'ok';
                    }
                }
            }else{
                $save=User::where('username',$request->id)->update([
                    'name'=>$request->name,
                    'status'=>'Organik',
                ]);
                
                $data=Anggota::where('nik',$request->id)->update([
                    'name'=>$request->name,
                    'no_hp'=>$request->no_hp,
                    'tgl_masuk'=>$request->tgl_masuk,
                    'status'=>$request->status,
                    'nominal'=>$request->nominal,
                    'update'=>date('Y-m-d H:i:s'),
                ]);
                echo'ok';
                
            }
        }
    }

    
}
