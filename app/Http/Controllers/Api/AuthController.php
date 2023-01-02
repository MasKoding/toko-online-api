<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function PHPSTORM_META\map;

class AuthController extends Controller
{
    //

    public function login(Request $request){
        $validasi = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);
        $user =  User::where('email',$request->email)->first();   
        if($validasi->fails()){
         
            return $this->error_message($validasi->errors()->all());
        }
        
        if($user){
            if(password_verify($request->password,$user->password)){
                return $this->success_message($user);
            }else{
                return $this->error_message('Email atau Password salah');
            }
        }
        return $this->error_message('Data tidak ditemukan!');
    }

    public function register(Request $request){
        $validasi =  Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email|unique:users',
            'phone'=>'required|unique:users',
            'password'=>'required|min:8'
        ]);

        if($validasi->fails()){
           return $this->error_message($validasi->errors()->all());
            
        }
       $user = User::create(array_merge($request->all(),[
        'password'=>bcrypt($request->password)
       ]));

       if($user){
            return $this->success_message($user);
        
       }else{
            return $this->error_message();

       }
    }

    public function error_message($message="failed"){
        return response()->json([
            'code'=>400,
            'message'=>$message
        ],400);
    }
    
    public function success_message($data,$message="success"){
        return response()->json([
            'code'=>200,
            'message'=>$message,
            'data'=>$data
        ],200);
    }
}
