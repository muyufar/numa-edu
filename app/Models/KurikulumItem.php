<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KurikulumItem extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const SEMESTER_OPTIONS = ['1', '2'];

    protected $fillable = [
        'sekolah_id',
        'mata_pelajaran_id',
        'tingkat',
        'semester',
        'tahun_ajaran',
        'jam_per_minggu',
        'urutan',
        'is_active',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tingkat' => 'integer',
            'jam_per_minggu' => 'integer',
            'urutan' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function resolveSekolahIdOnCreating(): ?int
    {
        if (! $this->mata_pelajaran_id) {
            return null;
        }

        $sid = MataPelajaran::withoutGlobalScopes()->whereKey($this->mata_pelajaran_id)->value('sekolah_id');

        return $sid !== null ? (int) $sid : null;
    }

    /**
     * If curriculum rows exist for tingkat + tahun ajaran, mapel must appear there; otherwise returns null (no error).
     */
    public static function jadwalCurriculumErrorMessage(int $kelasId, int $mataPelajaranId, string $tahunAjaran): ?string
    {
        $kelas = Kelas::query()->find($kelasId);
        if (! $kelas) {
            return null;
        }

        $hasAnyForGrade = static::query()
            ->where('tingkat', $kelas->tingkat)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('is_active', true)
            ->exists();

        if (! $hasAnyForGrade) {
            return null;
        }

        $mapelOk = static::query()
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->where('tingkat', $kelas->tingkat)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('is_active', true)
            ->exists();

        if (! $mapelOk) {
            return __('Mapel ini belum ada di kurikulum untuk tingkat dan tahun ajaran kelas tersebut.');
        }

        return null;
    }
}
