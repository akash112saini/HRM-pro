<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAssignment;
use App\Models\Employee;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    /**
     * Display asset listing.
     */
    public function index(Request $request)
    {
        $query = Asset::with('currentAssignment.employee');

        // Filter by type
        if ($request->filled('asset_type')) {
            $query->where('asset_type', $request->asset_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_name', 'like', "%{$search}%")
                    ->orWhere('asset_tag', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('asset_name')->paginate(20);

        return view('assets.index', compact('assets'));
    }

    /**
     * Show asset creation form.
     */
    public function create()
    {
        return view('assets.create');
    }

    /**
     * Store new asset.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_type' => 'required|in:laptop,phone,tablet,vehicle,furniture,other',
            'asset_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'asset_tag' => 'required|string|max:255|unique:assets,asset_tag',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'warranty_expires_at' => 'nullable|date',
        ]);

        Asset::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect()->route('assets.index')
            ->with('success', 'Asset created successfully');
    }

    /**
     * Display asset details.
     */
    public function show($id)
    {
        $asset = Asset::with('assignments.employee')->findOrFail($id);
        return view('assets.show', compact('asset'));
    }

    /**
     * Assign asset to employee.
     */
    public function assign(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'remarks' => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($id);

        // Check if asset is already assigned
        if ($asset->status === 'assigned') {
            return redirect()->back()
                ->with('error', 'Asset is already assigned');
        }

        AssetAssignment::create([
            'tenant_id' => $asset->tenant_id,
            'asset_id' => $asset->id,
            'employee_id' => $request->employee_id,
            'assigned_at' => now(),
            'assigned_by' => auth()->id(),
            'remarks' => $request->remarks,
        ]);

        $asset->update(['status' => 'assigned']);

        return redirect()->back()
            ->with('success', 'Asset assigned successfully');
    }

    /**
     * Return asset from employee.
     */
    public function return(Request $request, $id)
    {
        $request->validate([
            'return_condition' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($id);
        $assignment = $asset->currentAssignment;

        if (!$assignment) {
            return redirect()->back()
                ->with('error', 'Asset is not currently assigned');
        }

        $assignment->update([
            'returned_at' => now(),
            'return_condition' => $request->return_condition,
            'remarks' => $request->remarks,
        ]);

        $asset->update([
            'status' => 'available',
            'condition' => $request->return_condition,
        ]);

        return redirect()->back()
            ->with('success', 'Asset returned successfully');
    }

    /**
     * Update asset.
     */
    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);

        $request->validate([
            'asset_name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'status' => 'required|in:available,assigned,under_repair,retired',
            'condition' => 'required|in:new,good,fair,poor',
        ]);

        $asset->update($request->all());

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully');
    }
}
