<?php

namespace App\Notifications;

use App\Models\PpdbRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PpdbRegistrationSubmitted extends Notification
{
    use Queueable;

    public function __construct(
        public readonly PpdbRegistration $registration,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => __('Pendaftaran PPDB baru'),
            'body' => __(':nama mendaftar via PPDB. Status: :status.', [
                'nama' => $this->registration->nama,
                'status' => PpdbRegistration::statusLabel($this->registration->status),
            ]),
            'url' => route('ppdb.show', $this->registration),
            'ppdb_registration_id' => $this->registration->id,
            'status' => $this->registration->status,
        ];
    }
}
