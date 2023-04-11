<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Services\PopupImageService;
use App\Http\Requests\PopupImage\StorePopupImageRequest;
use DB;

class PopupImageController extends Controller
{
    use ApiResponser;

    public function store(StorePopupImageRequest $request)
    {
        try{
            PopupImageService::create($request->all());
            return $this->successResponse("Successfully created.");
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
            $images = PopupImageService::all();

            return $this->successResponse("",
                $images +
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
            PopupImageService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
