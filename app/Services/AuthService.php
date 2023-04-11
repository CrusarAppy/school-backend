<?php

namespace App\Services;
use App\Models\User;

class AuthService
{
  public static function login($login_credentials)
  {
      if(auth()->attempt($login_credentials)){
         
          $user_login_token= auth()->user()->createToken('ambika.s.s.edu.np')->accessToken;
          //now return this token on success login attemp
          
          return [
            "status"=>"success",
            "data"=>[
              "token"=>$user_login_token,
              "id"=>auth()->user()->id,
              "user_type" => auth()->user()->user_type,
          ]
        ];
      }
      else{
          return ["status"=>"error","message"=>"Incorrect Password."];
      }
  }

  public static function logout()
  {
      if(auth()->check())
      {
          auth()->user()->token()->revoke();
          return ["status"=>"success","message"=>"Logged out successfully."];
      }
      return ["status"=>"error","message"=>"Unable to logout."];
  }

  public static function updatePassword($data)
  {      
    $user = auth()->guard('api')->user();
    $check  = auth()->guard('web')->attempt([
        'email' => $user->email,
        'password' => $data['old_password']
    ]);
    if($check)
    {
        User::where('id',$user->id)->update(['password'=>bcrypt($data['password'])]);
        return ['status'=>'success','message'=>"Password successfully updated."]; 
    }
    else{
        return ['status'=>'error','message'=>"Incorrect old password."];
    }  
     
  }
}