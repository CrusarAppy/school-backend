<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectWithUs extends Model
{
    use HasFactory;

    protected $table = 'connect_with_us';

    protected $fillable = [
        'name',
        'phone_number',
        'message',
        'address',
        'email',
        'subject',
        'read_status'
    ];
}
