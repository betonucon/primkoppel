<?php

namespace App\Http\Controllers;
use Yajra\Datatables\Datatables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Milon\Barcode\DNS1D;
use Validator;
use App\Vanggota;
use App\Barang;
use App\VBarang;
use App\Anggota;
use App\Agt;
use App\User;
class AnggotaController extends Controller
{
    public function index(request $request){
        $menu='Anggota';
        return view('anggota.index',compact('menu'));
    }

    public function cari_qr(request $request){
        $data=Barang::where('kode_qr',$request->kode_qr)->count();
        if($data>0){
            return '@'.$data;
        }else{
            return '@0';
        }
        
    }

    public function get_import(request $request){
        $data=Agt::orderBy('no_register','Asc')->get();;
        foreach($data as $no=>$o){
            
            if($o->bulan==0){
                $bulan=01;
            }else{
                $bulan=$o->bulan;
            }
            if($o->tahun==0){
                $tahun=2020;
            }else{
                $tahun=$o->tahun;
            }
            $masuk=$tahun.'-'.$bulan.'-01';
            $no_register=date('y').sprintf("%05s",  $o->no_register);
            $save=Anggota::UpdateOrcreate([
                'no_register'=>$no_register,
            ],[
                'nama'=>$o->nama,
                'perusahaan'=>$o->perusahaan,
                'bulan'=>$bulan,
                'tahun'=>$tahun,
                'tgl_masuk'=>$masuk,
                'active'=>1,
                'created_at'=>date('Y-m-d H:i:s'),
                
            ]);
        }
        
    }
    public function get_user(request $request){
        $data=Anggota::orderBy('id','Asc')->get();;
        foreach($data as $no=>$o){
            
            $save=User::UpdateOrcreate([
                'username'=>$o->no_register,
            ],[
                'name'=>$o->nama,
                'email'=>$o->no_register.'@gmail.com',
                'role_id'=>4,
                'sts_anggota'=>1,
                'active'=>1,
                'password'=>Hash::make('admin'),
                'created_at'=>date('Y-m-d H:i:s'),
                
            ]);
        }
        
    }
    public function tambah(request $request){
        error_reporting(0);
        $id=$request->id;
        if($id!=0){
            $read='readonly';
        }else{
            $read='';
        }
        $data=Barang::where('id',$request->id)->first();
        return view('anggota.tambah',compact('data','id','read'));
    }
    public function view_file(request $request){
        error_reporting(0);
        $view='
        <img src="'.url('/public/_icon').'/'.$request->file.'" width="100%" height="300px">
        <div class="viewqr">
            '.barcoderr($request->kode_qr).'
        </div>
        ';
        return $view;
    }

    public function get_data(request $request){
        $data=VBarang::where('active',1)->orderBy('nama_barang','Asc')->get();
       
        return  Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function($data){
                    $btn='
                            <div class="btn-group btn-group-sm dropup m-r-5">
								<a href="#" data-toggle="dropdown" class="btn btn-primary btn-xs dropdown-toggle">Act <b class="caret"></b></a>
								<div class="dropdown-menu dropdown-menu-right">
									<a href="javascript:;" class="dropdown-item">Action Button</a>
									<div class="dropdown-divider"></div>
                                    <a href="javascript:;" onclick="tambah('.$data->id.')" class="dropdown-item"><i class="fas fa-pencil-alt fa-fw"></i> Ubah</a>
									<a href="javascript:;" onclick="delete_data('.$data->id.')"  class="dropdown-item"><i class="fas fa-trash-alt fa-fw"></i> Hapus</a>
									
								</div>
							</div>
                    ';
                    return $btn;
                })
                ->addColumn('file', function($data){
                    $btn='<span  class="btn btn-info btn-xs" onclick="show_foto(`'.$data->file.'`,`'.$data->kode_qr.'`)"><i class="fas fa-image"></i></span>';
                    return $btn;
                })
                ->addColumn('uang_harga_modal', function($data){
                    return uang($data->harga_modal);
                })
                ->addColumn('uang_harga_jual', function($data){
                    return uang($data->harga_jual);
                })
                
