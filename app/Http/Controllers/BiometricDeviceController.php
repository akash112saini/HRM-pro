<?php

namespace App\Http\Controllers;

use App\Models\BiometricDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BiometricDeviceController extends Controller
{
    public function index()
    {
        $devices = BiometricDevice::orderBy('device_name')->get();
        return view('attendance.devices.index', compact('devices'));
    }

    public function create()
    {
        return view('attendance.devices.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        BiometricDevice::create(array_merge(
            $request->all(),
            [
                'tenant_id' => app('tenant.id'),
                'api_token' => Str::random(60),
                'last_sync_at' => now(),
            ]
        ));

        return redirect()->route('devices.index')
            ->with('success', 'Biometric device added successfully.');
    }

    public function edit(BiometricDevice $device)
    {
        return view('attendance.devices.edit', compact('device'));
    }

    public function update(Request $request, BiometricDevice $device)
    {
        $request->validate([
            'device_name' => 'required|string|max:255',
            'device_id' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $device->update($request->all());

        return redirect()->route('devices.index')
            ->with('success', 'Biometric device updated successfully.');
    }

    public function destroy(BiometricDevice $device)
    {
        $device->delete();
        return redirect()->route('devices.index')
            ->with('success', 'Biometric device deleted successfully.');
    }
}
