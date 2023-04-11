<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'blogs';

    protected $fillable=[
        'date'
    ];

    public function translation($language)
    {
        return $this->hasOne(BlogTranslation::class,'blog_id','id')->where('blog_translations.language','=',$language);
    }

    public function images()
    {
        return $this->hasMany(BlogImage::class,'blog_id','id');
    }
}
