<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\SimpananwajibController;
use App\Http\Controllers\SimpanansukarelaController;
use App\Http\Controllers\PinjamanController;
use App\Http\Controllers\RiwayatpinjamanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\TransaksipinjamanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\BarangController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/cache-clear', function() {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});
Route::get('/config-cache', function() {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Cache facade value cleared</h1>';
});
Route::get('register',[AuthController::class, 'register']);
Route::get('buatanggota',[MasterController::class, 'buatanggota']);
Route::get('get-karyawan',[MasterController::class, 'map']);
Route::post('prosesregister',[AuthController::class, 'prosesregister']);
Route::post('proseslogin',[AuthController::class, 'proseslogin']);
Route::get('login',[AuthController::class, 'login'])->name('login');
Route::post('simpan',[AuthController::class, 'simpan']);
// Route::get('/', function () {
//     return view('welcome');
// })->name('/');
// Route::get('/home', function () {
//     return view('welcome');
// })->name('/home');

Auth::routes();
Route::get('ubah_rupiah',[MasterController::class, 'ubah_rupiah']);

Route::group(['prefix' => 'barang','middleware'    => 'auth'],function(){
    Route::get('/',[BarangController::class, 'index']);
    Route::get('/tambah',[BarangController::class, 'tambah']);
    Route::get('/cari_qr',[BarangController::class, 'cari_qr']);
    Route::get('/get_data',[BarangController::class, 'get_data']);
    Route::get('/view_file',[BarangController::class, 'view_file']);
    Route::get('/cari_anggota',[BarangController::class, 'cari_anggota']);
    Route::post('/',[BarangController::class, 'save_data']);
    Route::post('/hapus',[BarangController::class, 'hapus_data']);
});

Route::group(['prefix' => 'anggota','middleware'    => 'auth'],function(){
    Route::get('/',[AnggotaController::class, 'index']);
    Route::get('/tambah',[AnggotaController::class, 'tambah']);
    Route::get('/get_import',[AnggotaController::class, 'get_import']);
    Route::get('/get_user',[AnggotaController::class, 'get_user']);
    Route::get('/cari_qr',[AnggotaController::class, 'cari_qr']);
    Route::get('/get_data',[AnggotaController::class, 'get_data']);
    Route::get('/view_file',[AnggotaController::class, 'view_file']);
    Route::get('/cari_anggota',[AnggotaController::class, 'cari_anggota']);
    Route::post('/',[AnggotaController::class, 'save_data']);
    Route::post('/hapus',[AnggotaController::class, 'hapus_data']);
});

Route::group(['middleware'    => 'auth'],function(){
    Route::get('Gaji',[GajiController::class, 'index']);
    Route::get('Gaji/cetak',[GajiController::class, 'cetak']);
    Route::post('Gaji/import_data',[GajiController::class, 'import_data']);
    
});

Route::group(['middleware'    => 'auth'],function(){
    Route::get('HOME',[HomeController::class, 'index']);
    Route::get('/',[HomeController::class, 'index']);
});

Route::group(['middleware'    => 'auth'],function(){
    Route::get('Pinjaman',[PinjamanController::class, 'index']);
    Route::get('PinjamanTransfer',[PinjamanController::class, 'index_pencairan']);
    Route::get('PinjamanTransfer/cetak',[PinjamanController::class, 'cetak_transfer']);
    Route::get('Pinjaman/approve',[PinjamanController::class, 'approve']);
    Route::Post('Pinjaman/approve',[PinjamanController::class, 'proses_approve']);
    Route::Post('Pinjaman/simpan',[PinjamanController::class, 'simpan']);
    Route::Post('Pinjaman/verifikasi_cair',[PinjamanController::class, 'verifikasi_cair']);
});
Route::group(['middleware'    => 'auth'],function(){
    Route::get('Transaksi',[TransaksiController::class, 'index']);
    Route::get('Saldopinjaman',[TransaksiController::class, 'index_pinjaman']);
    Route::get('Saldopinjaman/get_data',[TransaksiController::class, 'get_data_pinjaman']);
    Route::get('Transaksi/get_data',[TransaksiController::class, 'get_data']);
    Route::post('Transaksi/simpan',[TransaksiController::class, 'simpan']);
    Route::post('Simpanansaldo',[TransaksiController::class, 'simpan_saldo']);
});

Route::group(['middleware'    => 'auth'],function(){
    Route::get('TransaksiPinjaman',[TransaksipinjamanController::class, 'index']);
    Route::get('TransaksiPinjaman/export_excel_pinjaman',[TransaksipinjamanController::class, 'export_excel_pinjaman']);
    Route::get('TransaksiPinjaman/tampil_tagihan',[TransaksipinjamanController::class, 'tampil_tagihan']);
    Route::get('TransaksiPinjaman/view_tagihan',[TransaksipinjamanController::class, 'view_tagihan']);
    
    Route::Post('TransaksiPinjaman/proses_tagihan',[TransaksipinjamanController::class, 'proses_tagihan']);
    Route::Post('TransaksiPinjaman/proses_bayar',[TransaksipinjamanController::class, 'proses_bayar']);
    Route::Post('TransaksiPinjaman/approve',[TransaksipinjamanController::class, 'proses_approve']);
});

Route::group(['middleware'    => 'auth'],function(){
    Route::get('Riwayatpinjaman',[RiwayatpinjamanController::class, 'index']);
});
Route::group(['middleware'    => 'auth'],function(){
    Route::get('Simpananwajib',[SimpananwajibController::class, 'index']);
    Route::get('Simpananwajib/view_tagihan',[SimpananwajibController::class, 'view_tagihan']);
    Route::get('Simpananwajib/hapus_tagihan',[SimpananwajibController::class, 'hapus_tagihan']);
    Route::Post('Simpananwajib',[SimpananwajibController::class, 'save']);
    Route::Post('Simpananwajib/import',[SimpananwajibController::class, 'import_data_simpan']);
});
Route::group(['middleware'    => 'auth'],function(){
    Route::get('Simpanansukarela',[SimpanansukarelaController::class, 'index']);
    Route::get('Simpanansukarela/view_tagihan',[SimpanansukarelaController::class, 'view_tagihan']);
    Route::Post('Simpanansukarela',[SimpanansukarelaController::class, 'save']);
    Route::Post('Simpanansukarela/import',[SimpanansukarelaController::class, 'import_data_simpan']);
});

Route::get('/home', 'HomeController@index')->name('home');
