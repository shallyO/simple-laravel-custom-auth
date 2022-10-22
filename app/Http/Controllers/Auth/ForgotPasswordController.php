<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function sendPasswordEmail(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false,'message'=> $validator->errors()], 400);

        }

        $otp = rand(123456, 999999);

        #send otp to email


        $user = User::where('email', $request->email)->update(['password' => Hash::make($otp)]);



        return response()->json(['status' => true, 'message'=> 'new password has been sent to your email '.$otp], 200);

    }

    public function generateAndSendOtp($userId){

        $user = User::where('id', $userId)->first();

        $otp = rand(123456, 999999);

        #send to email

        #send to phone_number

        // Create OTP
        return VerificationCode::where('user_id', $user->id)->update([
            'otp' => $otp,
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);
    }

    public function verifyOtp(Request $request){

    }

    public function updatePassword(Request $request){

    }

}
