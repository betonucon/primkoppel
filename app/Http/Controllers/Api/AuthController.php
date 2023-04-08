<?php
   
namespace App\Http\Controllers\Api;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\User;
use App\VUser;
use App\Accesstoken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Validator;
   
class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        error_reporting(0);
        $rules = [];
        $messages = [];
        $rules['nama'] = 'required';
        $rules['email'] = 'required|email|unique:users';
        $rules['phone'] = 'required|numeric|unique:users,username';
        $rules['Kd_Propinsi'] = 'required';
        $rules['Kd_Kabupaten'] = 'required';
        $rules['password'] = 'required|string|min:8|confirmed';

        $messages['nama.required'] = 'Lengkapi kolom nama';
        $messages['email.required'] = 'Lengkapi email';
        $messages['email.email'] = 'Format email tidak sesuai';
        $messages['email.unique'] = 'Email sudah terdaftar';

        $messages['phone.required'] = 'Lengkapi nomor handphone';
        $messages['phone.numeric'] = 'Format phone harus angka';
        $messages['phone.unique'] = 'Nomor handphone sudah terdaftar';

        $messages['Kd_Propinsi.required'] = 'Lengkapi Provinsi';
        $messages['Kd_Kabupaten.required'] = 'Lengkapi Kota';

        $messages['password.required'] = 'Lengkapi kolom password';
        $messages['password.min'] = 'Minimal 8 Karakter';
        $messages['password.confirmed'] = 'Konfirmasi password salah';

        $validator = Validator::make($request->all(), $rules,$messages);
        $val=$validator->Errors();
        if($validator->fails()){
            $error="";
            foreach(parsing_validator($val) as $value){
                foreach($value as $isi){
                   $error.=$isi."\n";
                }
            }
            return $this->sendResponseerror($error);
            // $success['error']='error';
            // return $this->sendResponse($success, $validator->errors());      
        }else{
            $kode_customer=kode_customer();
            $user       = New User;
            $user->name = $request->nama;
            $user->email = $request->email;
            $user->username = $request->phone;
            $user->password = Hash::make($request->password);
            $user->active_status =1;
            $user->role_id =4;
            $user->save();
            if($user){
                
                
                $guest=Mobilecustomer::UpdateOrcreate([
                    'kode_customer'=>$kode_customer,
                    
                ],[
                    'users_id'=>$user->id,
                    'nama_customer'=>$request->nama,
                    'email'=>$request->email,
                    'phone'=>$request->phone,
                    'Kd_Propinsi'=>$request->Kd_Propinsi,
                    'Kd_Kabupaten'=>$request->Kd_Kabupaten,
                    'foto'=>'akun.png',
                    'tahun'=>date('Y'),
                    'created_at'=>date('Y-m-d H:i:s')
                ]);
                
            }

            // $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            // $success['name'] =  $user->name;
   
            $success=[];
            return $this->sendResponse($success, 'success');
        }
   
        
    }

    public function login(Request $request)
    {
        error_reporting(0);
        if(Auth::attempt(['email' => $request->username, 'password' => $request->password])){ 
            $auth = Auth::user(); 
            $user = VUser::where('username',$auth->username)->first(); 
           
                if($auth->aktif==1){
                    if($user->pinjaman_aktif>0){
                        $pinjamanaktif=true;
                    }else{
                        $pinjamanaktif=false;
                    }
                    if($user->role_id==5){
                        $anggota=false;
                    }else{
                        $anggota=true;
                    }
                    $success=[];
                    $berier=$auth->createToken('MyApp')->plainTextToken;
                    $token=explode('|',$berier);
                    $success['token'] =  $berier; 
                    $success['nama'] =  $user->name;
                    $success['nik'] =  $user->username;
                    $success['nik'] =  $user->username;
                    $success['sts_anggota'] =  $user->sts_anggota;
                    $success['saldo_wajib'] =  $user->saldo_wajib;
                    $success['saldo_sukarela'] =  $user->saldo_sukarela;
                    $success['pinjaman_aktif'] =  $pinjamanaktif;
                    
                    return $this->sendResponse($success, 'User login successfully.');
                }else{
                   
                        $error='Akun anda telah dibekukan';
                        return $this->sendResponseerror($error);
                    
                }
            
            
        } 
        else{ 
            $error='username atau password anda salah';
            return $this->sendResponseerror($error);
        } 
    }

    public function cek_login(Request $request)
    {
        error_reporting(0);
        if(Auth::attempt(['email' => $request->username, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $cek=Accesstoken::where('tokenable_id',$user->id)->count();
            if($cek>0){
                $success=false;
                return $this->sendResponselogin($success);
                
            }else{
                $success=true;
                return $this->sendResponselogin($success);
            }
            
        } 
        else{ 
            $success=true;
            return $this->sendResponselogin($success);
        } 
    }

    public function fcm_token(Request $request)
    {
        $akses = $request->user(); 
        $token=explode('|',$request->bearerToken());
        $cek=Accesstoken::where('tokenable_id',$akses->id)->where('id','!=',$token[0])->where('token_device',$request->token)->update([
            'active_status'=>0,
            'token_device'=>null,
        ]);
        $hapus=Accesstoken::where('tokenable_id','!=',$akses->id)->where('token_device',$request->token)->delete();
        $update=Accesstoken::where('id',$token[0])->update([
            'token_device'=>$request->token,
            'active_status'=>1,
            'customer_code'=>$akses->cust_id,
        ]);
        $delete=Accesstoken::where('id','!=',$token[0])->where('token_device',$request->token)->delete();
        $success=[];
        return $this->sendResponse($success, 'success');
    }

    public function logout(Request $request)
    {
        $akses = $request->user(); 
        $token=explode('|',$request->bearerToken());
        $hapus=Accesstoken::where('tokenable_id',$akses->id)->delete();
        $success=[];
        return $this->sendResponse($success, 'success');
    }
}