<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\VideoGalleryService;
use App\Traits\ApiResponser;
use App\Http\Requests\VideoGallery\StoreVideoRequest;
use App\Http\Requests\VideoGallery\UpdateVideoRequest;

class VideoGalleryController extends Controller
{
    use ApiResponser;

    public function store(StoreVideoRequest $request)
    {
        try{
            VideoGalleryService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdateVideoRequest $request,$id)
    {
        try{
            VideoGalleryService::update($id,$request->all());            

            return $this->successResponse("Successfully updated");            
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
            $video = VideoGalleryService::show($id);
            return $this->successResponse("",[
                "video"=>$video,
                "asset_url" => \config("app.asset_url"),
            ]);
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function index(Request $request)
    {
        try{
            $videos = VideoGalleryService::all($request->input('language'));

            return $this->successResponse("",
                $videos +
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
            VideoGalleryService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
