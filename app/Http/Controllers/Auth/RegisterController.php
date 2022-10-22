<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function create(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone_number' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'message'=> $validator->errors()], 400);

        }

       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        $this->generateAndSendOtp($user->id);

        return response()->json(['status' => true,'message'=> 'Otp has been sent to your phonenumber and email'], 200);


    }

    public function generateAndSendOtp($userId){

        $user = User::where('id', $userId)->first();

        $otp = rand(123456, 999999);

        #send to email

        #send to phone_number

        // Create OTP
        return VerificationCode::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
    }


}
