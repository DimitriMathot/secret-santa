<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Participant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecretSantaAssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private Event $event,
        private Participant $participant
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('participant.show', ['token' => $this->participant->token]);

        return (new MailMessage)
            ->subject('🎅 Votre Secret Santa est prêt !')
            ->greeting('Bonjour ' . $this->participant->name . ' !')
            ->line('Les assignations pour l\'événement **' . $this->event->name . '** ont été générées.')
            ->line('Cliquez sur le lien ci-dessous pour découvrir à qui vous devez offrir un cadeau :')
            ->action('Découvrir mon Secret Santa', $url)
            ->line('Ce lien est unique et confidentiel. Ne le partagez avec personne.')
            ->salutation('Joyeux Noël ! 🎄');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'participant_id' => $this->participant->id,
        ];
    }
}
