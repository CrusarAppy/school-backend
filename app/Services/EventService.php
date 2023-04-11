<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventImage;
use DB;
use App\Models\EventTranslation;
use Carbon\Carbon;
use Str;

class EventService
{
  public static $imagePath = '/uploads/events/';

  public static function all($language)
  {
    $events = DB::table('events')
                ->join(DB::raw("(select title,event_id,description,location from event_translations where language ='$language') translations "),function($join){
                    $join->on('events.id','=','translations.event_id');
                })
                ->orderBy('events.date','DESC')
                ->orderBy('events.id','ASC')
                ->select([
                    'events.id',
                    'translations.title',
                    'events.date',
                    'events.start_time',
                    'translations.location',
                    'events.created_at',
                    'translations.description'
                ])
                ->paginate(10);

    foreach($events as $event)
    {
      $event->image = DB::table('event_images')
                              ->where('event_id','=',$event->id)
                              ->pluck('image')
                              ->first();
    }

    $res = [
      "events" => $events->values(),
      "total" => $events->total(),
      "current_page" => $events->currentPage(),
      "last_page" => $events->lastPage()
    ];
    return $res;
  }

  public static function upcoming($language)
  {
    $events = DB::table('events')
                ->where('date','>=',Carbon::now())
                ->join(DB::raw("(select title,event_id,description,location from event_translations where language ='$language') translations "),function($join){
                    $join->on('events.id','=','translations.event_id');
                })
                ->orderBy('events.date','DESC')
                ->orderBy('events.id','ASC')
                ->select([
                    'events.id',
                    'translations.title',
                    'events.date',
                    'events.start_time',
                    'translations.location',
                    'events.created_at',
                    'translations.description'
                ])
                ->paginate(10);

    foreach($events as $event)
    {
      $event->images = DB::table('event_images')
                              ->where('event_id','=',$event->id)
                              ->get(['id','image']);
    }

    $res = [
      "events" => $events->values(),
      "total" => $events->total(),
      "current_page" => $events->currentPage(),
      "last_page" => $events->lastPage()
    ];
    return $res;
  }

  public static function create($data)
  {
    DB::beginTransaction();
    $event = new Event;
    if(array_key_exists('date',$data))
    {
      $event->date = $data['date'];
    }
    if(array_key_exists('start_time',$data))
    {
      $event->start_time = $data['start_time'];
    }
    $event->save();

    if(array_key_exists('english',$data))
    {
        EventTranslation::updateOrCreate([
                'event_id'=> $event->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists('nepali',$data))
    {
        EventTranslation::updateOrCreate([
                'event_id'=> $event->id,
                'language'=>'nepali'
            ],$data['nepali']
        );
    }

    if(array_key_exists('images',$data))
    {
      foreach($data['images'] as $file)
      {
          $imageName = time().Str::random(4).'.'.$file->extension();

          $eventImage = new EventImage;
          $eventImage->event_id = $event->id;
          $eventImage->image = self::$imagePath.$imageName;
          $eventImage->save();
          $file->move(public_path(self::$imagePath),$imageName);
      }
    }

    DB::commit();
  }

  public static function update($id, array $data)
  {
    DB::beginTransaction();
    $event = Event::where('id',$id)->firstOrFail();

    if(array_key_exists("english",$data))
    {        
      EventTranslation::updateOrCreate([
              'event_id'=>$id,
              'language'=>'english'
          ],$data['english']
      );
      unset($data['english']);

    }

    if(array_key_exists("nepali",$data))
    {
      EventTranslation::updateOrCreate([
              'event_id'=>$id,
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

          $eventImage = new EventImage;
          $eventImage->event_id = $event->id;
          $eventImage->image = self::$imagePath.$imageName;
          $eventImage->save();
          $file->move(public_path(self::$imagePath),$imageName);
      }
      unset($data['images']);
    }
    
    $photosToDelete = [];
    if(array_key_exists('delete_image_ids',$data))
    {
        $photosToDelete = EventImage::select('image')
          ->where('event_id',$id)
          ->whereIn('id',$data['delete_image_ids'])
          ->get();

        EventImage::where('event_id',$id)->whereIn('id',$data['delete_image_ids'])->delete();
        unset($data['delete_image_ids']);
    }
    
    $event->update($data);
    $event->save();

    DB::commit();
    foreach($photosToDelete as $temp)
    {
      ImageService::delete(public_path().$temp->image);
    }
  }

  public static function read($id,$language)
  {
    $event = Event::where('id',$id)->firstOrFail();
    $images = $event->images()->get(['id','image']);
    $translation = $event->translation($language)->select([
        'title',
        'description',
        'location'
    ])->first();

    return $event->toArray() + $translation->toArray() + ["images" =>$images];
  }

  public static function show($id)
  {
    $event = Event::where('id',$id)->firstOrFail();
    $images = $event->images()->get(['id','image']);
    $translationNep = $event->translation('nepali')->select([
      'title',
      'description',
      'location'
    ])->first();

    $translationEng = $event->translation('english')->select([
      'title',
      'description',
      'location'
    ])->first();

    return $event->toArray() + ['nepali' => $translationNep->toArray()] + ['english' => $translationEng->toArray()] + ["images" =>$images];
  }

  public static function delete($id)
  {
      return Event::where('id',$id)->firstOrFail()->delete();
  }
}