<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeBanking;
use Illuminate\Http\Request;

class EmployeeBankingController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'ifsc_code' => 'required|string|max:20',
            'branch_name' => 'nullable|string|max:255',
        ]);

        $employee->bankingDetails()->create($validated);

        return back()->with('success', 'Banking details added successfully.');
    }
}
