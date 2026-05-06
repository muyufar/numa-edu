<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PpdbRegistration extends Model
{
    use BelongsToSekolah;

    /** @var list<string> */
    public const STATUS_OPTIONS = [
        'submitted',
        'verified',
        'accepted',
        'rejected',
    ];

    protected $fillable = [
        'sekolah_id',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'asal_sekolah',
        'no_hp_ortu',
        'email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
        ];
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'ppdb_registration_id');
    }
}
