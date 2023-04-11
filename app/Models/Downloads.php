<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Downloads extends Model
{
    use HasFactory;

    protected $table = 'downloads';

    public function translation($language)
    {
        return $this->hasOne(DownloadsTranslation::class,'downloads_id','id')->where('downloads_translations.language','=',$language);
    }
}
