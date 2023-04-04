<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Simpanansukarela;
use Session;
use Validator;
use App\Imports\SimpanansukarelaImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
class SimpanansukarelaController extends Controller
{
    public function index(request $request){
        $menu='Simpanan Sukarela';
        return view('simpanansukarela.index',compact('menu'));
    }

    public function save(request $request){
        error_reporting(0);
        
        $rules = [
            'nik'=> 'required|numeric',
            'nominal'=> 'required|numeric',
            'bulan'=> 'required|numeric',
            'tahun'=> 'required|numeric',
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
            $cekcount=Simpanansukarela::where('tahun',$request->tahun)->count();
            if($cekcount>0){
                $cek=Simpanansukarela::where('tahun',$request->tahun)->orderBy('id','Desc')->firstOrfail();
                $urutan = (int) substr($cek['nomortransaksi'], 8, 5);
                $urutan++;
                $nomor='SS'.date('Ym').sprintf("%05s", $urutan);
                
            }else{
                $nomor='SS'.date('Ym').sprintf("%05s", 1);
            }
            $cekdata=Simpanansukarela::where('tahun',$request->tahun)->where('bulan',$request->bulan)->where('nik',$request->nik)->count();
            if($cekdata>0){
                echo'ok';
            }else{
                $record=Simpanansukarela::create([
                    'nomortransaksi'=>$nomor,
                    'nik'=>$request->nik,
                    'bulan'=>$request->bulan,
                    'tahun'=>$request->tahun,
                    'nominal'=>$request->nominal,
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
            Excel::import(new SimpanansukarelaImport, public_path('/file_excel/'.$nama_file));
            echo'ok';
        }else{
            echo' Format file upload harus excel';
        }
    }

    public function view_tagihan(request $request){
        $data=Simpanansukarela::where('nik',$request->nik)->orderBy('id','Desc')->get();
        echo'    
            <table class="table table-striped table-bordered table-td-valign-middle" id="dataheader">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Nomor</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Nominal</th>
                    </tr>
                </thead>
                <tbody>';
                
                    foreach($data as $no=>$o){
                        echo'
                            <tr>
                                <td>'.($no+1).'</td>
                                <td>'.$o['nomortransaksi'].'</td>
                                <td>'.bulan($o['bulan']).'</td>
                                <td>'.$o['tahun'].'</td>
                                <td>'.uang($o['nominal']).'</td>
                                
                            </tr>
                        ';
                    }
                
                    
                echo'    
                </tbody>
            </table>
        ';
    }
}
