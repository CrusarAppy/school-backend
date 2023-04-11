<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use DB;

class DashboardController extends Controller
{
    use ApiResponser;

    public function getStats(Request $request)
    {
        try
        {
            $activeUsers = DB::table('users')
                ->where('user_type','like','active')
                ->where('approval_status',1)
                ->count();
            
            $generalUsers = DB::table('users')
            ->where('user_type','like','general')
            ->count();

            $pendingUsers = DB::table('users')
            ->where('user_type','like','active')
            ->where('approval_status',0)
            ->count();
            
            $unreadFeedbacks = DB::table('feedbacks')
                ->where('read_status',0)
                ->count();
            
            $unreadConnect =  DB::table('connect_with_us')
            ->where('read_status',0)
            ->count();



            return $this->successResponse("",[
                'active_users'=>$activeUsers,
                'general_users'=>$generalUsers,
                'pending_users'=>$pendingUsers,
                'unread_feedbacks'=>$unreadFeedbacks,
                'unread_connect_with_us'=>$unreadConnect
            ]);
        }
        catch(Exception $e)
        {
            return $this->errorResponse($e->getMessage());
        }
    }
}
