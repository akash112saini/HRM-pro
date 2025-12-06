<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public LeaveRequest $leaveRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(LeaveRequest $leaveRequest)
    {
        $this->leaveRequest = $leaveRequest;
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
        return (new MailMessage)
            ->subject('New Leave Request Submitted')
            ->greeting('Hello ' . $notifiable->name)
            ->line($this->leaveRequest->employee->full_name . ' has submitted a leave request.')
            ->line('Leave Type: ' . $this->leaveRequest->leaveType->name)
            ->line('Duration: ' . $this->leaveRequest->start_date->format('d M Y') . ' to ' . $this->leaveRequest->end_date->format('d M Y'))
            ->line('Total Days: ' . $this->leaveRequest->total_days)
            ->action('View Request', url('/leaves/' . $this->leaveRequest->id))
            ->line('Please review and approve/reject this request.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'leave_request_id' => $this->leaveRequest->id,
            'employee_name' => $this->leaveRequest->employee->full_name,
            'leave_type' => $this->leaveRequest->leaveType->name,
            'start_date' => $this->leaveRequest->start_date,
            'end_date' => $this->leaveRequest->end_date,
            'total_days' => $this->leaveRequest->total_days,
        ];
    }
}
