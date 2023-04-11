<?php

namespace App\Services;
use Image;
use Illuminate\Support\Facades\File; 

class ImageService
{
  public static function resize($file,$width,$height) {
    $resize = Image::make($file->getRealPath())->resize($width, $height);        
    return $resize;
  }

  public static function store($file,$path)
  {
    $file->save($path);
  }

  public static function delete($filePath)
  {
    File::delete($filePath);
  }
}