<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributionSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'name',
        'start_time',
        'end_time',
        'quota',
    ];

    public function mustahiqs()
    {
        return $this->hasMany(Mustahiq::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
    
    public function getRemainingQuotaAttribute()
    {
        return $this->quota - $this->mustahiqs()->count();
    }
}
