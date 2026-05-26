<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnimalGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'name',
        'type',
        'sequence_number',
        'is_full',
        'status',
    ];

    public function mudhohis()
    {
        return $this->hasMany(Mudhohi::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
