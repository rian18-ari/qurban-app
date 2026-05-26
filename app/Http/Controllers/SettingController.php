<?php

namespace App\Http\Controllers;

use App\Models\Period;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $periods = Period::orderBy('name', 'desc')->get();
        return Inertia::render('Settings/Index', [
            'periods' => $periods,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:periods,name',
            'cow_patungan_cost' => 'required|numeric|min:0',
            'goat_operational_cost' => 'required|numeric|min:0',
        ]);

        if (Period::count() === 0) {
            $validated['is_active'] = true;
        }

        Period::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Period $period)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:periods,name,' . $period->id,
            'cow_patungan_cost' => 'required|numeric|min:0',
            'goat_operational_cost' => 'required|numeric|min:0',
        ]);

        $period->update($validated);

        return redirect()->back();
    }

    public function setActive(Period $period)
    {
        Period::query()->update(['is_active' => false]);
        $period->update(['is_active' => true]);

        return redirect()->back();
    }
}
