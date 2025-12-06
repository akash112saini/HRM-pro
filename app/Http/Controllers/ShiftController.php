<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::orderBy('name')->get();
        return view('shifts.index', compact('shifts'));
    }

    public function create()
    {
        return view('shifts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'working_days' => 'required|array',
        ]);

        Shift::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id'), 'is_active' => true]
        ));

        return redirect(roleRoute('shifts.index'))
            ->with('success', 'Shift created successfully.');
    }

    public function edit(Shift $shift)
    {
        return view('shifts.edit', compact('shift'));
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
            'working_days' => 'required|array',
        ]);

        $shift->update($request->all());

        return redirect(roleRoute('shifts.index'))
            ->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect(roleRoute('shifts.index'))
            ->with('success', 'Shift deleted successfully.');
    }
}
