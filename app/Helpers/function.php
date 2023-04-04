<?php

function uang($nilai){
   return number_format($nilai,0);

}
function aplikasi()
{
   $data=App\Cost::where('id',Auth::user()['cost'])->first();
   return $data['name'];
}
function name_app()
{
   $data=App\Cost::where('id',Auth::user()['cost'])->first();
   return $data['name'];
}
function bulan($bulan)
{
   Switch ($bulan){
      case '01' : $bulan="Januari";
         Break;
      case '02' : $bulan="Februari";
         Break;
      case '03' : $bulan="Maret";
         Break;
      case '04' : $bulan="April";
         Break;
      case '05' : $bulan="Mei";
         Break;
      case '06' : $bulan="Juni";
         Break;
      case '07' : $bulan="Juli";
         Break;
      case '08' : $bulan="Agustus";
         Break;
      case '09' : $bulan="September";
         Break;
      case 10 : $bulan="Oktober";
         Break;
      case 11 : $bulan="November";
         Break;
      case 12 : $bulan="Desember";
         Break;
      }
   return $bulan;
}
function bulan_kedepan($tanggal,$lama)
{
   $tgl=explode(' ',$tanggal);
   $hari=$lama;
   $kedepan = date('Y-m-27', strtotime("$hari Month", strtotime($tgl[0])));
   return  $kedepan;
}
function ubah_bulan($id){
   if($id>9){
      $data=$id;
   }else{
      $data='0'.$id;
   }
   return $data;
}

function get_user(){
   $data=App\User::orderBy('sts_anggota','Asc')->get();
   return $data;
}
function get_status(){
   $data=App\Status::orderBy('id','Asc')->get();
   return $data;
}
function total_anggota(){
   $data=App\Anggota::where('cost',Auth::user()['cost'])->where('status',1)->count();
   return $data;
}
function notifikasi_pengajuan(){
   if(Auth::user()['role_id']==1){
      $app=App\Pinjaman::where('sts_pinjaman',3)->count();
      
   }
   if(Auth::user()['role_id']==2){
      $app=App\Pinjaman::where('sts_pinjaman',2)->count();
      
   }

   if($app>0){
      $data='<span class="badge pull-right">'.$app.'</span>'; 
   }else{
      $data='';
   }
   
   return $data;
}
function notifikasi_pencairan(){
   if(Auth::user()['role_id']==1){
      $app=App\Pinjaman::where('sts_pinjaman',4)->where('sts_pencairan',1)->count();
      
   }
   if(Auth::user()['role_id']==2){
      $app=App\Pinjaman::where('sts_pinjaman',4)->where('sts_pencairan',1)->count();
      
   }

   if($app>0){
      $data='<span class="badge pull-right">'.$app.'</span>'; 
   }else{
      $data='';
   }
   
   return $data;
}

function bulanberikut($tanggal,$waktu){
   $exp=explode('-',$tanggal);
   $tgl=$exp[0].'-'.$exp[1].'-27';
   $bulan = date('F Y', strtotime('+'.$waktu.' month', strtotime($tgl)));
   return $bulan;
}

function tgl_tagihan($tanggal,$waktu){
   $exp=explode('-',$tanggal);
   $tgl=$exp[0].'-'.$exp[1].'-27';
   $bulan = date('Y-m-27', strtotime('+'.$waktu.' month', strtotime($tgl)));
   return $bulan;
}

function pinjaman_sebelumnya($nik){
   $data=App\Pinjaman::where('sts_pinjaman','4')->where('nik',$nik)->count();
   if($data>0){
      $pinjaman=App\Pinjaman::where('sts_pinjaman','4')->where('nik',$nik)->first();
      $bayar=App\Periodepinjaman::where('nomorpinjaman',$pinjaman['nomorpinjaman'])->where('sts',0)->count();
      $sts=$bayar;
   }else{
      $sts='Tidak ada pinjaman';
   }

   return $sts;
}

function total_pinjaman_sebelumnya($nik){
   $data=App\Pinjaman::where('sts_pinjaman','4')->where('nik',$nik)->count();
   if($data>0){
      $pinjaman=App\Pinjaman::where('sts_pinjaman','4')->where('nik',$nik)->first();
      $sts=($pinjaman['nominal']-$pinjaman['terbayar'])+$pinjaman['bunga'];
   }else{
      $sts='0';
   }

   return $sts;
}

function total_saldo_pinjaman($tahun){
   $data=App\Pembayaranpinjaman::where('tahun',$tahun)->count();
   if($data>0){
      $masuk=App\Pembayaranpinjaman::where('tahun',$tahun)->where('sts',1)->sum('pinjaman');
      $keluar=App\Pembayaranpinjaman::where('tahun',$tahun)->where('sts',2)->sum('pinjaman');
      $sts=($masuk-$keluar);
   }else{
      $sts='0';
   }

   return $sts;
}
function total_bagihasil($tahun){
   $data=App\Pembayaranpinjaman::where('tahun',$tahun)->where('sts',1)->count();
   if($data>0){
      $masuk=App\Pembayaranpinjaman::where('tahun',$tahun)->where('sts',1)->sum('margin');
      $sts=$masuk;
   }else{
      $sts='0';
   }

   return $sts;
}


