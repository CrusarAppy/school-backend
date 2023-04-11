<?php

namespace App\Services;

use App\Models\Downloads;
use DB;
use App\Models\DownloadsTranslation;
use Str;

class DownloadsService
{
  public static $filePath = '/uploads/downloads/';

  public static function all($language)
  {
    $data = DB::table('downloads')
                ->join(DB::raw("(select title,downloads_id from downloads_translations where language ='$language') translations "),function($join){
                    $join->on('downloads.id','=','translations.downloads_id');
                })
                ->orderBy('downloads.created_at','DESC')
                ->orderBy('downloads.id','ASC')
                ->select([
                    'downloads.id',
                    'translations.title',
                    'downloads.created_at',
                    'downloads.file'
                ])
                ->paginate(10);
    $res = [
      "downloads" => $data->values(),
      "total" => $data->total(),
      "current_page" => $data->currentPage(),
      "last_page" => $data->lastPage()
    ];

    return $res;
  }

  public static function show($id)
  {
    $downloads = Downloads::where('id',$id)->firstOrFail();
    $translationNep = $downloads->translation('nepali')->select([
      'title'
    ])->first();

    $translationEng = $downloads->translation('english')->select([
      'title'
    ])->first();

    return $downloads->toArray() + ['nepali' => $translationNep->toArray()] + ['english' => $translationEng->toArray()];
  }

  public static function create($data)
  {
    DB::beginTransaction();
    $downloads = new Downloads;

    $file = $data['file'];
    $fileName = time().Str::random(4).'.'.$file->extension();

    $downloads->file = self::$filePath.$fileName;
    $downloads->save();

    if(array_key_exists('english',$data))
    {
        DownloadsTranslation::updateOrCreate([
                'downloads_id'=> $downloads->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists('nepali',$data))
    {
        DownloadsTranslation::updateOrCreate([
                'downloads_id'=> $downloads->id,
                'language'=>'nepali'
            ],$data['nepali']
        );
    }
    $file->move(public_path(self::$filePath), $fileName);
    DB::commit();
  }

  public static function update($id, array $data)
  {
    DB::beginTransaction();
    $downloads = Downloads::where('id',$id)->firstOrFail();

    if(array_key_exists("file",$data))
    {
      $file = $data['file'];
      $fileName = time().Str::random(4).'.'.$file->extension();
      $downloads->file = self::$filePath.$fileName;    
      $downloads->save();
      $file->move(public_path(self::$filePath), $fileName);

      unset($data['file']);
    }

    if(array_key_exists("english",$data))
    {        
      DownloadsTranslation::updateOrCreate([
              'downloads_id'=>$id,
              'language'=>'english'
          ],$data['english']
      );
      unset($data['english']);
    }

    if(array_key_exists("nepali",$data))
    {
      DownloadsTranslation::updateOrCreate([
              'downloads_id'=>$id,
              'language'=>'nepali'
          ],$data['nepali']
      );
      unset($data['nepali']);
    }
    
    $downloads->update($data);

    DB::commit();
  }

  

  public static function delete($id)
  {
      return Downloads::where('id',$id)->firstOrFail()->delete();
  }
}