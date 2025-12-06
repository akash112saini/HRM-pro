<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function attendance()
    {
        return view('reports.attendance');
    }

    public function payroll()
    {
        return view('reports.payroll');
    }

    public function headcount()
    {
        return view('reports.headcount');
    }

    public function attrition()
    {
        return view('reports.attrition');
    }
}
