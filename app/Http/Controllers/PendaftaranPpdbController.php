<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePublicPpdbRequest;
use App\Models\PpdbRegistration;
use App\Services\PpdbNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PendaftaranPpdbController extends Controller
{
    public function __construct(
        private PpdbNotificationService $ppdbNotifications
    ) {}

    public function create(): View
    {
        return view('ppdb.daftar');
    }

    public function store(StorePublicPpdbRequest $request): RedirectResponse
    {
        $registration = PpdbRegistration::query()->create(array_merge(
            $request->validated(),
            ['status' => 'submitted']
        ));

        $this->ppdbNotifications->notifyNewRegistration($registration);

        return redirect()
            ->route('ppdb.daftar')
            ->with('status', __('Pendaftaran berhasil dikirim. Tim sekolah akan menghubungi jika diperlukan.'));
    }
}
