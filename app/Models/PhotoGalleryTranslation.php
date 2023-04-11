<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoGalleryTranslation extends Model
{
    use HasFactory;

    protected $table = 'photo_gallery_translations';

    public $timestamps = false;

    protected $fillable = [
        'photo_gallery_id',
        'language',
        'title'
    ];
}
