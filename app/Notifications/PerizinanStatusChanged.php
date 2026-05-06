<?php

namespace App\Notifications;

use App\Models\Perizinan;
use App\Notifications\Channels\WhatsAppChannel;
use App\Support\WhatsApp\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PerizinanStatusChanged extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Perizinan $perizinan,
        public readonly string $oldStatus,
    )
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (config('services.whatsapp.enabled') && method_exists($notifiable, 'routeNotificationForWhatsApp')) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toArray(object $notifiable): array
    {
        $jenis = \App\Models\Perizinan::jenisLabel($this->perizinan->jenis);
        $status = \App\Models\Perizinan::statusLabel($this->perizinan->status);

        return [
            'title' => __('Status perizinan berubah'),
            'body' => __('Pengajuan :jenis pada :tanggal sekarang :status.', [
                'jenis' => $jenis,
                'tanggal' => $this->perizinan->tanggal?->format('Y-m-d') ?? '-',
                'status' => $status,
            ]),
            'url' => route('perizinan.edit', $this->perizinan),
            'perizinan_id' => $this->perizinan->id,
            'status' => $this->perizinan->status,
            'old_status' => $this->oldStatus,
        ];
    }

    public function toWhatsApp(object $notifiable): ?WhatsAppMessage
    {
        $to = $notifiable->routeNotificationForWhatsApp();
        if (! $to) {
            return null;
        }

        $jenis = \App\Models\Perizinan::jenisLabel($this->perizinan->jenis);
        $status = \App\Models\Perizinan::statusLabel($this->perizinan->status);
        $tanggal = $this->perizinan->tanggal?->format('d/m/Y') ?? '-';

        return new WhatsAppMessage(
            to: $to,
            message: __('[NumaEdu] Perizinan :jenis (:tanggal) -> :status', [
                'jenis' => $jenis,
                'tanggal' => $tanggal,
                'status' => $status,
            ])
        );
    }
}
