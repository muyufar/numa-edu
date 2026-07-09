<?php

namespace App\Http\Requests\Concerns;

use App\Models\MateriAjar;
use App\Support\ModulAjarMerdeka;
use App\Support\PerangkatAjarJenis;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait ValidatesMateriAjarRequest
{
    protected function prepareMateriAjarFields(): void
    {
        if ($this->input('kelas_id') === '') {
            $this->merge(['kelas_id' => null]);
        }

        if ($this->input('guru_id') === '') {
            $this->merge(['guru_id' => null]);
        }

        if ($this->input('pertemuan_ke') === '') {
            $this->merge(['pertemuan_ke' => null]);
        }

        if ($this->input('fase') === '') {
            $this->merge(['fase' => null]);
        }

        if ($this->input('model_pembelajaran') === '') {
            $this->merge(['model_pembelajaran' => null]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function materiAjarRules(bool $fileRequired = true): array
    {
        $kontenRules = [];
        foreach (PerangkatAjarJenis::STRUKTUR_DIGITAL as $jenis) {
            foreach (array_keys(PerangkatAjarJenis::kontenFields($jenis)) as $key) {
                $kontenRules["konten_modul.{$key}"] = ['nullable', 'string', 'max:20000'];
            }
        }

        return array_merge([
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'kelas_id' => ['nullable', 'integer', 'exists:kelas,id'],
            'guru_id' => ['nullable', 'integer', 'exists:gurus,id'],

            'judul' => ['required', 'string', 'max:160'],
            'jenis' => ['required', 'string', Rule::in(MateriAjar::JENIS_OPTIONS)],
            'fase' => ['nullable', 'string', Rule::in(ModulAjarMerdeka::FASE_OPTIONS)],
            'elemen_topik' => ['nullable', 'string', 'max:200'],
            'alokasi_waktu' => ['nullable', 'string', 'max:64'],
            'model_pembelajaran' => ['nullable', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string', 'max:4000'],
            'status_penggunaan' => ['required', 'string', Rule::in(MateriAjar::STATUS_PENGGUNAAN_OPTIONS)],
            'pertemuan_ke' => ['nullable', 'integer', 'min:1', 'max:200'],
            'semester' => ['nullable', Rule::in(['1', '2'])],
            'tahun_ajaran' => ['nullable', 'string', 'max:16'],
            'tanggal' => ['nullable', 'date'],
            'lkpd_sistematika' => ['nullable', 'string', Rule::in(array_keys(\App\Support\LkpdSistematika::ALTERNATIF_OPTIONS))],

            'file' => [
                $fileRequired ? 'required' : 'nullable',
                'file',
                'max:15360',
            ],
        ], $kontenRules);
    }

    protected function validateModulAjarContent(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $jenis = (string) $this->input('jenis');
            if (! PerangkatAjarJenis::supportsKontenDigital($jenis)) {
                return;
            }

            $konten = PerangkatAjarJenis::normalizeKonten($jenis, $this->input('konten_modul', []));
            $hasKonten = PerangkatAjarJenis::hasIsi($jenis, $konten);
            $hasFile = $this->hasFile('file');

            /** @var MateriAjar|null $existing */
            $existing = $this->route('materi_ajar');
            $hasExistingFile = $existing?->file_path;

            if (! $hasKonten && ! $hasFile && ! $hasExistingFile) {
                $label = (new MateriAjar(['jenis' => $jenis]))->labelJenis();
                $validator->errors()->add(
                    'file',
                    __('Untuk :jenis, unggah berkas atau isi minimal satu bagian konten.', ['jenis' => $label])
                );
            }
        });
    }
}
