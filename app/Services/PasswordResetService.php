<?php

namespace App\Services;
use DB;
use Str;
use Hash;
use Carbon\Carbon;

class PasswordResetService
{
  public static function sendCode($email)
  {
    $code = \config('app.env') =='local' ? 9999 : rand(1000,9999);
    DB::table('password_resets')
      ->updateOrInsert(['email'=>$email],[
        'code' => bcrypt($code),
        'expiry_date' => Carbon::now()->addMinute(10)->toDateTimeString(),
        'status' =>0
    ]);
    MailService::send(\config('app.email_sender'),$email,'Code: '.$code,"Password Reset");
    return ['status'=>'success','message'=> 'Code sent successfully.'];
  }

  public static function verifyCode($email,$code)
  {
    $data = DB::table('password_resets')
                ->where('email',$email)
                ->first();
    if(!$data)
    {
      return ['status'=>'error','message'=>'Code not sent.'];
    }
    
    if(Carbon::now()->gt($data->expiry_date) || $data->status == 1)
    {
      return ['status'=>'error','message'=>'Code has expired.'];
    }
    if(!Hash::check($code,$data->code))
    {
      return ['status'=>'error','message'=>'Invalid Code.'];
    }
    $token = Str::random(16);
    DB::table('password_resets')->updateOrInsert(['email'=>$email],[
        'token'=>$token,
        'expiry_date'=>Carbon::now(),
        'status' => 1
    ]);

    return ['status'=>'success','message'=>'Code verified','data'=>['token'=>$token]];
  }

  public static function resetPassword($email,$password,$token)
  {
    $data = DB::table('password_resets')
                ->where('email',$email)
                ->first();
    
    if($data->status != 1)
    {
      return ['status'=>'error','message'=>'Code not verified.'];
    }

    if($token != $data->token)
    {
      return ['status'=>'error','message'=>'Invalid token'];
    }

    DB::table('users')->where('email',$email)->update(['password' => bcrypt($password)]);
    return ['status'=>'success','message'=>'Password changed successfully.'];
  }
}