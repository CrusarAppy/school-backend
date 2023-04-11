<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhotoGallery extends Model
{
    use HasFactory;

    protected $table = 'photo_gallery';

    public $timestamps = true;

    protected $fillable = ['title'];

    public function translation($language)
    {
        return $this->hasOne(PhotoGalleryTranslation::class,'photo_gallery_id','id')->where('photo_gallery_translations.language','=',$language);
    }

    public function images()
    {
        return $this->hasMany(Photo::class,'photo_gallery_id','id');
    }
}
