<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
class RegisterController extends Controller
{
    public $loginAfterSignUp = true;



    public function Register(Request $request){
      
        $v = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password'  => 'required|min:3',
        ]);

        if ($v->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
       $user = new User();
       $user->name = $request->name;
       $user->email = $request->email;
       $user->password = bcrypt($request->password);
       $user->role=$request->role;
       $user->save();
       return response()->json(['status' => 'success'], 200);
    }
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
        public function changePassword(Request $request){
                $user=User::find(Auth::user()->id);
            if($user){
                if(Hash::check($request['oldPassword'],$user->password)){
                    $user->password=bcrypt($request->newPassword);
                    $user->update();
                    return 'password changed';
                }else{
                    return response()->json(['error' => 'old password not match !'], 401);
                }
            }
                return 'user not fuound';
        }
}
