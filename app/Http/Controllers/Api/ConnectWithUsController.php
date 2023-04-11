<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Services\ConnectService;
use App\Http\Requests\ConnectWithUs\ConnectWithUsRequest;

class ConnectWithUsController extends Controller
{
    use ApiResponser;

    public function store(ConnectWithUsRequest $request)
    {
        try{
            ConnectService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }


    public function show(Request $request, $id)
    {
        try{
            $data = ConnectService::show($id);
            return $this->successResponse("",$data);
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function index(Request $request)
    {
        try{
            $data = ConnectService::all();

            return $this->successResponse("",$data);
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function markRead($id)
    {
        try{
            ConnectService::readMessage($id);
            return $this->successResponse("Read message");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
