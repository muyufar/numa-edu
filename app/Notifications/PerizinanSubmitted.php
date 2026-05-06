<?php

namespace App\Notifications;

use App\Models\Perizinan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PerizinanSubmitted extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public readonly Perizinan $perizinan,
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
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $jenis = \App\Models\Perizinan::jenisLabel($this->perizinan->jenis);
        return [
            'title' => __('Pengajuan perizinan baru'),
            'body' => __('Ada pengajuan :jenis pada :tanggal yang perlu ditinjau.', [
                'jenis' => $jenis,
                'tanggal' => $this->perizinan->tanggal?->format('Y-m-d') ?? '-',
            ]),
            'url' => route('perizinan.edit', $this->perizinan),
            'perizinan_id' => $this->perizinan->id,
        ];
    }
}
