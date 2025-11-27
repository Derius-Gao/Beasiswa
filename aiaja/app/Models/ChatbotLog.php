<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'channel',
        'request_payload',
        'response_text',
        'metadata',
    ];

    protected $casts = [
        'request_payload' => 'array',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


