<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scholarship extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'amount',
        'provider',
        'criteria',
        'application_deadline',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'criteria' => 'json',
            'application_deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function recommendations()
    {
        return $this->hasMany(ScholarshipRecommendation::class);
    }
}
