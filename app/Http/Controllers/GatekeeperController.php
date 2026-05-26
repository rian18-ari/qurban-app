<?php

namespace App\Http\Controllers;

use App\Models\Mustahiq;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GatekeeperController extends Controller
{
    public function index()
    {
        return Inertia::render('Gatekeeper/Scanner');
    }

    public function validateCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required']);
        
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $mustahiq = Mustahiq::with('distributionSession')
            ->where('period_id', $activePeriod->id)
            ->where('coupon_code', $request->coupon_code)
            ->first();

        if (!$mustahiq) {
            return response()->json([
                'success' => false,
                'type' => 'not_found',
                'message' => 'Kode Kupon tidak valid!'
            ], 404);
        }

        if ($mustahiq->status === 'Received') {
            return response()->json([
                'success' => false,
                'type' => 'already_taken',
                'message' => 'Kupon SUDAH PERNAH di-scan!',
                'data' => $mustahiq
            ], 400);
        }

        // Validate Session
        $session = $mustahiq->distributionSession;
        $now = Carbon::now();
        $startTime = Carbon::createFromFormat('H:i:s', $session->start_time);
        $endTime = Carbon::createFromFormat('H:i:s', $session->end_time);

        if (!$now->between($startTime, $endTime)) {
            return response()->json([
                'success' => false,
                'type' => 'wrong_session',
                'message' => "Sesi SALAH! Jadwal warga ini: {$session->name} ({$session->start_time} - {$session->end_time})",
                'data' => $mustahiq
            ], 400);
        }

        // Success - Mark as Received
        $mustahiq->update(['status' => 'Received']);

        return response()->json([
            'success' => true,
            'message' => 'Valid! Silakan berikan daging.',
            'data' => $mustahiq
        ]);
    }
}
