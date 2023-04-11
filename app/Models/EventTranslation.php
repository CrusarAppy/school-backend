<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTranslation extends Model
{
    use HasFactory;
    
    protected $table = 'event_translations';

    public $timestamps = false;

    protected $fillable= [
        'event_id',
        'language',
        'title',
        'description',
        'location'
    ];
}
