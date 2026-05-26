<?php

namespace App\Http\Controllers;

use App\Models\AnimalGroup;
use App\Models\Mudhohi;
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MudhohiController extends Controller
{
    public function index()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        
        $mudhohis = Mudhohi::with('animalGroup')
            ->where('period_id', $activePeriod->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Mudhohis/Index', [
            'mudhohis' => $mudhohis,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function create()
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();
        return Inertia::render('Mudhohis/Create', [
            'activePeriod' => $activePeriod,
        ]);
    }

    public function store(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'type' => 'required|in:Cow,Goat',
            'is_full_animal' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($validated, $activePeriod) {
            $animalGroupId = null;

            if ($validated['type'] === 'Cow') {
                if ($validated['is_full_animal']) {
                    // Create a new full group for this person
                    $animalGroupId = $this->createNewGroup($activePeriod, 'Cow', true);
                } else {
                    // Patungan: find available group (not full, < 7 members)
                    $group = AnimalGroup::where('period_id', $activePeriod->id)
                        ->where('type', 'Cow')
                        ->where('is_full', false)
                        ->withCount('mudhohis')
                        ->orderBy('sequence_number', 'asc')
                        ->first();

                    if (!$group) {
                        $animalGroupId = $this->createNewGroup($activePeriod, 'Cow', false);
                    } else {
                        $animalGroupId = $group->id;
                        // Check if this new member will make it full
                        if ($group->mudhohis_count + 1 >= 7) {
                            $group->update(['is_full' => true]);
                        }
                    }
                }
            } else {
                // Goat: 1 person = 1 animal group (to track slaughter order)
                $animalGroupId = $this->createNewGroup($activePeriod, 'Goat', true);
            }

            Mudhohi::create([
                ...$validated,
                'period_id' => $activePeriod->id,
                'animal_group_id' => $animalGroupId,
            ]);

            return redirect()->route('mudhohis.index')->with('success', 'Mudhohi berhasil didaftarkan.');
        });
    }

    private function createNewGroup(Period $period, string $type, bool $isFull): int
    {
        $lastGroup = AnimalGroup::where('period_id', $period->id)
            ->where('type', $type)
            ->orderBy('sequence_number', 'desc')
            ->first();

        $nextSequence = ($lastGroup?->sequence_number ?? 0) + 1;
        $prefix = $type === 'Cow' ? 'Sapi' : 'Kambing';
        $name = $prefix . ' ' . str_pad($nextSequence, 2, '0', STR_PAD_LEFT);

        $group = AnimalGroup::create([
            'period_id' => $period->id,
            'name' => $name,
            'type' => $type,
            'sequence_number' => $nextSequence,
            'is_full' => $isFull,
        ]);

        return $group->id;
    }
}
