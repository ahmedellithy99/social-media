<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class CommentNotification extends Notification
{
    use Queueable;

    public $commentedUser;
    public $postBody;
    public $postId;
    public $userId;
    /**
     * Create a new notification instance.
     */
    public function __construct($commentedUser, $postBody, $postId, $userId)
    {
        $this->commentedUser = $commentedUser;
        $this->postBody = $postBody;
        $this->postId = $postId;
        $this->userId = $userId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'text' => $this->commentedUser . " Comment Your Post: " . mb_substr($this->postBody, 0, 20 , 'UTF-8') ?? '',
            'post_id' => $this->postId,
            'user_id' => $this->userId,
        ];
    }
}
