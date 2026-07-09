<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use App\Models\Tugas;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TugasTest extends TestCase
{
    use RefreshDatabase;

    public function test_guru_can_open_create_tugas_form(): void
    {
        $this->seed(RoleSeeder::class);

        $guruUser = User::factory()->create(['sekolah_id' => 1]);
        $guruUser->assignRole('guru');

        $this->actingAs($guruUser)
            ->get(route('tugas.create'))
            ->assertOk();
    }

    public function test_guru_can_create_pilihan_ganda_tugas(): void
    {
        $this->seed(RoleSeeder::class);

        $guruUser = User::factory()->create(['sekolah_id' => 1]);
        $guruUser->assignRole('guru');

        $mapel = MataPelajaran::query()->create(['nama' => 'PKN', 'kode' => 'PKN']);

        $this->actingAs($guruUser)
            ->post(route('tugas.store'), [
                'judul' => 'Quiz PKn',
                'mata_pelajaran_id' => $mapel->id,
                'jenis_soal' => 'pilihan_ganda',
                'tipe' => 'individu',
                'is_published' => '1',
                'soal' => [
                    [
                        'pertanyaan' => 'Ibukota Indonesia?',
                        'jawaban_benar' => 1,
                        'pilihan' => [
                            ['teks' => 'Bandung'],
                            ['teks' => 'Jakarta'],
                            ['teks' => 'Surabaya'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect();

        $tugas = Tugas::withoutGlobalScopes()->first();
        $this->assertSame('pilihan_ganda', $tugas->jenis_soal);
        $this->assertCount(1, $tugas->soals);
        $this->assertCount(3, $tugas->soals->first()->pilihans);
        $this->assertTrue($tugas->soals->first()->pilihans->firstWhere('label', 'B')?->is_benar);
    }

    public function test_guru_can_create_tugas(): void
    {
        $this->seed(RoleSeeder::class);

        $guruUser = User::factory()->create(['sekolah_id' => 1]);
        $guruUser->assignRole('guru');

        $kelas = Kelas::query()->create([
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::query()->create(['nama' => 'IPA', 'kode' => 'IPA']);

        $this->actingAs($guruUser)
            ->post(route('tugas.store'), [
                'judul' => 'LKPD Bab 1',
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'jenis_soal' => 'esai',
                'hari' => 'Senin',
                'jam' => '08:00',
                'tanggal_batas' => now()->addDays(3)->toDateString(),
                'jam_batas' => '23:59',
                'tipe' => 'individu',
                'bobot' => 20,
                'bahan_materi' => "1. Jelaskan fotosintesis.\n2. Buat diagram.",
                'instruksi' => 'Kumpulkan lewat guru masing-masing.',
                'is_published' => '1',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tugas', [
            'judul' => 'LKPD Bab 1',
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'hari' => 'Senin',
            'tipe' => 'individu',
            'bobot' => 20,
            'is_published' => true,
        ]);
    }

    public function test_siswa_sees_published_tugas_for_their_kelas(): void
    {
        $this->seed(RoleSeeder::class);

        $kelas = Kelas::query()->create([
            'tingkat' => 8,
            'nama' => 'B',
            'tahun_ajaran' => '2025/2026',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::query()->create(['nama' => 'Matematika', 'kode' => 'MTK']);

        Tugas::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => $kelas->id,
            'judul' => 'Tugas Kelas B',
            'hari' => 'Rabu',
            'jam' => '10:00',
            'tipe' => 'individu',
            'is_published' => true,
        ]);

        Tugas::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => null,
            'judul' => 'Tugas Semua Kelas',
            'tipe' => 'latihan',
            'is_published' => true,
        ]);

        Tugas::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'mata_pelajaran_id' => $mapel->id,
            'kelas_id' => Kelas::query()->create([
                'tingkat' => 9,
                'nama' => 'C',
                'tahun_ajaran' => '2025/2026',
                'is_active' => true,
            ])->id,
            'judul' => 'Tugas Kelas Lain',
            'tipe' => 'individu',
            'is_published' => true,
        ]);

        $siswaUser = User::factory()->create(['sekolah_id' => 1]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => $siswaUser->id,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-'.uniqid(),
            'nama' => 'Siswa Test',
        ]);

        $response = $this->actingAs($siswaUser)->get(route('tugas.index'));

        $response->assertOk();
        $response->assertSee('Tugas Kelas B');
        $response->assertSee('Tugas Semua Kelas');
        $response->assertDontSee('Tugas Kelas Lain');
    }

    public function test_siswa_can_submit_pilihan_ganda_tugas(): void
    {
        $this->seed(RoleSeeder::class);

        $kelas = Kelas::query()->create([
            'tingkat' => 7,
            'nama' => 'A',
            'tahun_ajaran' => '2026/2027',
            'is_active' => true,
        ]);

        $mapel = MataPelajaran::query()->create(['nama' => 'Matematika', 'kode' => 'MTK']);

        $guruUser = User::factory()->create(['sekolah_id' => 1]);
        $guruUser->assignRole('guru');

        $this->actingAs($guruUser)
            ->post(route('tugas.store'), [
                'judul' => 'TRIGONOMETRI BAB2',
                'mata_pelajaran_id' => $mapel->id,
                'kelas_id' => $kelas->id,
                'jenis_soal' => 'pilihan_ganda',
                'tipe' => 'individu',
                'bobot' => 80,
                'tanggal_batas' => now()->addDays(7)->toDateString(),
                'jam_batas' => '10:00',
                'is_published' => '1',
                'soal' => [
                    [
                        'pertanyaan' => 'Sin 30 = ?',
                        'jawaban_benar' => 1,
                        'pilihan' => [
                            ['teks' => '0'],
                            ['teks' => '1/2'],
                            ['teks' => '1'],
                        ],
                    ],
                ],
            ])
            ->assertRedirect();

        $tugas = Tugas::withoutGlobalScopes()->first();
        $soal = $tugas->soals->first();
        $benar = $soal->pilihans->firstWhere('is_benar', true);

        $siswaUser = User::factory()->create(['sekolah_id' => 1]);
        $siswaUser->assignRole('siswa');

        Siswa::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'user_id' => $siswaUser->id,
            'kelas_id' => $kelas->id,
            'nis' => 'NIS-KERJAKAN-1',
            'nama' => 'Astari Nugraheni',
        ]);

        $this->actingAs($siswaUser)
            ->get(route('tugas.index'))
            ->assertOk()
            ->assertSee('Kerjakan tugas');

        $this->actingAs($siswaUser)
            ->get(route('tugas.kerjakan', $tugas))
            ->assertOk()
            ->assertSee('TRIGONOMETRI BAB2');

        $this->actingAs($siswaUser)
            ->post(route('tugas.kerjakan.store', $tugas), [
                'jawaban' => [
                    (string) $soal->id => (string) $benar->id,
                ],
            ])
            ->assertRedirect(route('tugas.show', $tugas))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('tugas_pengumpulans', [
            'tugas_id' => $tugas->id,
            'siswa_id' => $siswaUser->siswa->id,
            'nilai_otomatis' => 80,
        ]);
    }

    public function test_tugas_is_overdue_after_deadline(): void
    {
        $mapel = MataPelajaran::query()->create(['nama' => 'Bindo', 'kode' => 'BIN']);

        $tugas = Tugas::withoutGlobalScopes()->create([
            'sekolah_id' => 1,
            'mata_pelajaran_id' => $mapel->id,
            'judul' => 'Essay',
            'tipe' => 'individu',
            'tanggal_batas' => now()->subDay()->toDateString(),
            'jam_batas' => '12:00',
            'is_published' => true,
        ]);

        $this->assertTrue($tugas->isOverdue());
    }
}
