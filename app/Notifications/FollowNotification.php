<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class FollowNotification extends Notification implements ShouldQueue
{
    use Queueable ;
    
    public $reactedUser ;
    public $avatar_path;
    public $username;
    /**
     * Create a new notification instance.
     */
    public function __construct($reactedUser , $avatar_path , $username)
    {   
        $this->reactedUser = $reactedUser ;
        $this->avatar_path = $avatar_path;
        $this->username = $username;
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
            'text' => $this->reactedUser . " is following You" ,
            'avatar_url' => Storage::url($this->avatar_path),
            'username' => $this->username,
        ];
    }
}
