<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false,'message'=> $validator->errors()], 400);

        }


        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            if(Auth::user()->is_verified  === false){

                return response()->json(['status' => false, 'message'=> 'Please verify your account'], 400);

            }

            return response()->json(['status' => true,'message'=> 'Login successful'], 200);

        }else{

            return response()->json(['status' => true, 'message'=> 'Incorrect email or password'], 400);

        }

    }
}
