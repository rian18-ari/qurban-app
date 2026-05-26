<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'period_id',
        'description',
        'type',
        'amount',
        'transaction_date',
        'category',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