                ->rawColumns(['action','file'])
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
        
        $rules = [];
        $messages = [];
        
        $rules['nama_barang']= 'required';
        $messages['nama_anggota.required']= 'Silahkan isi nama barang';

        $rules['harga_modal']= 'required|min:0|not_in:0';
        $messages['harga_modal.required']= 'Masukan harga modal ';
        $messages['harga_modal.not_in']= 'Masukan harga modal ';
        
        $rules['harga_jual']= 'required|min:0|not_in:0';
        $messages['harga_jual.required']= 'Masukan harga jual ';
        $messages['harga_jual.not_in']= 'Masukan harga jual ';
        
        $rules['satuan']= 'required';
        $messages['satuan.required']= 'Silahkan pilih satuan';
        if($request->id==0){
            $rules['file']= 'required|mimes:jpeg,jpg,png';
            $messages['file.required']= 'Silahkan upload icon gambar';
            $messages['file.mimes']= 'Hanya menerima format (jpeg,jpg,png)';
        }else{
            if($request->file!=""){
                $rules['file']= 'required|mimes:jpeg,jpg,png';
                $messages['file.required']= 'Silahkan upload icon gambar';
                $messages['file.mimes']= 'Hanya menerima format (jpeg,jpg,png)';
            }
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        $val=$validator->Errors();


        if ($validator->fails()) {
            echo'<div style="padding:1%;background:##f3f3f3">Error !<br>';
            foreach(parsing_validator($val) as $value){
                foreach($value as $isi){
                    echo'-&nbsp;'.$isi.'<br>';
                }
            }
            echo'</div>';
        }else{
            if($request->id==0){
                $cek=User::where('username',$request->username)->count();
                $kode_barang=kode_barang();
                if($request->kode_qr==""){
                    $kode_qr=$kode_barang;
                }else{
                    $kode_qr=$request->kode_qr;
                }
                $image = $request->file('file');
                $imageFileName =$kode_anggota.'.'.$image->getClientOriginalExtension();
                $filePath =$imageFileName;
                $file =\Storage::disk('public_icon');
                if($file->put($filePath, file_get_contents($image))){
                    $save=Barang::create([
                        'kode_barang'=>$kode_barang,
                        'kode_qr'=>$kode_qr,
                        'nama_barang'=>$request->nama_barang,
                        'satuan'=>$request->satuan,
                        'harga_modal'=>ubah_uang($request->harga_modal),
                        'harga_jual'=>ubah_uang($request->harga_jual),
                        'file'=>$filePath,
                        'active'=>1,
                        'created_at'=>date('Y-m-d H:i:s'),
                        
                    ]);
                    echo'@ok';
                   
                }
                
            }else{
                $mst=Barang::where('id',$request->id)->first();
                $save=Barang::UpdateOrcreate([
                    'id'=>$request->id,
                ],[
                    'nama_barang'=>$request->nama_barang,
                    'satuan'=>$request->satuan,
                    'harga_modal'=>ubah_uang($request->harga_modal),
                    'harga_jual'=>ubah_uang($request->harga_jual),
                    'updated_at'=>date('Y-m-d H:i:s'),
                    
                ]);
                if($request->file!=""){
                    $image = $request->file('file');
                    $imageFileName =$mst->kode_anggota.'.'.$image->getClientOriginalExtension();
                    $filePath =$imageFileName;
                    $file =\Storage::disk('public_icon');
                    if($file->put($filePath, file_get_contents($image))){
                        $savefile=Barang::UpdateOrcreate([
                            'id'=>$request->id,
                        ],[
                            'file'=>$filePath,
                            'updated_at'=>date('Y-m-d H:i:s'),
                            
                        ]);
                    }
                }
                echo'@ok';
                
            }
        }
    }

    
}
