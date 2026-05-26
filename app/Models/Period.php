<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    /** @use HasFactory<\Database\Factories\PeriodFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active',
        'cow_patungan_cost',
        'goat_operational_cost',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'cow_patungan_cost' => 'decimal:2',
        'goat_operational_cost' => 'decimal:2',
    ];
}
