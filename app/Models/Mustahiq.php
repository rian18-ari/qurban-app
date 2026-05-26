<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mustahiq extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'distribution_session_id',
        'name',
        'coupon_code',
        'address',
        'phone_number',
        'status',
    ];

    public function distributionSession()
    {
        return $this->belongsTo(DistributionSession::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
