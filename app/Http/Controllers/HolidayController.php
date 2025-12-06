<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date')->get();
        return view('settings.holidays.index', compact('holidays'));
    }

    public function create()
    {
        return view('settings.holidays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'is_optional' => 'boolean',
            'description' => 'nullable|string',
        ]);

        Holiday::create(array_merge(
            $request->all(),
            ['tenant_id' => auth()->user()->tenant_id]
        ));

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday created.');
    }

    public function edit(Holiday $holiday)
    {
        return view('settings.holidays.edit', compact('holiday'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'is_optional' => 'boolean',
            'description' => 'nullable|string',
        ]);

        $holiday->update($request->all());

        return redirect()->route('admin.holidays.index')->with('success', 'Holiday updated.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->route('admin.holidays.index')->with('success', 'Holiday deleted.');
    }
}
