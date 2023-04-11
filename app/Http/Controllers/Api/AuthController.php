<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\AuthService;
use App\Traits\ApiResponser;
use App\Http\Requests\Auth\UpdatePasswordRequest;


class AuthController extends Controller
{
    use ApiResponser;

    public function login(LoginRequest $request)
    {
        try{
            $login_credentials['password'] = $request->input('password');
            if($request->input('phone_number'))
            {
                $login_credentials['phone_number'] = $request->input('phone_number');
            }
            else{
                $login_credentials['email'] = $request->input('email');
            }      
            
            $res = AuthService::Login($login_credentials);
            
            if($res['status'] == 'success'){
                return $this->successResponse('',$res['data']);
            }
            else{
                return $this->errorResponse($res['message'],401);
            }
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }        
    }

    public function logout(Request $request)
    {
        try{
            $res = AuthService::logout();
        
            if($res['status'] == 'success'){
                return $this->successResponse($res['message']);
            }
            else{
                return $this->errorResponse($res['message'],401);
            }
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
        
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try{
            $res = AuthService::updatePassword($request->all());
        
            if($res['status'] == 'success'){
                return $this->successResponse($res['message']);
            }
            else{
                return $this->errorResponse($res['message'],401);
            }
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
