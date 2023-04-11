<?php

namespace App\Services;

use App\Models\PopupImage;
use DB;
use Str;
use Carbon\Carbon;

class PopupImageService
{
  public static $photoPath = '/uploads/popups/';

  public static function all()
  {
    $photos = DB::table('popup_images')
                ->orderBy('popup_images.created_at','DESC')
                ->orderBy('popup_images.id','ASC')
                ->whereDate('expiry_date','>=',Carbon::now())
                ->select([
                    'id',
                    'photo',
                    'created_at',
                    'expiry_date'
                ])
                ->get();


    

    return ["photos"=>$photos->toArray()];
  }

  public static function create($data)
  {
    DB::beginTransaction();
    $photo = new PopupImage;
    $photo->expiry_date= $data['expiry_date'];
    $file = $data['photo'];
    $photoName = time().Str::random(4).'.'.$file->extension();
    $photo->photo = self::$photoPath.$photoName;
    $file->move(public_path(self::$photoPath),$photoName);
    $photo->save();

   
    DB::commit();
  }

  
  public static function delete($id)
  {
      return PopupImage::where('id',$id)->firstOrFail()->delete();
  }
}