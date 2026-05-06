<?php

namespace App\Http\Requests;

use App\Models\Jadwal;
use App\Models\KurikulumItem;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJadwalRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Jadwal $jadwal */
        $jadwal = $this->route('jadwal');

        return $this->user()?->can('update', $jadwal) ?? false;
    }

    protected function prepareForValidation(): void
    {
        foreach (['jam_mulai', 'jam_selesai'] as $field) {
            $v = $this->input($field);
            if (is_string($v) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $v)) {
                $this->merge([$field => substr($v, 0, 5)]);
            }
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'integer', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'integer', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'integer', 'exists:gurus,id'],
            'hari' => ['required', 'string', 'max:16', Rule::in(Jadwal::HARI_OPTIONS)],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i'],
            'tahun_ajaran' => ['required', 'string', 'max:16'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $mulai = $this->input('jam_mulai');
            $selesai = $this->input('jam_selesai');
            if (! is_string($mulai) || ! is_string($selesai)) {
                return;
            }
            if ($mulai >= $selesai) {
                $validator->errors()->add(
                    'jam_selesai',
                    __('Jam selesai harus setelah jam mulai.')
                );
            }

            $kelasId = (int) $this->input('kelas_id', 0);
            $mapelId = (int) $this->input('mata_pelajaran_id', 0);
            $ta = (string) $this->input('tahun_ajaran', '');
            if ($kelasId > 0 && $mapelId > 0 && $ta !== '') {
                $msg = KurikulumItem::jadwalCurriculumErrorMessage($kelasId, $mapelId, $ta);
                if ($msg !== null) {
                    $validator->errors()->add('mata_pelajaran_id', $msg);
                }
            }
        });
    }
}
