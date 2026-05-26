<?php

namespace App\Http\Controllers;

use App\Models\FinancialLog;
use App\Models\Period;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FinancialController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $logs = FinancialLog::where('period_id', $activePeriod->id)
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $summary = [
            'total_income' => $logs->where('type', 'Income')->sum('amount'),
            'total_expense' => $logs->where('type', 'Expense')->sum('amount'),
        ];
        $summary['balance'] = $summary['total_income'] - $summary['total_expense'];

        return Inertia::render('Financial/Index', [
            'logs' => $logs,
            'summary' => $summary,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function store(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'type' => 'required|in:Income,Expense',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'category' => 'nullable|string',
        ]);

        FinancialLog::create([
            ...$validated,
            'period_id' => $activePeriod->id,
        ]);

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }
}
