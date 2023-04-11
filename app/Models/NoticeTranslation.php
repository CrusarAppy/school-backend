<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NoticeTranslation extends Model
{
    use HasFactory;

    protected $table = 'notice_translations';

    public $timestamps = false;

    protected $fillable= [
        'notice_id',
        'language',
        'title',
        'description'
    ];
}
