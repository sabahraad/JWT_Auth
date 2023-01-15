<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Auth;
use Validator;
use Hash;
class AuthController extends Controller
{

    public function register(Request $request){

        $validator= Validator::make($request->all(),[
            'name'=>'required|string|min:2|max:100',
            'email'=>'required|string|email|max:100|unique:users',
            'password' =>'required|string|min:4|max:10'
        ],
        [
            'name.required' => 'Please Give a Proper User Name',
            'email.required'=>'Please Give a email address',
            'password.required'=>'Please Give a Password',
            'password.min'=>'Password need to be atleast 4 digit'

        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password)
        ]);

        return response()->json([
            'message'=>'User Successfully Registerd',
            'user'=>$user
        ]);

    }
    
    public function login(Request $request){

        $validator= Validator::make($request->all(),[
            'email'=>'required|string|email|max:100',
            'password' =>'required|string|min:4|max:10'
        ],
        [
            'email.required'=>'Please Give a email address',
            'password.required'=>'Please Give a Password',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }

        if(!$token=Auth()->attempt($validator->validate())){

            return response()->json(['error'=>'Unauthorized']);
        }

        return $this->responseToken($token);

    }

    public function profile(){

        return response()->json(Auth()->user());

    }

    public function logout(){
        Auth()->logout();
        return response()->json(['message'=>'User Successfully Logged Out']);
    }

    protected function responseToken($token){

        return response()->json([
            'token'=>$token,
            'expiers_in'=>Auth()->factory()->getTTL()*60

        ]);
    }
}
