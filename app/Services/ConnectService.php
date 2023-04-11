<?php

namespace App\Services;
use DB;
use App\Models\ConnectWithUs;

class ConnectService
{
  public static function create($data)
  {
    $connect = new ConnectWithUs;
    $connect->fill($data);
    $connect->save();
  }

  public static function all()
  {
    $data = DB::table('connect_with_us')
              ->select(['id','name','phone_number','address','message','read_status','email','subject','created_at'])
              ->orderBy('created_at','DESC')
              ->paginate(10);
    $res = [
      "connect_with_us" => $data->values(),
      "total" => $data->total(),
      "current_page" => $data->currentPage(),
      "last_page" => $data->lastPage()
    ];
    return $res;
  }

  public static function readMessage($id)
  {
    DB::table('connect_with_us')->where('id',$id)->update(['read_status'=>1]);
  }

  public static function show($id)
  {
    return DB::table('connect_with_us')->firstOrFail()->where('id',$id)->get();
  }

}