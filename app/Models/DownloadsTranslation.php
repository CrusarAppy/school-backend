<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DownloadsTranslation extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'downloads_translations';

    protected $fillable= [
        'downloads_id',
        'language',
        'title',
    ];
}
