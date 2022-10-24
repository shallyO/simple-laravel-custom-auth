<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordOtpMail;
use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * This function accepts an email address and sends an otp to the email address
     */
    public function resetUserPassword(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false,'message'=> $validator->errors()], 400);

        }

        $otp = rand(123456, 999999);

        $this->generateAndSendOtp($request->email);

        return response()->json(['status' => true,'message'=> 'please check email for otp'], 200);

    }

    /**
     * @param $email
     * This function generates a unique otp and sends it to the email address passed to it
     */
    public function generateAndSendOtp($email){

        $user = User::where('email', $email)->first();

        $otp = rand(123456, 999999);

        $forgotMailData = [
            'title' => 'Forgot Password Otp',
            'body' => 'Otp : '.$otp,' This otp expires in 10 minutes'
        ];

        VerificationCode::where('user_id', $user->id)->update([
            'otp' => $otp,
            'expire_at' => Carbon::now()->addMinutes(10)
        ]);

        Mail::to($email)->send(new ForgotPasswordOtpMail($forgotMailData));

    }

    /**
     * @param Request $request
     * accept the email and otp and confirms if its correct
     */
    public function verifyOtp(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false,'message'=> $validator->errors()], 400);

        }

        $user = User::where('email', $request->email)->first();

        #Validation Logic
        $verificationCode   = VerificationCode::where('user_id', $user->id)->where('otp', $request->otp)->first();

        $now = Carbon::now();
        if (!$verificationCode) {

            return response()->json(['status' => false, 'message'=> 'incorrect Otp'], 400);

        }elseif($verificationCode && $now->isAfter($verificationCode->expire_at)){

            return response()->json(['status' => false, 'message'=> 'your otp has expired'], 400);

        }else{

            return response()->json(['status' => true, 'message'=> 'otp correct, please provide new password'], 200);

        }

    }

    public function updatePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false,'message'=> $validator->errors()], 400);

        }

        $user = User::where('email', $request->email)->first();

        User::where('id', $user->id)->update(['password' => Hash::make($request->password)]);

        return response()->json(['status' => true,'message'=> 'password has been updated successfully'], 200);


    }

}
