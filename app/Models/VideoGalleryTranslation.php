<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoGalleryTranslation extends Model
{
    use HasFactory;

    protected $table = 'video_gallery_translations';

    public $timestamps = false;

    protected $fillable = [
        'video_id',
        'language',
        'title'
    ];
}
