<?php

namespace App\Services;
use Mail;

class MailService
{
  public static function send($from,$to,$body,$subject)
    {
        Mail::send([], [], function($message) use($from,$to,$body,$subject){            
            $message->to($to, '')->subject
                ($subject)->setBody($body);
            $message->from($from,\config('app.name'));
        });
    }
}