<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::latest()->get();
        return view('super-admin.email-templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('super-admin.email-templates.edit', compact('emailTemplate'));
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'schedule_time' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $oldData = $emailTemplate->toArray();
        $emailTemplate->update($validated);

        ActivityLog::logActivity(
            'updated_email_template',
            $emailTemplate,
            $oldData,
            $emailTemplate->toArray(),
            "Updated email template: {$emailTemplate->name}"
        );

        return redirect()->route('super-admin.email-templates.index')
            ->with('success', 'Email template updated successfully!');
    }

    public function sendTest(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'test_email' => 'required|email',
            'company_name' => 'nullable|string|max:255',
        ]);

        // Prepare test data
        $testData = [
            'company_name' => $validated['company_name'] ?? 'Test Company',
            'plan_name' => 'Premium Plan',
            'expiry_date' => now()->addDays(7)->format('F d, Y'),
            'days_remaining' => '7',
            'renewal_url' => url('/'),
            'current_year' => date('Y'),
        ];

        // Render template
        $rendered = $emailTemplate->render($testData);

        // Send email using Mail::html for HTML content
        try {
            \Mail::html($rendered['body'], function ($message) use ($validated, $rendered) {
                $message->to($validated['test_email'])
                    ->subject($rendered['subject'])
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            ActivityLog::logActivity(
                'sent_test_email',
                $emailTemplate,
                null,
                ['email' => $validated['test_email']],
                "Sent test email to: {$validated['test_email']}"
            );

            return back()->with('success', 'Test email sent to ' . $validated['test_email']);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }
}
