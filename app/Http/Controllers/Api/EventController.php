<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use App\Models\Event;
use App\Models\EventTranslation;
use App\Services\EventService;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use DB;

class EventController extends Controller
{
    use ApiResponser;

    public function store(StoreEventRequest $request)
    {
        try{
            EventService::create($request->all());
            return $this->successResponse("Successfully created.");
        }
        catch(Exception $e)
        {
            DB::rollback();
            return $this->errorResponse($e->getMessage());
        }
    }

    public function update(UpdateEventRequest $request,$id)
    {
        try{
            
            EventService::update($id,$request->all());            

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
            $event = EventService::show($id);
            return $this->successResponse("",[
                "event"=>$event,
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
            $event = EventService::read($id,$request->language);
            return $this->successResponse("",[
                "event"=>$event,
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
            $events = EventService::all($request->input('language'));

            return $this->successResponse("",
                $events +
                ["asset_url" => \config("app.asset_url")]
            );
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }

    public function upcoming(Request $request)
    {
        try{
            $events = EventService::upcoming($request->input('language'));

            return $this->successResponse("",
                $events +
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
            EventService::delete($id);
            return $this->successResponse("Deleted successfully");
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