function count_sisabayar($kode){
  
   $bayar=App\Periodepinjaman::where('nomorpinjaman',$kode)->where('sts',0)->count();
   if($bayar>0){
      $sts=$bayar.'X';
   }else{
      $sts='Lunas';
   }
   return $sts;
}
function sum_transaksi($tahun,$bulan,$kat){
  
   $bayar=App\Transaksi::where('bulan',$bulan)->where('tahun',$tahun)->where('sts',$kat)->count();
   if($bayar>0){
      $tot=App\Transaksi::where('bulan',$bulan)->where('tahun',$tahun)->where('sts',$kat)->sum('margin');
      $sts=$tot;
   }else{
      $sts=0;
   }
   return $sts;
}
function get_slid(){
   $data=App\Slipgaji::select('tahun')->groupBy('tahun')->orderBy('tahun','Desc')->get();
   return $data;
}
function ubah_int($nilai){
   $patr='/([^0-9]+)/';
   $data=preg_replace($patr,'',$nilai);
   return $data;
}
function cek_user($nik){
   $data=App\Anggota::where('nik',$nik)->count();
   return $data;
}
function get_slip_gaji($tahun,$bulan){
   $data=App\Slipgaji::where('bulan',$bulan)->where('tahun',$tahun)->get();
   return $data;
}
function sum_transaksi_tahunan($tahun,$kat){
  
   $bayar=App\Transaksi::where('tahun',$tahun)->where('sts',$kat)->count();
   if($bayar>0){
      $tot=App\Transaksi::where('tahun',$tahun)->where('sts',$kat)->sum('margin');
      $sts=$tot;
   }else{
      $sts=0;
   }
   return $sts;
}

function get_pengajuan(){
   if(Auth::user()['role_id']==1){
      $data=App\Pinjaman::where('sts_pinjaman','3')->orderBy('sts_pinjaman','Asc')->get();
   }
   if(Auth::user()['role_id']==2){
      $data=App\Pinjaman::where('sts_pinjaman','2')->orderBy('sts_pinjaman','Asc')->get();
   }
   if(Auth::user()['role_id']==3){
      $data=App\Pinjaman::where('sts_pinjaman','1')->orderBy('sts_pinjaman','Asc')->get();
   }
   
   return $data;
}

function get_pengajuan_cair(){
   $data=App\Pinjaman::where('sts_pinjaman','4')->where('sts_pencairan',1)->orderBy('sts_pinjaman','Asc')->get();
   
   return $data;
}

function saldo_simpananwajib($nik){
   $cek=App\Simpananwajib::where('nik',$nik)->count();
   if($cek>0){
      $masuk=App\Simpananwajib::where('nik',$nik)->where('sts',1)->sum('nominal');
      $keluar=App\Simpananwajib::where('nik',$nik)->where('sts',2)->sum('nominal');
      $data=$masuk-$keluar;
   }else{
      $data='0';
   }
   return $data;
}
function total_simpananwajib(){
   $data=App\Simpananwajib::sum('nominal');
   
   return $data;
}

function get_simpananwajib(){
   $data=App\Simpananwajib::select('nik')->where('cost',Auth::user()['cost'])->groupBy('nik')->orderBy('nik','Asc')->get();
   
   return $data;
}
function saldo_simpanansukarela($nik){
   $data=App\Simpanansukarela::where('nik',$nik)->sum('nominal');
   
   return $data;
}

function get_simpanansukarela(){
   $data=App\Simpanansukarela::select('nik')->groupBy('nik')->orderBy('nik','Asc')->get();
   
   return $data;
}
function get_pengajuan_barucair(){
   $data=App\Pinjaman::where('sts_pinjaman','4')->where('sts_pencairan',2)->where('tgl_cair',date('Y-m-d'))->orderBy('sts_pinjaman','Asc')->get();
   
   return $data;
}

function parsing_validator($url){
   $content=utf8_encode($url);
   $data = json_decode($content,true);

   return $data;
}

function get_pinjaman(){
   
  $data=App\Pinjaman::where('sts_pinjaman','4')->where('sts_pencairan','>',1)->orderBy('nomorpinjaman','Asc')->get();
   
   return $data;
}

function kode_kategori($id){
   
  $data=App\Kategori::where('id',$id)->first();
   
   return $data['kode'];
}

function get_transaksi($tahun,$kategori){
  if($kategori=='all'){
      $data=App\Transaksi::where('tahun',$tahun)->orderBy('id','Desc')->get();
  }
  else{
      $data=App\Transaksi::where('tahun',$tahun)->where('kategori_id',$kategori)->orderBy('id','Desc')->get();
  }
  
   
   return $data;
}

function masuk_transaksi($tahun){
   
  $cek=App\Transaksi::where('tahun',($tahun-1))->where('sts',1)->whereIn('kategori_id',array('1','6'))->count();
  if($cek>0){
      $data=App\Transaksi::where('tahun',$tahun)->where('sts',1)->whereIn('kategori_id',array('1','6'))->sum('nominal');
      $saldo=$data;
  }else{
      $data=App\Transaksi::where('tahun',$tahun)->where('sts',1)->sum('nominal');
      $saldo=$data;
  }
  
   
   return $saldo;
}

function keluar_transaksi($tahun){
   
  $data=App\Transaksi::where('tahun',$tahun)->where('sts',2)->whereIn('kategori_id',array('1','6'))->sum('nominal');
   
   return $data;
}

function margin_transaksi($tahun){
   
  $data=App\Transaksi::where('tahun',$tahun)->sum('margin');
   
   return $data;
}

function get_tahun_transaksi(){
   
  $data=App\Transaksi::select('tahun')->groupBy('tahun')->get();
   
   return $data;
}

function get_kategori(){
   
  $data=App\Kategori::all();
   
   return $data;
}

function get_kategori_transaksi(){
   
  $data=App\Kategori::whereIn('id',array('5','6'))->get();
   
   return $data;
}

function bln($id){
   if($id>9){
      $data=$id;
   }else{
      $data='0'.$id;
   }

   return substr(bulan($data),0,3);
}

function blnfull($id){
   if($id>9){
      $data=$id;
   }else{
      $data='0'.$id;
   }

   return bulan($data);
}



?>