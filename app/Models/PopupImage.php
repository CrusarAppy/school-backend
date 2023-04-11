<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupImage extends Model
{
    use HasFactory;

    protected $table = "popup_images";

    protected $fillable = [
        'photo',
        'expiry_date'
    ];
}
