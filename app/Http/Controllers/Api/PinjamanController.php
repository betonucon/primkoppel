<?php
   
namespace App\Http\Controllers\Api;
   
use Illuminate\Http\Request;
use App\Http\Controllers\Api\BaseController as BaseController;
use App\Nilai;
use App\Cicilan;
use App\User;
use App\VUser;
use App\Tujuan;
use Illuminate\Support\Facades\Auth;
use Validator;
   
class PinjamanController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $auth = Auth::user(); 
        $user = VUser::where('username',$auth->username)->first(); 
        
        try{
            $rules = [];
            $messages = [];
                $rules['nominal'] = 'required|numeric';
                $rules['waktu'] = 'required|numeric';

                
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
                }else{

                }
        } catch(\Exception $e){
            return $this->sendResponseerror($e->getMessage());
        } 
    }

    
    
    
    
}