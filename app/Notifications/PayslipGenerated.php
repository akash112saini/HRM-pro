<?php

namespace App\Notifications;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayslipGenerated extends Notification implements ShouldQueue
{
    use Queueable;

    public Payroll $payroll;

    /**
     * Create a new notification instance.
     */
    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $monthYear = \Carbon\Carbon::create($this->payroll->year, $this->payroll->month, 1)->format('F Y');

        return (new MailMessage)
            ->subject('Payslip Generated - ' . $monthYear)
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your payslip for ' . $monthYear . ' has been generated.')
            ->line('Net Salary: ₹' . number_format($this->payroll->net_salary, 2))
            ->action('Download Payslip', url('/payroll/' . $this->payroll->id . '/download'))
            ->line('Please review your payslip and contact HR if you have any questions.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payroll_id' => $this->payroll->id,
            'month' => $this->payroll->month,
            'year' => $this->payroll->year,
            'net_salary' => $this->payroll->net_salary,
        ];
    }
}
