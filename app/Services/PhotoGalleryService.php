<?php

namespace App\Services;

use App\Models\PhotoGallery;
use App\Models\Photo;
use DB;
use App\Models\PhotoGalleryTranslation;
use Carbon\Carbon;
use Str;

class PhotoGalleryService
{
  public static $imagePath = '/uploads/photo_gallery/';

  public static function all($language)
  {
    $photoGallery = DB::table('photo_gallery')
                ->join(DB::raw("(select title,photo_gallery_id from photo_gallery_translations where language ='$language') translations "),function($join){
                    $join->on('photo_gallery.id','=','translations.photo_gallery_id');
                })
                ->orderBy('photo_gallery.created_at','DESC')
                ->orderBy('photo_gallery.id','ASC')
                ->select([
                    'photo_gallery.id',
                    'translations.title',
                    'photo_gallery.created_at'
                ])
                ->get();

      foreach($photoGallery as $photo)
      {
        $photo->photo = DB::table('photos')
                  ->where('photo_gallery_id','=',$photo->id)
                  ->pluck('photo')
                  ->first();
      }    
                  
      $res = [
        "photo_gallery" => $photoGallery
      ];
      return $res;
  }

  public static function create($data)
  {
    DB::beginTransaction();
    $photoGallery = new PhotoGallery;
    $photoGallery->save();
    if(array_key_exists('english',$data))
    {
        PhotoGalleryTranslation::updateOrCreate([
                'photo_gallery_id'=> $photoGallery->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists('nepali',$data))
    {
        PhotoGalleryTranslation::updateOrCreate([
                'photo_gallery_id'=> $photoGallery->id,
                'language'=>'nepali'
            ],$data['nepali']
        );
    }

    if(array_key_exists('photos',$data))
    {
      foreach($data['photos'] as $file)
      {
          $photoName = time().Str::random(4).'.'.$file->extension();

          $photo= new Photo;
          $photo->photo_gallery_id = $photoGallery->id;
          $photo->photo = self::$imagePath.$photoName;
          $photo->save();
          $file->move(public_path(self::$imagePath),$photoName);
      }
    }

    DB::commit();
  }

  public static function update($id, array $data)
  {
    DB::beginTransaction();
    $photoGallery = PhotoGallery::where('id',$id)->firstOrFail();
  
    if(array_key_exists('english',$data))
    {
        PhotoGalleryTranslation::updateOrCreate([
                'photo_gallery_id'=> $photoGallery->id,
                'language'=>'english'
            ],$data['english']
        );
        unset($data['english']);

    }

    if(array_key_exists('nepali',$data))
    {
        PhotoGalleryTranslation::updateOrCreate([
                'photo_gallery_id'=> $photoGallery->id,
                'language'=>'nepali'
            ],$data['nepali']
        );
        unset($data['nepali']);
    }

    if(array_key_exists('photos',$data))
    {
      foreach($data['photos'] as $file)
      {
          $photoName = time().Str::random(4).'.'.$file->extension();

          $photo= new Photo;
          $photo->photo_gallery_id = $photoGallery->id;
          $photo->photo = self::$imagePath.$photoName;
          $photo->save();
          $file->move(public_path(self::$imagePath),$photoName);
      }
      unset($data['photos']);
    }
    
    if(array_key_exists('delete_photo_ids',$data))
    {
      Photo::where('photo_gallery_id',$id)->whereIn('id',$data['delete_photo_ids'])->delete();
      unset($data['delete_photo_ids']);
    }
    $photoGallery->save();

    DB::commit();
  }

  public static function show($id)
  {
    $photoGallery = PhotoGallery::where('id',$id)->first();
    $images = $photoGallery->images()->get(['id','photo']);
    $translationNep = $photoGallery->translation('nepali')->select([
      'title',
    ])->first();

    $translationEng = $photoGallery->translation('english')->select([
      'title',
    ])->first();

    return $photoGallery->toArray() + ['nepali' => $translationNep->toArray()] + ['english' => $translationEng->toArray()] + ["photos" =>$images];
  }

  public static function delete($id)
  {
      return PhotoGallery::where('id',$id)->firstOrFail()->delete();
  }

  public static function deletePhoto($id,$photoId)
  {
      return Photo::where('photo_gallery_id',$id)->where('id',$photoId)->firstOrFail()->delete();
  }
}