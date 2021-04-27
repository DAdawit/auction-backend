<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignInController extends Controller
{
    public function SignIn(Request $request)
    {
     if(!$token=auth()->attempt($request->only('email','password'))){
         return response('not auth',401);
     }
     return response()->json(compact('token'));
    }

    public function AdminLogin(Request $request){
        return $request;
//        if(!$token = auth()->attempt($request->only('email','password'))){
//            return response('user name and password not match !',401);
//        }else{
//            return response([
//                "role"=>"admin",
//                "token"=>$token
//            ]);
//        }
    }

}
