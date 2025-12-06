<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payslip - {{ date('F', mktime(0, 0, 0, $payroll->month, 1)) }} {{ $payroll->year }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
        }

        .payslip-title {
            font-size: 18px;
            margin-top: 10px;
        }

        .section {
            margin: 20px 0;
        }

        .section-title {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            background: #f0f0f0;
            padding: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table td,
        table th {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f8f8f8;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }

        .print-button {
            margin: 20px 0;
            text-align: center;
        }

        @media print {
            .print-button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="print-button">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #0d6efd; color: white; border: none; cursor: pointer; border-radius: 5px;">
            Print / Save as PDF
        </button>
    </div>

    <div class="header">
        <div class="company-name">{{ config('app.name', 'HRM-Pro') }}</div>
        <div class="payslip-title">Payslip for {{ date('F', mktime(0, 0, 0, $payroll->month, 1)) }} {{ $payroll->year }}
        </div>
    </div>

    <div class="section">
        <div class="section-title">Employee Information</div>
        <table>
            <tr>
                <td><strong>Employee Name:</strong></td>
                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td><strong>Employee ID:</strong></td>
                <td>{{ $employee->employee_id }}</td>
            </tr>
            <tr>
                <td><strong>Designation:</strong></td>
                <td>{{ $employee->designation->title ?? 'N/A' }}</td>
                <td><strong>Department:</strong></td>
                <td>{{ $employee->department->name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Join Date:</strong></td>
                <td>{{ $employee->join_date ? \Carbon\Carbon::parse($employee->join_date)->format('d M, Y') : 'N/A' }}
                </td>
                <td><strong>Payment Date:</strong></td>
                <td>{{ now()->format('d M, Y') }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Earnings</div>
        <table>
            <tr>
                <th>Component</th>
                <th class="text-right">Amount</th>
            </tr>
            <tr>
                <td>Basic Salary</td>
                <td class="text-right">{{ number_format($payroll->basic_salary, 2) }}</td>
            </tr>
            @php
                $allowances = $payroll->gross_salary - $payroll->basic_salary;
            @endphp
            @if($allowances > 0)
                <tr>
                    <td>Allowances</td>
                    <td class="text-right">{{ number_format($allowances, 2) }}</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Gross Salary</td>
                <td class="text-right">{{ number_format($payroll->gross_salary, 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Deductions</div>
        <table>
            <tr>
                <th>Component</th>
                <th class="text-right">Amount</th>
            </tr>
            @if(is_array($payroll->deductions) && count($payroll->deductions) > 0)
                @foreach($payroll->deductions as $type => $amount)
                    <tr>
                        <td>{{ $type }}</td>
                        <td class="text-right">{{ number_format($amount, 2) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="2">No deductions</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total Deductions</td>
                <td class="text-right">{{ number_format(array_sum($payroll->deductions ?? []), 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <tr class="total-row" style="font-size: 18px;">
                <td><strong>NET SALARY</strong></td>
                <td class="text-right"><strong>{{ number_format($payroll->net_salary, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 50px; text-align: center; font-size: 12px; color: #666;">
        This is a computer-generated payslip and does not require a signature.
    </div>
</body>

</html>