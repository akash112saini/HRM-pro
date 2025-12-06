<?php

namespace App\Http\Controllers;

use App\Models\TaxSlab;
use Illuminate\Http\Request;

class TaxSlabController extends Controller
{
    public function index()
    {
        $taxSlabs = TaxSlab::orderBy('min_amount')->get();
        return view('settings.tax-slabs.index', compact('taxSlabs'));
    }

    public function create()
    {
        return view('settings.tax-slabs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'financial_year' => 'required|string',
            'min_amount' => 'required|numeric',
            'max_amount' => 'nullable|numeric',
            'tax_rate' => 'required|numeric',
        ]);

        TaxSlab::create(array_merge(
            $request->all(),
            ['tenant_id' => app('tenant.id')]
        ));

        return redirect()->route('tax-slabs.index')->with('success', 'Tax Slab created.');
    }

    public function destroy(TaxSlab $taxSlab)
    {
        $taxSlab->delete();
        return redirect()->route('tax-slabs.index')->with('success', 'Tax Slab deleted.');
    }
}
