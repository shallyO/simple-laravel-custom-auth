<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('user/register', 'Auth\RegisterController@create');
Route::post('user/verify-otp', 'Auth\VerifyOtpController@verifyOtp');
Route::post('user/login', 'Auth\LoginController@login');
Route::post('user/reset-password', 'Auth\ForgotPasswordController@resetUserPassword');

