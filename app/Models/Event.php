<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $fillable=[
        'date',
        'start_time'
    ];

    public function translation($language)
    {
        return $this->hasOne(EventTranslation::class,'event_id','id')->where('event_translations.language','=',$language);
    }

    public function images()
    {
        return $this->hasMany(EventImage::class,'event_id','id');
    }
}
