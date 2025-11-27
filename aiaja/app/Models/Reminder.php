<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_id',
        'user_id',
        'type',
        'channel',
        'message',
        'remind_date',
        'is_sent',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'remind_date' => 'date',
            'is_sent' => 'boolean',
            'sent_at' => 'datetime',
        ];
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
