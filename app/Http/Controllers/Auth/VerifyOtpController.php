<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class VerifyOtpController extends Controller
{
    public function verifyOtp(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false,'message'=> $validator->errors()], 400);

        }

        #Validation Logic
        $verificationCode   = VerificationCode::where('user_id', $request->user_id)->where('otp', $request->otp)->first();

        $now = Carbon::now();
        if (!$verificationCode) {

            return response()->json(['status' => false, 'message'=> 'incorrect Otp'], 400);

        }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){

            return response()->json(['status' => false, 'message'=> 'your otp has expired'], 400);

            //You can resend otp
        }else{
            User::where('id', $request->user_id)->update(['is_verified' => 'true' ]);

            return response()->json(['status' => true, 'message'=> 'otp correct, login to your account'], 200);

        }




    }
}
