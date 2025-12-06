<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use App\Services\PayrollGeneratorService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    protected PayrollGeneratorService $payrollService;

    public function __construct(PayrollGeneratorService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    /**
     * Display payroll listing.
     */
    public function index(Request $request)
    {
        $query = Payroll::with(['employee', 'salaryStructure']);

        // Filter by month/year
        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(50);

        $employees = Employee::active()->orderBy('first_name')->get();

        return view('payroll.index', compact('payrolls', 'employees'));
    }

    /**
     * Show payroll generation form.
     */
    public function create()
    {
        $employees = Employee::active()->with('currentSalary')->get();
        return view('payroll.create', compact('employees'));
    }

    /**
     * Generate payroll.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        try {
            if ($request->filled('employee_ids')) {
                // Generate for selected employees
                $results = $this->payrollService->generateBulkPayroll(
                    $request->month,
                    $request->year,
                    $request->employee_ids
                );
            } else {
                // Generate for all employees
                $results = $this->payrollService->generateBulkPayroll(
                    $request->month,
                    $request->year
                );
            }

            $successCount = $results->where('status', 'success')->count();
            $failureCount = $results->where('status', 'failed')->count();

            return redirect(roleRoute('payroll.index'))
                ->with('success', "Payroll generated: {$successCount} successful, {$failureCount} failed")
                ->with('results', $results);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to generate payroll: ' . $e->getMessage());
        }
    }

    /**
     * View payroll details.
     */
    public function show($id)
    {
        $payroll = Payroll::with(['employee', 'salaryStructure'])->findOrFail($id);
        return view('payroll.show', compact('payroll'));
    }

    /**
     * Download payslip PDF.
     */
    public function downloadPDF($id)
    {
        $payroll = Payroll::findOrFail($id);

        $pdfPath = $this->payrollService->generatePayslipPDF($payroll);

        return response()->download($pdfPath);
    }

    /**
     * Mark payroll as paid.
     */
    public function markAsPaid(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'transaction_reference' => 'nullable|string',
        ]);

        $payroll = Payroll::findOrFail($id);

        $this->payrollService->markAsPaid(
            $payroll,
            $request->payment_method,
            $request->transaction_reference
        );

        return redirect()->back()->with('success', 'Payroll marked as paid');
    }
}
