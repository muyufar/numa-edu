<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use App\Support\WhatsApp\ManualWhatsAppLink;
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

    public function sekolah(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            'submitted' => __('Sedang mendaftar'),
            'verified' => __('Diproses'),
            'accepted' => __('Diterima'),
            'rejected' => __('Ditolak'),
            default => $status,
        };
    }

    public static function statusWhatsAppLabel(string $status): string
    {
        return self::statusLabel($status);
    }

    public static function statusWhatsAppDetail(string $status): string
    {
        return match ($status) {
            'submitted' => __('Pendaftaran PPDB telah kami terima dan sedang menunggu peninjauan.'),
            'verified' => __('Berkas sedang diproses/diverifikasi oleh tim sekolah.'),
            'accepted' => __('Selamat! Pendaftaran diterima. Silakan menunggu instruksi selanjutnya dari sekolah.'),
            'rejected' => __('Mohon maaf, pendaftaran belum dapat diterima. Hubungi sekolah untuk informasi lebih lanjut.'),
            default => '',
        };
    }

    public function whatsappMessage(): string
    {
        $this->loadMissing('sekolah:id,nama');

        $sekolah = $this->sekolah?->nama ?? config('app.name');

        return __('[NumaEdu] PPDB :sekolah'.PHP_EOL.
            'Calon siswa: :nama'.PHP_EOL.
            'Status: :status'.PHP_EOL.
            ':detail', [
            'sekolah' => $sekolah,
            'nama' => $this->nama,
            'status' => self::statusWhatsAppLabel((string) $this->status),
            'detail' => self::statusWhatsAppDetail((string) $this->status),
        ]);
    }

    public function whatsappUrl(): ?string
    {
        return ManualWhatsAppLink::url($this->no_hp_ortu, $this->whatsappMessage());
    }
}
