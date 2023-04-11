<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PhotoGalleryService;
use App\Http\Requests\PhotoGallery\StorePhotoGalleryRequest;
use App\Http\Requests\PhotoGallery\UpdatePhotoGalleryRequest;
use App\Traits\ApiResponser;

class PhotoGalleryController extends Controller
{
    use ApiResponser;

    public function store(StorePhotoGalleryRequest $request)
    {
        try{
            $path = PhotoGalleryService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdatePhotoGalleryRequest $request,$id)
    {
        try{
            PhotoGalleryService::update($id,$request->all());            

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
            $photos = PhotoGalleryService::all($request->input('language'));

            return $this->successResponse("",
                $photos +
                ["asset_url" => \config("app.asset_url")]
            );
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
    public function show(Request $request, $id)
    {
        try{
            $data = PhotoGalleryService::show($id);
            return $this->successResponse("",["photo_gallery"=>$data,"asset_url" => \config("app.asset_url")]);
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try{
            PhotoGalleryService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function destroySingle($id,$photoId)
    {
        try{
            PhotoGalleryService::deletePhoto($id,$photoId);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
