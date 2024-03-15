<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class ReactionNotification extends Notification
{
    use Queueable;

    public $reactedUser ;
    public $postBody;
    public $postId;
    public $avatar_path;
    /**
     * Create a new notification instance.
     */
    public function __construct($reactedUser , $postBody , $postId , $avatar_path)
    {
        $this->reactedUser = $reactedUser ;
        $this->postBody = $postBody ;
        $this->postId = $postId;
        $this->avatar_path = $avatar_path;
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
            'text' => $this->reactedUser . " Liked Your Post: " . substr($this->postBody, 0 , 10),
            'avatar_url' => Storage::url($this->avatar_path),
            'post_id' => $this->postId
        ];
    }
}
