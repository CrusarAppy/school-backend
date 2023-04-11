<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserChildren;
use App\Models\UserTranslation;
use DB;

class UserController extends Controller
{
    public function verifyEmail(Request $request)
    {
        try{
            $updateEmail = DB::table('email_updates')
                ->where([
                'token' => $request->token
                ])
                ->first();  
            
            if(!$updateEmail){
                return view('auth.message',['message' => "Invalid Token."]);
            }

            DB::beginTransaction();
            // dd($updateEmail);
            DB::table('users')->where('id',$updateEmail->user_id)->update(["email"=>$updateEmail->email]);
            
            DB::table('email_updates')->where('email',$updateEmail->email)->delete();

            DB::commit();
            return view('auth.message',['message' => "Email verified successfully."]);


   
        }
        catch(Exception $e)
        {
            DB::rollback();
            return view('auth.message',['message' => $e->getMessage()]);

        }
}
}
