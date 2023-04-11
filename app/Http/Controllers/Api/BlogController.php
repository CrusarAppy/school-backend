<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Services\BlogService;
use App\Http\Requests\Blog\StoreBlogRequest;
use App\Http\Requests\Blog\UpdateBlogRequest;
use DB;

class BlogController extends Controller
{
    use ApiResponser;

    public function store(StoreBlogRequest $request)
    {
        try{
            BlogService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdateBlogRequest $request,$id)
    {
        try{
            
            BlogService::update($id,$request->all());            

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
            $blog = BlogService::show($id);
            return $this->successResponse("",[
                "blog"=>$blog,
                "asset_url" => \config("app.asset_url"),
            ]);
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function read(Request $request, $id)
    {
        try{
            $blog = BlogService::read($id,$request->input('language'));
            return $this->successResponse("",[
                "blog"=>$blog,
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
            $blogs = BlogService::all($request->input('language'));

            return $this->successResponse("",
                $blogs +
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
            BlogService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
