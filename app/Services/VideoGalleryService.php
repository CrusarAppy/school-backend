<?php

namespace App\Services;
use App\Models\VideoGallery;
use DB;
use App\Models\VideoGalleryTranslation;
use Str;

class VideoGalleryService
{
  public static function all($language)
  {
    $data = DB::table('video_gallery')
                ->join(DB::raw("(select title,video_id from video_gallery_translations where language ='$language') translations "),function($join){
                    $join->on('video_gallery.id','=','translations.video_id');
                })
                ->select([
                    'video_gallery.id',
                    'translations.title',
                    'video_gallery.video',
                    'video_gallery.created_at'
                ])
                ->orderBy('video_gallery.created_at','DESC')
                ->paginate(8);
    $res = [
      "videos" => $data->values(),
      "total" => $data->total(),
      "current_page" => $data->currentPage(),
      "last_page" => $data->lastPage()
    ];
    return $res;
  }

  public static function create($data)
  {
    DB::beginTransaction();

    $video = new VideoGallery;
    $video->video = $data['video'];    
    $video->save();
    
    if(array_key_exists("english",$data))
    {
        VideoGalleryTranslation::updateOrCreate([
                'video_id'=> $video->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists("nepali",$data))
    {
      VideoGalleryTranslation::updateOrCreate([
          'video_id'=> $video->id,
          'language'=>'nepali'
        ],$data['nepali']
      );
    }
    
    DB::commit();
  }

  public static function update($id, $data)
  {
    DB::beginTransaction();
    $video = VideoGallery::where('id',$id)->firstOrFail();

    if(array_key_exists("video",$data))
    {
      $video->video = $data['video'];    
      $video->save();
    }
    

    if(array_key_exists('english',$data))
    {
        VideoGalleryTranslation::updateOrCreate([
                'video_id'=> $video->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists('nepali',$data))
    {
      VideoGalleryTranslation::updateOrCreate([
          'video_id'=> $video->id,
          'language'=>'nepali'
        ],$data['nepali']
      );
    }

    DB::commit();
  }

  public static function read($id,$language)
  {
    $video = VideoGallery::where('id',$id)->firstOrFail();
    $translation = $video->translation($language)->select([
        'title',
    ])->first();

    return $video->toArray() + $translation->toArray();
  }

  public static function show($id)
  {
    $video = VideoGallery::where('id',$id)->firstOrFail();
    $translationNep = $video->translation('nepali')->select([
      'title',
    ])->first();
    $translationEng = $video->translation('english')->select([
      'title',
    ])->first();

    return $video->toArray() + ['nepali' => $translationNep->toArray()] + ['english' => $translationEng->toArray()];
  }

  public static function delete($id)
  {
    return VideoGallery::where('id',$id)->firstOrFail()->delete();
  }
}