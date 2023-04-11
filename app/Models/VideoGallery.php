<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGallery extends Model
{
    use HasFactory;

    protected $table = 'video_gallery';

    public $timestamps = true;

    public function translation($language)
    {
        return $this->hasOne(VideoGalleryTranslation::class,'video_id','id')->where('video_gallery_translations.language','=',$language);
    }
}
