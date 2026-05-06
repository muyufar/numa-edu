<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\Concerns\RoutesWhatsApp;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, RoutesWhatsApp;

    /**
     * Jenis akun yang boleh dipilih di form registrasi publik (nilai = nama role Spatie).
     *
     * @var list<string>
     */
    public const JENIS_AKUN_REGISTRASI_PUBLIK = ['wali', 'siswa'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'jenis_akun',
        'phone',
        'password',
        'cabang_id',
        'sekolah_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function cabang(): BelongsTo
    {
        return $this->belongsTo(Cabang::class);
    }

    public function sekolah(): BelongsTo
    {
        return $this->belongsTo(Sekolah::class);
    }

    /**
     * Pengurus cabang sedang bertindak atas sekolah (session), bukan user.sekolah_id.
     */
    public function isPengurusSekolahAktif(): bool
    {
        return $this->hasRole('pengurus_cabang') && (bool) session('pengurus_sekolah_id');
    }

    public function waliSiswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'wali_siswa')
            ->withPivot('hubungan')
            ->withTimestamps();
    }

    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class);
    }

    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class);
    }

    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    /**
     * Wali/siswa self-register harus menautkan ke data sekolah sebelum mengakses aplikasi penuh.
     */
    public function needsHubungkanAkunSekolahOnboarding(): bool
    {
        if ($this->hasRole('wali') && ! $this->waliSiswas()->exists()) {
            return true;
        }

        if ($this->hasRole('siswa') && ! $this->siswa()->exists()) {
            return true;
        }

        return false;
    }
}
