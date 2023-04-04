<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Pinjaman;
use App\Transaksi;
use App\Periodepinjaman;
use App\Pembayaranpinjaman;
use Validator;
use PDF;
class PinjamanController extends Controller
{
    public function index(request $request){
        $menu='Pengajuan Pinjaman';
        if(Auth::user()['role_id']==1){
            return view('pinjaman.index_lv3',compact('menu'));
        }
        if(Auth::user()['role_id']==2){
            return view('pinjaman.index_lv2',compact('menu'));
        }
        if(Auth::user()['role_id']==3){
            return view('pinjaman.index',compact('menu'));
        }
        dd(Auth::user()['role_id']);
    }
    public function index_pencairan(request $request){
        $menu='Pengajuan Pinjaman';
        if(Auth::user()['role_id']==1){
            return view('pinjaman.index_lv3',compact('menu'));
        }
        if(Auth::user()['role_id']==2){
            return view('pinjaman.index_pencairan',compact('menu'));
        }
        if(Auth::user()['role_id']==3){
            return view('pinjaman.index',compact('menu'));
        }
        
    }

    public function proses_approve(request $request){
        if(Auth::user()['role_id']==1){
            $pengajuan=Pinjaman::find($request->id);
            if($request->sts_pinjaman==''){
                echo'- Pilih Status Approve';
            }else{
                if($request->sts_pinjaman=='0'){
                    $keterangan='Pinjaman ditolak oleh ketua koperasi';
                    $data=Pinjaman::where('id',$request->id)->update([
                        'sts_pinjaman'=>$request->sts_pinjaman,
                        'keterangan'=>$keterangan,
                    ]);
                    echo'ok';
                }else{
                    $keterangan='Acc';
                    $pinjaman=Pinjaman::find($request->id);
                    $cektransaksi=Transaksi::where('kodetransaksi',$pinjaman['nomorpinjaman'])->count();
                    if($cektransaksi>0){
                        echo'ok';
                    }else{
                        $transaksi=Transaksi::create([
                            'kodetransaksi'=>$pinjaman['nomorpinjaman'],
                            'kategori_id'=>1,
                            'name'=>'Peminjaman dana untuk '.$pinjaman->user['name'],
                            'bulan'=>date('m'),
                            'tahun'=>date('Y'),
                            'tanggal'=>date('Y-m-d'),
                            'nominal'=>$pinjaman['nominal'],
                            'sts'=>2,
                        ]);

                        if($transaksi){
                            
                            $data=Pinjaman::where('id',$request->id)->update([
                                'sts_pinjaman'=>$request->sts_pinjaman,
                                'keterangan'=>$keterangan,
                            ]);

                            if($data){
                                $sebelumnya=Pinjaman::where('nomorpinjaman',$pinjaman['nomorpinjamanaktive'])->update([
                                    'sts_pinjaman'=>5,
                                ]);
                                

                                echo'ok';
                            }
                            
                        }
                    }
                }
                

                
                
            }
        }
        if(Auth::user()['role_id']==2){
            $pengajuan=Pinjaman::find($request->id);
            if($request->sts_pinjaman==''){
                echo'- Pilih Status Approve00000';
            }else{
                if($request->sts_pinjaman=='0'){
                    $keterangan='Pinjaman sebelumnya harus tersisa setengah cicilan';
                }else{
                    $keterangan='Acc';
                }
                $data=Pinjaman::where('id',$request->id)->update([
                    'sts_pinjaman'=>$request->sts_pinjaman,
                    'keterangan'=>$keterangan,
                    'nominal_cair'=>($pengajuan['nominal']-total_pinjaman_sebelumnya($pengajuan['nik'])),
                    'nomorpinjamanaktive'=>$request->nomorpinjamanaktive,
                ]);
                echo'ok';
            }
        }
        if(Auth::user()['role_id']==3){
            if($request->sts_pinjaman==''){
                echo'- Masukan Tanggal akhir kontrak';
            }else{
                if($request->sts_pinjaman=='0'){
                    $keterangan='Masa kontrak tidak mencukupi';
                }else{
                    $keterangan='Acc';
                }
                $data=Pinjaman::where('id',$request->id)->update([
                    'sts_pinjaman'=>$request->sts_pinjaman,
                    'keterangan'=>$keterangan,
                ]);
                echo'ok';
            }
        }
            
    }

