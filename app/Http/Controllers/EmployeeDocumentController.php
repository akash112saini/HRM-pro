<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function store(Request $request, Employee $employee)
    {
        $request->validate([
            'document_type' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'expiry_date' => 'nullable|date',
        ]);

        if ($request->hasFile('document_file')) {
            $path = $request->file('document_file')->store('employee-documents', 'public');

            $employee->documents()->create([
                'document_type' => $request->document_type,
                'file_path' => $path,
                'expiry_date' => $request->expiry_date,
            ]);

            return back()->with('success', 'Document uploaded successfully.');
        }

        return back()->with('error', 'File upload failed.');
    }
}
