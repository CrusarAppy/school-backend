<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\PhotoGalleryController;
use App\Http\Controllers\Api\VideoGalleryController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\ConnectWithUsController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PopupImageController;
use App\Http\Controllers\Api\DownloadsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:api'], function()
{
    Route::group(['middleware' => 'auth.admin'], function()
    {
        Route::post('notices',[NoticeController::class,'store']);
        Route::post('notices/{id}',[NoticeController::class,'update']);
        Route::delete('notices/{id}',[NoticeController::class,'destroy']);
        Route::post('events',[EventController::class,'store']);
        Route::post('events/{id}',[EventController::class,'update']);
        Route::delete('events/{id}',[EventController::class,'destroy']);
        Route::post('blogs',[BlogController::class,'store']);
        Route::post('blogs/{id}',[BlogController::class,'update']);
        Route::delete('blogs/{id}',[BlogController::class,'destroy']);
        Route::post('photogallery',[PhotoGalleryController::class,'store']);
        Route::post('photogallery/{id}',[PhotoGalleryController::class,'update']);
        Route::delete('photogallery/{id}',[PhotoGalleryController::class,'destroy']);
        Route::delete('photogallery/{id}/photo/{photoId}',[PhotoGalleryController::class,'destroySingle']);
        Route::post('videos',[VideoGalleryController::class,'store']);
        Route::post('videos/{id}',[VideoGalleryController::class,'update']);
        Route::delete('videos/{id}',[VideoGalleryController::class,'destroy']);
        Route::get('connectwithus',[ConnectWithUsController::class,'index']);
        Route::get('connectwithus/{id}',[ConnectWithUsController::class,'show']);
        Route::post('connectwithus/{id}/read',[ConnectWithUsController::class,'markRead']);
        Route::delete('photos/{id}',[PhotoGalleryController::class,'destroy']);
        Route::get('dashboard',[DashboardController::class,'getStats']);

        Route::post('popupimage',[PopupImageController::class,'store']);
        Route::delete('popupimage/{id}',[PopupImageController::class,'destroy']);

        Route::post('downloads',[DownloadsController::class,'store']);
        Route::post('downloads/{id}',[DownloadsController::class,'update']);
        Route::delete('downloads/{id}',[DownloadsController::class,'destroy']);
    });
    
    Route::post('updatepassword',[AuthController::class,'updatePassword']);
    Route::get('logout',[AuthController::class,'logout']);
    
});

Route::post('login',[AuthController::class,'login']);

Route::get('notices',[NoticeController::class,'index']);
Route::get('notices/{id}',[NoticeController::class,'show']);
Route::get('notices/{id}/details',[NoticeController::class,'read']);

Route::get('downloads',[DownloadsController::class,'index']);
Route::get('downloads/{id}',[DownloadsController::class,'show']);

Route::get('events',[EventController::class,'index']);
Route::get('events/upcoming',[EventController::class,'upcoming']);
Route::get('events/{id}',[EventController::class,'show']);
Route::get('events/{id}/details',[EventController::class,'read']);

Route::get('blogs',[BlogController::class,'index']);
Route::get('blogs/{id}',[BlogController::class,'show']);
Route::get('blogs/{id}/details',[BlogController::class,'read']);

Route::get('photogallery',[PhotoGalleryController::class,'index']);
Route::get('photogallery/{id}',[PhotoGalleryController::class,'show']);

Route::get('videos',[VideoGalleryController::class,'index']);
Route::get('videos/{id}',[VideoGalleryController::class,'show']);

Route::post('connectwithus',[ConnectWIthUsController::class,'store']);

Route::post("forgotpassword",[ForgotPasswordController::class,'forgotPassword']);
Route::post("forgotpassword/verifycode",[ForgotPasswordController::class,'verifyCode']);
Route::post("forgotpassword/changepassword",[ForgotPasswordController::class,'changePassword']);

Route::get('popupimage',[PopupImageController::class,'index']);
