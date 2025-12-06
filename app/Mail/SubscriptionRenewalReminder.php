<?php

namespace App\Mail;

use App\Models\Tenant;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubscriptionRenewalReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public int $daysRemaining
    ) {
    }

    public function envelope(): Envelope
    {
        $template = EmailTemplate::where('key', 'subscription_renewal_reminder')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            return new Envelope(
                subject: 'Subscription Renewal Reminder - ' . $this->tenant->company_name,
            );
        }

        $rendered = $template->render($this->getTemplateData());

        return new Envelope(
            subject: $rendered['subject'],
        );
    }

    public function content(): Content
    {
        $template = EmailTemplate::where('key', 'subscription_renewal_reminder')
            ->where('is_active', true)
            ->first();

        if (!$template) {
            // Fallback content if template not found
            $data = $this->getTemplateData();
            $fallbackHtml = "
                <h1>Subscription Renewal Reminder</h1>
                <p>Dear {$data['company_name']},</p>
                <p>Your subscription will expire in {$data['days_remaining']} days on {$data['expiry_date']}.</p>
                <p>Please renew your subscription to continue using our services.</p>
            ";
            return new Content(
                htmlString: $fallbackHtml,
            );
        }

        $rendered = $template->render($this->getTemplateData());

        return new Content(
            htmlString: $rendered['body'],
        );
    }

    protected function getTemplateData(): array
    {
        return [
            'company_name' => $this->tenant->company_name,
            'plan_name' => ucfirst(str_replace('_', ' ', $this->tenant->subscription_plan)),
            'expiry_date' => $this->tenant->subscription_expires_at->format('F d, Y'),
            'days_remaining' => $this->daysRemaining,
            'renewal_url' => url('/super-admin/subscriptions'),
            'current_year' => date('Y'),
        ];
    }
}
