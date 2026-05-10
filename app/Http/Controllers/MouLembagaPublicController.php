<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignPublicLembagaMouRequest;
use App\Models\Cabang;
use App\Models\LembagaRegistration;
use App\Services\LembagaRegistrationDraftPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MouLembagaPublicController extends Controller
{
    public function show(Request $request, string $token): View|RedirectResponse
    {
        $reg = LembagaRegistration::query()->where('public_token', $token)->with('cabang')->firstOrFail();

        if ($reg->status === LembagaRegistration::STATUS_PENDING_REVIEW
            || $reg->status === LembagaRegistration::STATUS_APPROVED) {
            return redirect()
                ->route('public.lembaga-registrations.status', ['token' => $token])
                ->with('info', __('Permohonan Anda sudah masuk tahap berikutnya.'));
        }

        if ($reg->status === LembagaRegistration::STATUS_REJECTED) {
            abort(404);
        }

        return view('public.lembaga-mou', [
            'reg' => $reg,
        ]);
    }

    public function sign(SignPublicLembagaMouRequest $request, string $token, LembagaRegistrationDraftPdfService $pdfService): RedirectResponse
    {
        $reg = LembagaRegistration::query()->where('public_token', $token)->firstOrFail();

        if (! $reg->needsMou()) {
            return redirect()
                ->route('public.lembaga-registrations.status', ['token' => $token]);
        }

        $nomorSekolah = $request->validated()['mou_nomor_sekolah'];

        $cabangId = (int) ($reg->cabang_id ?? config('lembaga.default_cabang_id', 1));

        $cabangPreview = Cabang::query()->find($cabangId);
        if ($cabangPreview === null
            || $cabangPreview->mou_lp_next_sequence === null
            || $cabangPreview->mou_lp_number_suffix === null
            || $cabangPreview->mou_lp_number_suffix === '') {
            return back()->withErrors([
                'mou_settings' => __('Admin LP Ma’arif belum mengatur nomor surat MoU (angka berikutnya dan sufiks). Silakan hubungi LP Ma’arif setempat.'),
            ])->withInput();
        }

        DB::transaction(function () use ($reg, $nomorSekolah, $cabangId, $pdfService): void {
            /** @var Cabang $cabang */
            $cabang = Cabang::query()->whereKey($cabangId)->lockForUpdate()->firstOrFail();

            $seq = (int) $cabang->mou_lp_next_sequence;
            $digits = max(1, min(8, (int) ($cabang->mou_lp_number_digits ?: 4)));
            $nomorLp = str_pad((string) $seq, $digits, '0', STR_PAD_LEFT).$cabang->mou_lp_number_suffix;

            $cabang->forceFill([
                'mou_lp_next_sequence' => $seq + 1,
            ])->save();

            if (is_string($reg->signature_path) && $reg->signature_path !== '') {
                Storage::disk('public')->delete($reg->signature_path);
            }

            if (is_string($reg->materai_path) && $reg->materai_path !== '') {
                Storage::disk('public')->delete($reg->materai_path);
            }

            $reg->forceFill([
                'mou_nomor_lp' => $nomorLp,
                'mou_nomor_sekolah' => $nomorSekolah,
                'signature_path' => null,
                'materai_path' => null,
                'mou_signed_at' => now(),
                'status' => LembagaRegistration::STATUS_PENDING_REVIEW,
            ])->save();

            $reg->refresh();

            $pdfService->generateDraftMouAndSertifikat($reg, $cabang, $nomorLp, $nomorSekolah);
        });

        return redirect()
            ->route('public.lembaga-registrations.status', ['token' => $token])
            ->with('status', __('MoU dan e-sertifikat telah dibuat. Unduh PDF di halaman ini, cetak draft MoU untuk meterai dan tanda tangan basah Anda, lalu verifikasi di kantor LP Ma’arif sesuai petunjuk.'));
    }

    public function status(string $token): View
    {
        $reg = LembagaRegistration::query()->where('public_token', $token)->with('cabang')->firstOrFail();

        return view('public.lembaga-status', [
            'reg' => $reg,
        ]);
    }

    public function regeneratePdfs(string $token, LembagaRegistrationDraftPdfService $pdfService): RedirectResponse
    {
        $reg = LembagaRegistration::query()->where('public_token', $token)->with('cabang')->firstOrFail();

        if (! in_array($reg->status, [
            LembagaRegistration::STATUS_PENDING_REVIEW,
            LembagaRegistration::STATUS_APPROVED,
        ], true)) {
            abort(404);
        }

        if ($reg->cabang === null
            || ! is_string($reg->mou_nomor_lp) || $reg->mou_nomor_lp === ''
            || ! is_string($reg->mou_nomor_sekolah) || $reg->mou_nomor_sekolah === '') {
            return back()->withErrors([
                'pdf' => __('Data nomor MoU belum lengkap; tidak dapat memperbarui PDF.'),
            ]);
        }

        $pdfService->generateDraftMouAndSertifikat(
            $reg,
            $reg->cabang,
            $reg->mou_nomor_lp,
            $reg->mou_nomor_sekolah,
        );

        return back()->with('status', __('Draft MoU dan e-sertifikat telah dibuat ulang. Unduh lagi agar berkas di perangkat Anda ikut terbarui.'));
    }
}
