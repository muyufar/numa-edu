<?php

namespace App\Services;

use App\Models\PpdbRegistration;
use App\Models\User;
use App\Notifications\PpdbRegistrationSubmitted;
use Illuminate\Support\Facades\Notification;

class PpdbNotificationService
{
    public function notifyNewRegistration(PpdbRegistration $registration): void
    {
        $registration->loadMissing('sekolah:id,nama');

        $this->notifySchoolAdmins($registration);
    }

    private function notifySchoolAdmins(PpdbRegistration $registration): void
    {
        $sekolahId = (int) $registration->sekolah_id;

        $admins = User::query()
            ->where(function ($q) use ($sekolahId): void {
                $q->whereHas('roles', fn ($r) => $r->where('name', 'super_admin'))
                    ->orWhere(function ($q2) use ($sekolahId): void {
                        $q2->where('sekolah_id', $sekolahId)
                            ->whereHas('roles', fn ($r) => $r->where('name', 'admin'));
                    });
            })
            ->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new PpdbRegistrationSubmitted($registration));
    }
}
