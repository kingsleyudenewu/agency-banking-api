<?php

namespace App;



class Message extends BaseModel
{
    const STATUS_NEW = 'new';
    const STATUS_SENT = 'sent';


    protected $fillable = [
        'status',
        'message',
        'subject',
        'message_type',
        'user_id',
        'sender'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender');
    }
}
