<?php

namespace App\Http\Controllers;

use App\Models\DistributionSession;
use App\Models\Period;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DistributionSessionController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $sessions = DistributionSession::where('period_id', $activePeriod->id)
            ->withCount('mustahiqs')
            ->get()
            ->map(function ($session) {
                $session->remaining_quota = $session->quota - $session->mustahiqs_count;
                return $session;
            });

        return Inertia::render('Sessions/Index', [
            'sessions' => $sessions,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function store(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'quota' => 'required|integer|min:1',
        ]);

        DistributionSession::create([
            ...$validated,
            'period_id' => $activePeriod->id,
        ]);

        return redirect()->back()->with('success', 'Sesi antrean berhasil dibuat.');
    }
}
