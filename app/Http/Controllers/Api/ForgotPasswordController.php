<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PasswordResetService;
use App\Traits\ApiResponser;
use Str;
use DB;
use Carbon\Carbon;
use App\Services\MailService;
use App\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    use ApiResponser;

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try{
            $token = Str::random(64);
  
            DB::table('password_resets')->updateOrInsert([
                    'email' => $request->email,
                ] ,
                [
                'token' => $token, 
                'created_at' => Carbon::now()
              ]);
            
            MailService::send(\config('app.email_sender'),$request->email,'Password reset link: '.\config('app.url').'/reset-password/'.$token,"Password Reset");

            return $this->successResponse("Mail sent successfully.");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    
}
