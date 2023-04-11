<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $table = 'notices';

    protected $fillable=[
        'privacy'
    ];

    public function translation($language)
    {
        return $this->hasOne(NoticeTranslation::class,'notice_id','id')->where('notice_translations.language','=',$language);
    }

    public function images()
    {
        return $this->hasMany(NoticeImage::class,'notice_id','id');
    }
}
