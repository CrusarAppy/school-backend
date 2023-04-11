<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Notice;
use App\Models\NoticeTranslation;
use App\Services\NoticeService;
use App\Http\Requests\Notice\StoreNoticeRequest;
use App\Http\Requests\Notice\UpdateNoticeRequest;
use DB;

class NoticeController extends Controller
{
    use ApiResponser;

    public function store(StoreNoticeRequest $request)
    {
        try{
            NoticeService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdateNoticeRequest $request,$id)
    {
        try{
            NoticeService::update($id,$request->all());            

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
            $notice = NoticeService::show($id);
            return $this->successResponse("",[
                "notice"=>$notice,
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
            $notice = NoticeService::read($id,$request->input('language'));
            return $this->successResponse("",[
                "notice"=>$notice,
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
            $notices = NoticeService::all($request->input('language'));

            return $this->successResponse("",
                $notices +
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
            NoticeService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
