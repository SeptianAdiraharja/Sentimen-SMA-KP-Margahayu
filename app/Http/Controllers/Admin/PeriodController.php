<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Period;

class PeriodController extends Controller
{
    public function index()
    {
        $periods = Period::withCount('responses')->orderBy('id', 'desc')->paginate(10);
        return view('admin.periods.index', compact('periods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_active']) {
            // Nonaktifkan periode lain jika yang ini diaktifkan
            Period::where('is_active', true)->update(['is_active' => false]);
        }

        Period::create($validated);

        return redirect()->route('admin.periods.index')->with('success', 'Periode kuesioner berhasil ditambahkan.');
    }

    public function update(Request $request, Period $period)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        $period->update($validated);

        return redirect()->route('admin.periods.index')->with('success', 'Periode kuesioner berhasil diperbarui.');
    }

    public function toggleStatus(Period $period)
    {
        if (!$period->is_active) {
            Period::where('is_active', true)->update(['is_active' => false]);
            $period->is_active = true;
        } else {
            $period->is_active = false;
        }
        $period->save();

        return redirect()->route('admin.periods.index')->with('success', 'Status periode berhasil diubah.');
    }

    public function destroy(Period $period)
    {
        $period->delete();
        return redirect()->route('admin.periods.index')->with('success', 'Periode berhasil dihapus.');
    }
}
