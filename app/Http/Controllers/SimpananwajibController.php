<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Simpananwajib;
use Session;
use Validator;
use App\Imports\SimpananwajibImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
class SimpananwajibController extends Controller
{
    public function index(request $request){
        $menu='Simpanan wajib';
        return view('simpananwajib.index',compact('menu'));
    }

    public function save(request $request){
        error_reporting(0);
        
        $rules = [
            'nik'=> 'required|numeric',
            'nominal'=> 'required|numeric',
            'bulan'=> 'required|numeric',
            'tahun'=> 'required|numeric',
            'sts'=> 'required|numeric',
        ];

        $messages = [
            'nik.required'=> 'Isi NIK Karyawan', 
            'nik.numeric'=> 'NIK Hanya Numeric', 
            'nominal.required'=> 'Isi Nominal transaksi', 
            'nominal.numeric'=> 'Nominal transaksi Hanya Numeric', 
            'bulan.required'=> 'Pilih bulan', 
            'bulan.numeric'=> 'Bulan Hanya Angka', 
            'tahun.required'=> 'Pilih tahun', 
            'tahun.numeric'=> 'Tahun Hanya Angka',
            'sts.required'=> 'Pilih Kategori',
            
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        $val=$validator->Errors();


        if ($validator->fails()) {
            
            foreach(parsing_validator($val) as $value){
                foreach($value as $isi){
                    echo'-&nbsp;'.$isi.'<br>';
                }
            }
        }else{
            $cekcount=Simpananwajib::where('tahun',$request->tahun)->count();
            if($cekcount>0){
                $cek=Simpananwajib::where('tahun',$request->tahun)->orderBy('id','Desc')->firstOrfail();
                $urutan = (int) substr($cek['nomortransaksi'], 6, 5);
                $urutan++;
                $nomor=date('Ym').sprintf("%05s", $urutan);
                
            }else{
                $nomor=date('Ym').sprintf("%05s", 1);
            }
            $cekdata=Simpananwajib::where('sts',$request->sts)->where('tahun',$request->tahun)->where('bulan',$request->bulan)->where('nik',$request->nik)->count();
            if($cekdata>0){
                echo'ok';
            }else{
                if($request->sts==1){
                    $nominal=$request->nominal;
                }else{
                    $nominal=saldo_simpananwajib($request->nik);
                }
                $record=Simpananwajib::create([
                    'nomortransaksi'=>$nomor,
                    'nik'=>$request->nik,
                    'bulan'=>$request->bulan,
                    'tahun'=>$request->tahun,
                    'nominal'=>$nominal,
                    'sts'=>$request->sts,
                    'cost'=>Auth::user()['cost'],
                ]);

                echo'ok';
            }
                

        }
    }

    public function import_data_simpan(request $request)
    {
       error_reporting(0);
		
        $filess = $request->file('file');
        
        if($filess->getClientOriginalExtension()=='xlsx'){
           
            $nama_file = rand().$filess->getClientOriginalName();
            $filess->move('file_excel',$nama_file);
            Excel::import(new SimpananwajibImport, public_path('/file_excel/'.$nama_file));
            echo'ok';
        }else{
            echo' Format file upload harus excel';
        }
    }

    public function hapus_tagihan(request $request){
        $data=Simpananwajib::where('id',$request->id)->delete();
        echo $request->nik;
    }
    public function view_tagihan(request $request){
        $data=Simpananwajib::where('nik',$request->nik)->orderBy('id','Asc')->get();
        echo'    
            <table class="table table-striped table-bordered table-td-valign-middle" id="dataheader">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Nomor</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Nominal</th>
                        <th width="5%">Act</th>
                    </tr>
                </thead>
                <tbody>';
                
                    foreach($data as $no=>$o){
                        if($o['sts']==1){
                            $color="#000";
                        }else{
                            $color="red";
                        }
                        echo'
                            <tr>
                                <td>'.($no+1).'</td>
                                <td>'.$o['nomortransaksi'].'</td>
                                <td>'.bulan($o['bulan']).'</td>
                                <td>'.$o['tahun'].'</td>
                                <td style="color:'.$color.'">'.uang($o['nominal']).'</td>
                                <td><span class="btn btn-xs btn-red" onclick="hapus_tagihan('.$o['id'].',`'.$o['nik'].'`)"><i class="fas fa-window-close"></i></span></td>
                                
                            </tr>
                        ';
                    }
                
                    
                echo'    
                </tbody>
            </table>
        ';
    }
}