    public function approve(request $request){
        $data=Pinjaman::find($request->id);
        $cekp=Pinjaman::where('sts_pinjaman','4')->where('nik',$data['nik'])->count();
        if($cekp>0){
            $pinjaman=Pinjaman::where('sts_pinjaman','4')->where('nik',$data['nik'])->first();
            $nomor=$pinjaman['nomorpinjaman'];
        }else{
            $nomor='0';
        }
        if(Auth::user()['role_id']==1){
            echo'
                <input type="hidden" name="id" value="'.$data['id'].'">
                <fieldset>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Karyawan</label>
                        <input type="text" disabled class="form-control" id="exampleInputEmail1" value="['.$data['nik'].'] '.$data->user['name'].'" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Status Karyawan</label>
                        <input type="text" disabled class="form-control" id="exampleInputEmail1" value="'.$data->user['status'].'" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Pinjaman Sebelumnya</label>
                        <input type="text" disabled class="form-control" value="'.pinjaman_sebelumnya($data['nik']).'" placeholder="Enter email">
                        <input type="hidden" name="kat" class="form-control" value="'.pinjaman_sebelumnya($data['nik']).'" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <table width="100%">
                            <tr>
                                <td>
                                    <label for="exampleInputEmail1">Pinjaman</label>
                                    <input type="text" disabled class="form-control" value="'.uang($data['nominal']).'" >
                                </td>
                                <td>
                                    <label for="exampleInputEmail1">Jangka Waktu</label>
                                    <input type="text" disabled class="form-control" value="'.$data['waktu'].' Bulan" >
                                </td>
                            </tr>
                        </table>
                        
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Pilih Status Approv</label>
                        <select class="form-control" name="sts_pinjaman">
                            <option value="">Pilih-----</option>
                            <option value="4">Setujui</option>
                            <option value="0">Tolak</option>
                        </select>
                    </div><br>
                   
                    <span onclick="verifikasi_pinjaman()" class="btn btn-sm btn-primary m-r-5">Approve</span>
                </fieldset>

            ';
        }
        if(Auth::user()['role_id']==2){
            echo'
                <input type="hidden" name="id" value="'.$data['id'].'">
                <fieldset>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Karyawan</label>
                        <input type="text" disabled class="form-control" id="exampleInputEmail1" value="['.$data['nik'].'] '.$data->user['name'].'" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-8">
                                <label for="exampleInputEmail1">Pinjaman diajukan</label>
                                <input type="text" disabled class="form-control" value="'.uang($data['nominal']).'" placeholder="Enter email">
                            </div>
                            <div class="col-md-4">
                                <label for="exampleInputEmail1">Cicil</label>
                                <input type="text" disabled class="form-control" value="'.$data['waktu'].' X" placeholder="Enter email">
                            </div>
                        </div>
                        
                    </div>';
                    if($nomor=='0'){
                        echo'<input type="hidden" name="nomorpinjamanaktive" class="form-control" value="'.$nomor.'" placeholder="Enter email">';
                    }else{
                        echo'
                        
                        <div class="form-group">
                            
                            <table width="100%">
                                <tr>
                                    <td width="30%">
                                        <label for="exampleInputEmail1">No Pinjaman Berjalan</label>
                                        <input type="text" disabled class="form-control" value="'.$nomor.'" >
                                    </td>
                                    <td>
                                        <label for="exampleInputEmail1">Total Pinjaman Berjalan</label>
                                        <input type="text" disabled class="form-control" value="'.uang(total_pinjaman_sebelumnya($data['nik'])).'" placeholder="Enter email">
                                        <input type="hidden" name="nomorpinjamanaktive" class="form-control" value="'.$nomor.'" placeholder="Enter email">
                                    </td>
                                    <td>
                                        <label for="exampleInputEmail1">Nominal dicairkan</label>
                                        <input type="text" disabled class="form-control" value="'.uang($data['nominal']-total_pinjaman_sebelumnya($data['nik'])).'" >
                                    </td>
                                </tr>
                            </table>
                            
                        </div>

                        ';
                    }
                    echo'
                    
                    <div class="form-group">
                        <label for="exampleInputPassword1">Pilih Status Approv</label>
                        <select class="form-control" name="sts_pinjaman">
                            <option value="">Pilih-----</option>
                            <option value="3">Setujui</option>
                            <option value="0">Tolak</option>
                        </select>
                    </div><br>
                   
                    <span onclick="verifikasi_pinjaman()" class="btn btn-sm btn-primary m-r-5">Approve</span>
                </fieldset>

            ';
        }
        if(Auth::user()['role_id']==3){
            echo'
                <input type="hidden" name="id" value="'.$data['id'].'">
                <fieldset>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Karyawan</label>
                        <input type="text" disabled class="form-control" id="exampleInputEmail1" value="['.$data['nik'].'] '.$data->user['name'].'" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Akhir Pinjaman</label>
                        <input type="text" disabled class="form-control" id="tgl_akhir" value="'.bulanberikut($data['tgl_pengajuan'],$data['waktu']).'" placeholder="Enter email">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Tanggal Akhir Kontrak</label>
                        <input type="text" class="form-control" id="tanggaledit" placeholder="yyyy-mm-dd">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Verifikasi</label>
                        <input type="text" class="form-control" id="statuskontrak" disabled>
                        
                    </div>
                    <div class="form-group">
                        <label for="exampleInputPassword1">Pilih Status Approv</label>
                        <select class="form-control" name="sts_pinjaman">
                            <option value="">Pilih-----</option>
                            <option value="2">Setujui</option>
                            <option value="0">Tolak</option>
                        </select>
                    </div><br>
                    <span onclick="verifikasi_pinjaman()" class="btn btn-sm btn-primary m-r-5">Approve</span>
                </fieldset>

            ';
        }

        echo'
            <script>
                $(document).ready(function () {
                    var tgl_akhir=$("#tgl_akhir").val();
                    $("#tanggaledit").datepicker({
                        
                        format: "yyyy-mm-dd",
                        autoclose:true,
                        
                        
                    }).on("change", function() {
                        if(this.value>tgl_akhir){
                            $("#statuskontrak").val("Lulus Verifikasi");
                            $("#sts_peminjaman").val(2);
                        }else{
                            $("#statuskontrak").val("Tidak Lolos ");
                            $("#sts_peminjaman").val(0);
                        }
                        
                    });
                });
            </script>
            
        ';
    }

