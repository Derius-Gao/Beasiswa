<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ScholarshipRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'scholarship_id',
        'match_score',
        'reason',
        'is_notified',
        'recommended_at',
    ];

    protected function casts(): array
    {
        return [
            'match_score' => 'decimal:2',
            'reason' => 'json',
            'is_notified' => 'boolean',
            'recommended_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}
