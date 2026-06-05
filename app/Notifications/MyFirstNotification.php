<?php
  
namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Mail;
class MyFirstNotification extends Notification implements ShouldQueue
{
    use Queueable;
  
    private $details;
   
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
		
        $this->details = $details;
    }
   
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }
   
    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
	 
	
   public function toMail($notifiable)
    {
			/*return (new MailMessage)
					->from('test@example.com', 'Example') 
					->cc('hiten@virtualheight.com', 'Example')
					->greeting($this->details['greeting'])
                    ->line($this->details['body'])
                    ->action($this->details['actionText'], $this->details['actionURL'])
                    ->line("Thank you for visiting codechief.org!");*/
    }
  
    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
		
        return [
			
            'body' => $this->details['body'],
			'action' => $this->details['actionURL'],
			'record_id' => $this->details['record_id']
        ];
    }
	
}