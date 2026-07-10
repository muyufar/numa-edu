<?php

namespace App\Http\Controllers;

use App\Imports\GuruImport;
use App\Imports\PegawaiImport;
use App\Models\Guru;
use App\Models\Pegawai;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TenagaKependidikanController extends Controller
{
    public function index(Request $request): View
    {
        $canViewGuru = Gate::allows('viewAny', Guru::class);
        $canViewPegawai = Gate::allows('viewAny', Pegawai::class);

        if (! $canViewGuru && ! $canViewPegawai) {
            abort(403);
        }

        if ($request->boolean('import')) {
            $tab = (string) $request->query('tab', 'guru');
            if ($tab !== 'pegawai') {
                $tab = 'guru';
            }

            if ($tab === 'pegawai') {
                Gate::authorize('create', Pegawai::class);

                return view('tenaga-kependidikan.import', [
                    'tab' => $tab,
                    'total' => Pegawai::query()->count(),
                ]);
            }

            Gate::authorize('create', Guru::class);

            return view('tenaga-kependidikan.import', [
                'tab' => 'guru',
                'total' => Guru::query()->count(),
            ]);
        }

        $tab = (string) $request->query('tab', 'guru');
        if ($tab !== 'pegawai') {
            $tab = 'guru';
        }

        if ($tab === 'guru' && ! $canViewGuru) {
            $tab = 'pegawai';
        }

        if ($tab === 'pegawai' && ! $canViewPegawai) {
            $tab = 'guru';
        }

        $gurus = null;
        $pegawais = null;

        if ($tab === 'guru') {
            $gurus = Guru::query()
                ->with('user:id,name,email')
                ->orderBy('nama')
                ->paginate(10)
                ->withQueryString();
        } else {
            $pegawais = Pegawai::query()
                ->orderByDesc('is_active')
                ->orderBy('nama')
                ->paginate(15)
                ->withQueryString();
        }

        return view('tenaga-kependidikan.index', [
            'tab' => $tab,
            'canViewGuru' => $canViewGuru,
            'canViewPegawai' => $canViewPegawai,
            'gurus' => $gurus,
            'pegawais' => $pegawais,
        ]);
    }

    public function importStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls'],
            'tab' => ['required', 'in:guru,pegawai'],
        ]);

        $tab = $data['tab'];

        try {
            if ($tab === 'pegawai') {
                Gate::authorize('create', Pegawai::class);

                $import = new PegawaiImport();
                $import->importFromFile($data['file']->getRealPath());

                $status = __('Import tenaga kependidikan selesai. Diproses: :processed, dibuat: :created, diperbarui: :updated, dilewati: :skipped.', [
                    'processed' => $import->processed,
                    'created' => $import->created,
                    'updated' => $import->updated,
                    'skipped' => $import->skipped,
                ]);
            } else {
                Gate::authorize('create', Guru::class);

                $import = new GuruImport();
                $import->importFromFile($data['file']->getRealPath());

                $status = __('Import guru selesai. Diproses: :processed, dibuat: :created, diperbarui: :updated, dilewati: :skipped.', [
                    'processed' => $import->processed,
                    'created' => $import->created,
                    'updated' => $import->updated,
                    'skipped' => $import->skipped,
                ]);
            }
        } catch (\Throwable $e) {
            return back()->withErrors([
                'file' => __('Gagal melakukan import: :message', ['message' => $e->getMessage()]),
            ]);
        }

        return redirect()
            ->route('tenaga-kependidikan.index', ['tab' => $tab, 'import' => 1])
            ->with('status', $status);
    }
}
