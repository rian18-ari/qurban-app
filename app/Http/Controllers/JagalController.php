<?php

namespace App\Http\Controllers;

use App\Events\AnimalStatusUpdated;
use App\Jobs\SendWhatsAppMessage;
use App\Models\AnimalGroup;
use App\Models\Period;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JagalController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        $animals = AnimalGroup::where('period_id', $activePeriod->id)
            ->orderBy('sequence_number', 'asc')
            ->get();

        return Inertia::render('Jagal/Dashboard', [
            'animals' => $animals,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function updateStatus(Request $request, AnimalGroup $animal)
    {
        $validated = $request->validate([
            'status' => 'required|in:Antre,Disembelih,Dikuliti,Selesai Cacah',
        ]);

        $animal->update(['status' => $validated['status']]);

        // Broadcast real-time update
        broadcast(new AnimalStatusUpdated($animal))->toOthers();

        // WA Blast to Mudhohis in this group
        if ($validated['status'] === 'Disembelih') {
            $animal->load('mudhohis');
            foreach ($animal->mudhohis as $mudhohi) {
                $message = "Assalamu'alaikum Bpk/Ibu {$mudhohi->name}, menginfokan bahwa hewan qurban Anda ({$animal->name}) telah disembelih. Semoga berkah.";
                SendWhatsAppMessage::dispatch($mudhohi->phone_number, $message);
            }
        }

        return redirect()->back();
    }
}