    public function cetak_transfer(request $request){
        $data=Pinjaman::where('sts_pinjaman','4')->where('sts_pencairan',1)->orderBy('sts_pinjaman','Asc')->get();
        $pdf = PDF::loadView('pinjaman.cetak_transfer', ['data'=>$data]);
        $pdf->setPaper('A4', 'Potrait');
        return $pdf->stream();
    }

    public function verifikasi_cair(request $request){
        error_reporting(0);
        $count=count($request->id);
        if($count>0){
            for($x=0;$x<$count;$x++){
                $data=Pinjaman::find($request->id[$x]);
                if($data['nomorpinjamanaktive']=='0' || $data['nomorpinjamanaktive']==''){
                    $pinjaman=Pinjaman::where('id',$request->id[$x])->update([
                        'sts_pencairan'=>2,
                        'berikutnya'=>bulan_kedepan(date('Y-m-d'),1),
                        'tgl_cair'=>date('Y-m-d'),
                        'terbayar'=>0,
                    ]);
                    
                }else{
                    $pinjaman=Pinjaman::where('id',$request->id[$x])->where('sts_pencairan',1)->update([
                        'sts_pencairan'=>2,
                        'berikutnya'=>bulan_kedepan(date('Y-m-d'),1),
                        'tgl_cair'=>date('Y-m-d'),
                        'terbayar'=>0,
                    ]);
                    
                    $dataaktive=Pinjaman::where('nomorpinjaman',$data['nomorpinjamanaktive'])->first();
                    $updateperiodeaktive=Pinjaman::where('nomorpinjaman',$data['nomorpinjamanaktive'])->update([
                        'sts_pinjaman'=>5,
                        'terbayar'=>($dataaktive['nominal']+$dataaktive['terbayar']),
                        'jumlah_dibayar'=>$dataaktive['waktu'],
                    ]);

                    $pelunasan=Pembayaranpinjaman::create([
                        'nik'=>$data['nik'],
                        'bulan'=>date('m'),
                        'tahun'=>date('Y'),
                        'total'=>($dataaktive['nominal']-$dataaktive['terbayar']),
                        'pinjaman'=>($dataaktive['nominal']-$dataaktive['terbayar']),
                        'margin'=>$dataaktive['bunga'],
                        'cost'=>Auth::user()['cost'],
                        'tanggal'=>date('Y-m-d'),
                        'pinjaman_id'=>$dataaktive['id'],
                        'angsuran'=>$dataaktive['waktu'],
                        'sts'=>1,
                        'kategori'=>1,
                        'name'=>'Pelunasan Pinjaman '.$data->user['name'],
                    ]);
                        
                    
                }

                $bayar=Pembayaranpinjaman::create([
                    'nik'=>$data['nik'],
                    'bulan'=>date('m'),
                    'tahun'=>date('Y'),
                    'total'=>$data['nominal_cair'],
                    'pinjaman'=>$data['nominal_cair'],
                    'margin'=>'0',
                    'cost'=>Auth::user()['cost'],
                    'tanggal'=>date('Y-m-d'),
                    'pinjaman_id'=>$data['id'],
                    'angsuran'=>'0',
                    'sts'=>2,
                    'kategori'=>1,
                    'name'=>'Pinjaman '.$data->user['name'],
                ]);
                
            }
            echo'ok';
        }else{
            echo'Pilih data pinjaman yang akan diproses pencairannya';
        }
    }
    public function simpan(request $request){
        error_reporting(0);
        
        $rules = [
            'nik'=> 'required|numeric',
            'nominal'=> 'required|numeric',
            'tgl_pengajuan'=> 'required|date',
            'bulan'=> 'required|numeric',
            'tahun'=> 'required|numeric',
            'waktu'=> 'required|numeric',
        ];

        $messages = [
            'nik.required'=> 'Isi Nik Karyawan', 
            'nik.numeric'=> 'NIK Hanya Numeric', 
            'nominal.required'=> 'Isi Nominal transaksi', 
            'nominal.numeric'=> 'Nominal transaksi Hanya Numeric', 
            'tgl_pengajuan.required'=> 'Isi Tanggal Pengajuan ', 
            'tgl_pengajuan.date'=> 'Tanggal Pengajuan yyyy-mm-dd',  
            'bulan.required'=> 'Pilih bulan', 
            'bulan.numeric'=> 'Bulan Hanya Angka', 
            'tahun.required'=> 'Pilih tahun', 
            'tahun.numeric'=> 'Tahun Hanya Angka',  
            'waktu.required'=> 'Pilih Jangka waktu', 
            'waktu.numeric'=> 'Jangka Waktu Hanya Angka',  
            
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
            // $cekcount=Transaksi::where('tahun',$request->tahun)->count();
            // if($cekcount>0){
            //     $cek=Transaksi::where('tahun',$request->tahun)->orderBy('id','Desc')->firstOrfail();
            //     $urutan = (int) substr($cek['kodetransaksi'], 8, 4);
            //     $urutan++;
            //     $nomor=kode_kategori($request->kategori_id).date('Ym').sprintf("%04s", $urutan);
               
            // }else{
            //     $nomor=kode_kategori($request->kategori_id).date('Ym').sprintf("%04s", 1);
            // }
            // $transaksi=Transaksi::create([
            //     'kodetransaksi'=>$nomor,
            //     'kategori_id'=>$request->kategori_id,
            //     'name'=>$request->name,
            //     'bulan'=>$request->bulan,
            //     'tahun'=>$request->tahun,
            //     'tanggal'=>$request->tanggal,
            //     'nominal'=>$request->nominal,
            //     'margin'=>0,
            //     'sts'=>$request->sts,
            // ]);
        }
    }
}
