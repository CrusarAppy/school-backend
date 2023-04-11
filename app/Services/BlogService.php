<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\BlogImage;
use DB;
use App\Models\BlogTranslation;
use Str;

class BlogService
{
  public static $imagePath = '/uploads/blogs/';

  public static function all($language)
  {
    $blogs = DB::table('blogs')
                ->join(DB::raw("(select title,blog_id from blog_translations where language ='$language') translations "),function($join){
                    $join->on('blogs.id','=','translations.blog_id');
                })
                ->orderBy('blogs.created_at','DESC')
                ->orderBy('blogs.id','ASC')
                ->select([
                    'blogs.id',
                    'translations.title',
                    'blogs.created_at',
                ])
                ->paginate(10);

    foreach($blogs as $blog)
    {
      $blog->image = DB::table('blog_images')
                              ->where('blog_id','=',$blog->id)
                              ->pluck('image')
                              ->first();
    }

    $res = [
      "blogs" => $blogs->values(),
      "total" => $blogs->total(),
      "current_page" => $blogs->currentPage(),
      "last_page" => $blogs->lastPage()
  ];

    return $res;
  }

  public static function create($data)
  {
    DB::beginTransaction();
    $blog = new Blog;
    $blog->save();

    if(array_key_exists('english',$data))
    {
        BlogTranslation::updateOrCreate([
                'blog_id'=> $blog->id,
                'language'=>'english'
            ],$data['english']
        );

    }

    if(array_key_exists('nepali',$data))
    {
        BlogTranslation::updateOrCreate([
                'blog_id'=> $blog->id,
                'language'=>'nepali'
            ],$data['nepali']
        );
    }

    if(array_key_exists('images',$data))
    {
      foreach($data['images'] as $file)
      {
          $imageName = time().Str::random(4).'.'.$file->extension();

          $blogImage = new BlogImage;
          $blogImage->blog_id = $blog->id;
          $blogImage->image = self::$imagePath.$imageName;
          $blogImage->save();
          $file->move(public_path(self::$imagePath),$imageName);
      }
    }

    DB::commit();
  }

  public static function update($id, array $data)
  {
    DB::beginTransaction();
    $blog = Blog::where('id',$id)->firstOrFail();

    if(array_key_exists("english",$data))
    {        
      BlogTranslation::updateOrCreate([
              'blog_id'=>$id,
              'language'=>'english'
          ],$data['english']
      );
      unset($data['english']);

    }

    if(array_key_exists("nepali",$data))
    {
      BlogTranslation::updateOrCreate([
              'blog_id'=>$id,
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

          $blogImage = new BlogImage;
          $blogImage->blog_id = $blog->id;
          $blogImage->image = self::$imagePath.$imageName;
          $blogImage->save();
          $file->move(public_path(self::$imagePath),$imageName);
      }
      unset($data['images']);
    }
    
    $photosToDelete = [];
    if(array_key_exists('delete_image_ids',$data))
    {
        $photosToDelete = BlogImage::select('image')
          ->where('blog_id',$id)
          ->whereIn('id',$data['delete_image_ids'])
          ->get();

        BlogImage::where('blog_id',$id)->whereIn('id',$data['delete_image_ids'])->delete();
        unset($data['delete_image_ids']);
    }
    
    $blog->update($data);
    $blog->save();

    DB::commit();
    foreach($photosToDelete as $temp)
    {
      ImageService::delete(public_path().$temp->image);
    }
  }

  public static function read($id,$language)
  {
    $blog = Blog::where('id',$id)->firstOrFail();
    $images = $blog->images()->get(['id','image']);
    $translation = $blog->translation($language)->select([
        'title',
        'description'
    ])->first();

    return $blog->toArray() + $translation->toArray() + ["images" =>$images];
  }

  public static function show($id)
  {
    $blog = Blog::where('id',$id)->first();
    $images = $blog->images()->get(['id','image']);
    $translationNep = $blog->translation('nepali')->select([
      'title',
      'description'
    ])->first();

    $translationEng = $blog->translation('english')->select([
      'title',
      'description'
    ])->first();

    return $blog->toArray() + ['nepali' => $translationNep->toArray()] + ['english' => $translationEng->toArray()] + ["images" =>$images];
  }

  public static function delete($id)
  {
      return Blog::where('id',$id)->firstOrFail()->delete();
  }
}