<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Downloads;
use App\Models\DownloadsTranslation;
use App\Services\DownloadsService;
use App\Http\Requests\Downloads\StoreDownloadsRequest;
use App\Http\Requests\Downloads\UpdateDownloadsRequest;
use DB;

class DownloadsController extends Controller
{
    use ApiResponser;

    public function store(StoreDownloadsRequest $request)
    {
        try{
            DownloadsService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdateDownloadsRequest $request,$id)
    {
        try{
            
            DownloadsService::update($id,$request->all());            

            return $this->successResponse("Successfully updated");
            
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    
    public function index(Request $request)
    {
        try{
            $downloads = DownloadsService::all($request->input('language'));

            return $this->successResponse("",
                $downloads +
                ["asset_url" => \config("app.asset_url")]
            );
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function show(Request $request,$id)
    {
        try{
            $downloads = DownloadsService::show($id);

            return $this->successResponse("",
                ["downloads" => $downloads] +
                ["asset_url" => \config("app.asset_url")]
            );
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    


    public function destroy($id)
    {
        try{
            DownloadsService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
