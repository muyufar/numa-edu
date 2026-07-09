<?php

namespace Tests\Feature;

use App\Models\Cabang;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\MateriAjar;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Database\Seeders\RoleSeeder;
use Tests\TestCase;

class MateriAjarPerangkatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    private function seedSekolah(): Sekolah
    {
        $cabang = Cabang::query()->create(['nama' => 'PC', 'kode' => 'P']);

        return Sekolah::query()->create([
            'cabang_id' => $cabang->id,
            'npsn' => '77776666',
            'nama' => 'SMP Perangkat',
            'jenjang' => 'smp',
            'is_active' => true,
        ]);
    }

    private function seedMapelKelasGuru(Sekolah $sekolah): array
    {
        $mapel = MataPelajaran::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'kode' => 'MTK',
            'nama' => 'Matematika',
        ]);

        $kelas = Kelas::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'tingkat' => 8,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $guruUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $guruUser->assignRole('guru');

        $guru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $guruUser->id,
            'nama' => 'Pak Guru',
        ]);

        return compact('mapel', 'kelas', 'guru', 'guruUser');
    }

    public function test_guru_can_upload_perangkat_ajar_as_draft(): void
    {
        Storage::fake('public');

        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $this->actingAs($guruUser)
            ->post(route('materi.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'judul' => 'RPP Persamaan Linear',
                'jenis' => 'rpp',
                'status_penggunaan' => 'rencana',
                'tahun_ajaran' => '2026/2027',
                'semester' => '1',
                'pertemuan_ke' => 3,
                'file' => UploadedFile::fake()->create('rpp.pdf', 120, 'application/pdf'),
            ])
            ->assertRedirect();

        $materi = MateriAjar::query()->first();
        $this->assertNotNull($materi);
        $this->assertSame('draft', $materi->status_publikasi);
        $this->assertSame('rencana', $materi->status_penggunaan);
        $this->assertSame('rpp', $materi->jenis);
        $this->assertSame((int) $guruUser->guru->id, (int) $materi->guru_id);
    }

    public function test_guru_can_publish_perangkat_ajar(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guru' => $guru, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $materi = MateriAjar::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'guru_id' => $guru->id,
            'judul' => 'Modul Ajar',
            'jenis' => 'modul',
            'status_publikasi' => 'draft',
            'status_penggunaan' => 'rencana',
            'file_path' => 'perangkat-ajar/test.pdf',
            'file_name' => 'test.pdf',
            'diunggah_oleh' => $guruUser->id,
        ]);

        $this->actingAs($guruUser)
            ->post(route('materi.publish', $materi))
            ->assertRedirect(route('materi.show', $materi));

        $materi->refresh();
        $this->assertSame('dipublikasi', $materi->status_publikasi);
        $this->assertNotNull($materi->dipublikasi_pada);
    }

    public function test_siswa_only_sees_published_perangkat_for_their_class(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas] = $this->seedMapelKelasGuru($sekolah);

        $siswaUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $siswaUser->id,
            'kelas_id' => $kelas->id,
            'nis' => 'SIS-PA-01',
            'nama' => 'Ani Siswa',
        ]);

        MateriAjar::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'judul' => 'LKPD Dipublikasi',
            'jenis' => 'lkpd',
            'status_publikasi' => 'dipublikasi',
            'status_penggunaan' => 'aktif',
            'file_path' => 'perangkat-ajar/a.pdf',
            'file_name' => 'a.pdf',
        ]);

        MateriAjar::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'judul' => 'RPP Draft',
            'jenis' => 'rpp',
            'status_publikasi' => 'draft',
            'status_penggunaan' => 'rencana',
            'file_path' => 'perangkat-ajar/b.pdf',
            'file_name' => 'b.pdf',
        ]);

        $this->actingAs($siswaUser)
            ->get(route('materi.index'))
            ->assertOk()
            ->assertSee('LKPD Dipublikasi')
            ->assertDontSee('RPP Draft');
    }

    public function test_admin_can_archive_perangkat_ajar(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel] = $this->seedMapelKelasGuru($sekolah);

        $admin = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $admin->assignRole('admin');

        $materi = MateriAjar::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'mata_pelajaran_id' => $mapel->id,
            'judul' => 'Silabus Lama',
            'jenis' => 'silabus',
            'status_publikasi' => 'dipublikasi',
            'status_penggunaan' => 'aktif',
            'tahun_ajaran' => '2024/2025',
            'file_path' => 'perangkat-ajar/c.pdf',
            'file_name' => 'c.pdf',
        ]);

        $this->actingAs($admin)
            ->post(route('materi.archive', $materi))
            ->assertRedirect(route('materi.index', ['tab' => 'arsip']));

        $materi->refresh();
        $this->assertSame('diarsipkan', $materi->status_publikasi);
        $this->assertSame('selesai', $materi->status_penggunaan);
        $this->assertNotNull($materi->diarsipkan_pada);
    }

    public function test_guru_cannot_edit_other_guru_perangkat(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $otherUser = User::factory()->create(['sekolah_id' => $sekolah->id]);
        $otherUser->assignRole('guru');

        $otherGuru = Guru::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'user_id' => $otherUser->id,
            'nama' => 'Guru Lain',
        ]);

        $materi = MateriAjar::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $otherGuru->id,
            'judul' => 'Milik Guru Lain',
            'jenis' => 'rpp',
            'status_publikasi' => 'draft',
            'status_penggunaan' => 'rencana',
            'file_path' => 'perangkat-ajar/d.pdf',
            'file_name' => 'd.pdf',
        ]);

        $this->actingAs($guruUser)
            ->get(route('materi.edit', $materi))
            ->assertForbidden();
    }

    public function test_guru_can_create_modul_merdeka_without_pdf(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $this->actingAs($guruUser)
            ->post(route('materi.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'judul' => 'Mengenal Bagian Tubuh dan Fungsinya',
                'jenis' => 'modul',
                'fase' => 'A',
                'elemen_topik' => 'Materi IPAS Fase A: Mengenal Bagian Tubuh dan Fungsinya',
                'alokasi_waktu' => '2 JP (2 x 45 menit)',
                'model_pembelajaran' => 'Discovery Learning',
                'status_penggunaan' => 'rencana',
                'konten_modul' => [
                    'capaian_pembelajaran' => 'Peserta didik dapat mengidentifikasi bagian tubuh.',
                    'tujuan_pembelajaran' => 'Melalui kegiatan mengamati, peserta didik dapat mengidentifikasi bagian tubuh.',
                ],
            ])
            ->assertRedirect();

        $materi = MateriAjar::query()->first();
        $this->assertSame('modul', $materi->jenis);
        $this->assertSame('A', $materi->fase);
        $this->assertSame('Discovery Learning', $materi->model_pembelajaran);
        $this->assertNotNull($materi->konten_modul);
    }

    public function test_guru_can_create_rpp_without_pdf(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $this->actingAs($guruUser)
            ->post(route('materi.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'judul' => 'RPP Mengenal Bagian Tubuh',
                'jenis' => 'rpp',
                'alokasi_waktu' => '2 JP',
                'model_pembelajaran' => 'Discovery Learning',
                'status_penggunaan' => 'rencana',
                'pertemuan_ke' => 1,
                'konten_modul' => [
                    'kegiatan_pendahuluan' => 'Apersepsi dan motivasi.',
                    'kegiatan_inti' => 'Guru memfasilitasi diskusi kelompok.',
                    'rencana_asesmen' => 'Observasi dan lembar penilaian.',
                ],
            ])
            ->assertRedirect();

        $materi = MateriAjar::query()->where('jenis', 'rpp')->first();
        $this->assertNotNull($materi);
        $this->assertSame('RPP Mengenal Bagian Tubuh', $materi->judul);
        $this->assertArrayHasKey('kegiatan_inti', $materi->konten_modul);
    }

    public function test_guru_can_create_modul_pembelajaran_without_pdf(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $this->actingAs($guruUser)
            ->post(route('materi.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'judul' => 'Modul Fisika — Gerak Lurus',
                'jenis' => 'modul_pembelajaran',
                'elemen_topik' => 'Bab 1 — Gerak',
                'status_penggunaan' => 'rencana',
                'konten_modul' => [
                    'materi_pembelajaran' => 'Gerak adalah perubahan posisi benda.',
                    'latihan_soal' => "1. Apa yang dimaksud gerak?\n2. Sebutkan jenis gerak!",
                    'kunci_jawaban' => '1. Perubahan posisi\n2. Gerak lurus, melingkar',
                ],
            ])
            ->assertRedirect();

        $materi = MateriAjar::query()->where('jenis', 'modul_pembelajaran')->first();
        $this->assertNotNull($materi);
        $this->assertSame('Modul Fisika — Gerak Lurus', $materi->judul);
        $this->assertArrayHasKey('latihan_soal', $materi->konten_modul);
    }

    public function test_guru_can_create_lkpd_alternatif_1_without_pdf(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $this->actingAs($guruUser)
            ->post(route('materi.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'judul' => 'LKPD Bagian Tubuh',
                'jenis' => 'lkpd',
                'lkpd_sistematika' => 'alternatif_1',
                'elemen_topik' => 'Mengenal Bagian Tubuh dan Fungsinya',
                'alokasi_waktu' => '2 x 45 menit',
                'semester' => '1',
                'status_penggunaan' => 'rencana',
                'konten_modul' => [
                    'indikator_kompetensi' => 'Mengidentifikasi bagian tubuh.',
                    'petunjuk_belajar' => 'Bacalah informasi pendukung.',
                    'langkah_kerja' => '1. Amati gambar\n2. Isi tabel',
                    'soal_soal' => '1. Sebutkan 5 bagian tubuh!',
                ],
            ])
            ->assertRedirect();

        $materi = MateriAjar::query()->where('jenis', 'lkpd')->first();
        $this->assertNotNull($materi);
        $this->assertSame('alternatif_1', $materi->lkpdSistematika());
        $this->assertArrayHasKey('soal_soal', $materi->konten_modul);
    }

    public function test_guru_can_create_lkpd_alternatif_2_without_pdf(): void
    {
        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'kelas' => $kelas, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $this->actingAs($guruUser)
            ->post(route('materi.store'), [
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'judul' => 'LKPD Eksperimen Sederhana',
                'jenis' => 'lkpd',
                'lkpd_sistematika' => 'alternatif_2',
                'elemen_topik' => 'Pengamatan tumbuhan',
                'status_penggunaan' => 'rencana',
                'konten_modul' => [
                    'alat_bahan' => 'Kaca pembesar, daun',
                    'tugas_dilakukan' => 'Amati struktur daun.',
                    'hasil_penyelesaian' => 'Laporan pengamatan 1 halaman.',
                ],
            ])
            ->assertRedirect();

        $materi = MateriAjar::query()->where('judul', 'LKPD Eksperimen Sederhana')->first();
        $this->assertSame('alternatif_2', $materi->lkpdSistematika());
        $this->assertArrayHasKey('alat_bahan', $materi->konten_modul);
    }

    public function test_authorized_user_can_preview_pdf_inline(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('perangkat-ajar/rpp.pdf', '%PDF-1.4 fake');

        $sekolah = $this->seedSekolah();
        ['mapel' => $mapel, 'guru' => $guru, 'guruUser' => $guruUser] = $this->seedMapelKelasGuru($sekolah);

        $materi = MateriAjar::withoutGlobalScopes()->create([
            'sekolah_id' => $sekolah->id,
            'mata_pelajaran_id' => $mapel->id,
            'guru_id' => $guru->id,
            'judul' => 'RPP PDF',
            'jenis' => 'rpp',
            'status_publikasi' => 'dipublikasi',
            'status_penggunaan' => 'aktif',
            'file_path' => 'perangkat-ajar/rpp.pdf',
            'file_name' => 'rpp.pdf',
            'mime' => 'application/pdf',
        ]);

        $this->actingAs($guruUser)
            ->get(route('materi.preview', $materi))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($guruUser)
            ->get(route('materi.show', $materi))
            ->assertOk()
            ->assertSee(__('Baca PDF'), false);
    }
}
