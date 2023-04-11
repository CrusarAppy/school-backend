<?php

namespace App\Services;

use App\Models\Notice;
use App\Models\NoticeImage;
use DB;
use App\Models\NoticeTranslation;
use Str;

class NoticeService
{
  public static $imagePath = '/uploads/notices/';

  public static function all($language)
  {
    $notices = DB::table('notices')
                ->join(DB::raw("(select title,notice_id,description from notice_translations where language ='$language') translations "),function($join){
                    $join->on('notices.id','=','translations.notice_id');
                })
                ->orderBy('notices.created_at','DESC')
                ->orderBy('notices.id','ASC')
                ->select([
                    'notices.id',
                    'translations.title',
                    'notices.created_at',
                    'translations.description'
                ])
                ->paginate(10);

    foreach($notices as $notice)
    {
      $notice->image = DB::table('notice_images')
                              ->where('notice_id','=',$notice->id)
                              ->pluck('image')
                              ->first();
    }

    $res = [
      "notices" => $notices->values(),
      "total" => $notices->total(),
      "current_page" => $notices->currentPage(),
      "last_page" => $notices->lastPage()
    ];

    return $res;
  }

  public static function create($data)
  {
    DB::beginTransaction();
    $notice = new Notice;
    $notice->save();

    if(array_key_exists("english",$data))
    {
        NoticeTranslation::updateOrCreate([
                'notice_id'=> $notice->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists("nepali",$data))
    {
        NoticeTranslation::updateOrCreate([
                'notice_id'=> $notice->id,
                'language'=>'nepali'
            ],$data['nepali']
        );
    }

    if(array_key_exists('images',$data))
    {
      foreach($data['images'] as $file)
      {
        $imageName = time().Str::random(4).'.'.$file->extension();

        $noticeImage = new NoticeImage;
        $noticeImage->notice_id = $notice->id;
        $noticeImage->image = self::$imagePath.$imageName;
        $noticeImage->save();
        $file->move(public_path(self::$imagePath),$imageName);
      }
    }

    DB::commit();
  }

  public static function update($id, array $data)
  {
    DB::beginTransaction();
    $notice = Notice::where('id',$id)->firstOrFail();

    if(array_key_exists("english",$data))
    {        
      NoticeTranslation::updateOrCreate([
              'notice_id'=>$id,
              'language'=>'english'
          ],$data['english']
      );
      unset($data['english']);

    }

    if(array_key_exists("nepali",$data))
    {
      NoticeTranslation::updateOrCreate([
              'notice_id'=>$id,
              'language'=>'nepali'
          ],$data['nepali']
      );
      unset($data['nepali']);
    }

    if(array_key_exists('images',$data))
    {
      foreach($data['images'] as $file)
      {
        $imageName = time().Str::random(4).'.'.$file->extension();

        $noticeImage = new NoticeImage;
        $noticeImage->notice_id = $notice->id;
        $noticeImage->image = self::$imagePath.$imageName;
        $noticeImage->save();
        $file->move(public_path(self::$imagePath),$imageName);
      }
      unset($data['images']);
    }
    $photosToDelete = [];
    if(array_key_exists('delete_image_ids',$data))
    {
        $photosToDelete = NoticeImage::select('image')
          ->where('notice_id',$id)
          ->whereIn('id',$data['delete_image_ids'])
          ->get();

        NoticeImage::where('notice_id',$id)->whereIn('id',$data['delete_image_ids'])->delete();
        unset($data['delete_image_ids']);
    }
    
    $notice->update($data);
    $notice->save();
    DB::commit();
    
    foreach($photosToDelete as $temp)
    {
      ImageService::delete(public_path().$temp->image);
    }
    
  }

  public static function read($id,$language)
  {
    $notice = Notice::where('id',$id)
              ->firstOrFail();
    $images = $notice->images()->get(['id','image']);
    $translation = $notice->translation($language)->select([
        'title',
        'description'
    ])->first();

    return $notice->toArray() + $translation->toArray() + ["images" =>$images];
  }

  public static function show($id)
  {
    $notice = Notice::where('id',$id)
              ->firstOrFail();
    $images = $notice->images()->get(['id','image']);
    $translationNep = $notice->translation('nepali')->select([
      'title',
      'description'
    ])->first();

    $translationEng = $notice->translation('english')->select([
      'title',
      'description'
    ])->first();

    return $notice->toArray() + ['nepali' => $translationNep->toArray()] + ['english' => $translationEng->toArray()] + ["images" =>$images];
  }

  public static function delete($id)
  {
      return Notice::where('id',$id)->firstOrFail()->delete();
  }
}