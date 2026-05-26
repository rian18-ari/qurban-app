<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mudhohi extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'animal_group_id',
        'name',
        'phone_number',
        'address',
        'type',
        'is_full_animal',
    ];

    public function animalGroup()
    {
        return $this->belongsTo(AnimalGroup::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
