{{--
    Isi Nota Kesepahaman selaras dokumen referensi «NOTA KESEPAHAMAN MA-MA'ARIF - REVISI 16 MARET».
    Dipakai oleh pdf/lembaga-mou-draft dan public/lembaga-mou.
--}}
@props([
    'reg',
    'cabang' => null,
    'nomorLp' => null,
    'nomorSekolah' => null,
    'nomorLpHtml' => null,
    'nomorSekolahHtml' => null,
    'mouCarbon' => null,
])
@php
    use Illuminate\Support\Carbon;

    /** @var \Illuminate\Support\Carbon $d */
    $d = ($mouCarbon instanceof Carbon ? $mouCarbon->copy() : Carbon::now())->locale('id');
    $cab = $cabang ?? $reg->cabang;
    $wilayahNama = trim((string) ($cab?->nama ?? ''));
    $kopWilayahLp = $wilayahNama !== '' ? mb_strtoupper($wilayahNama, 'UTF-8') : mb_strtoupper(__('wilayah cabang'), 'UTF-8');
    $lpPcnu = $wilayahNama !== '' ? "LP Ma'arif NU PCNU {$wilayahNama}" : "LP Ma'arif NU PCNU ………………";
    $kotaTempat = $reg->kabupaten_kota ?? '………………';
    $kotaKedua = $cab?->mou_surat_kota ?? $reg->kabupaten_kota ?? '………………';
@endphp

<p class="mou-r26-kop-title">NOTA KESEPAHAMAN<br><span class="mou-r26-kop-sub">(MEMORANDUM OF UNDERSTANDING)</span></p>
<p class="mou-r26-center mou-r26-small">ANTARA SEKOLAH/MADRASAH</p>
<p class="mou-r26-center mou-r26-school">{{ mb_strtoupper($reg->nama_lembaga, 'UTF-8') }}</p>
<p class="mou-r26-center mou-r26-small">DAN</p>
<p class="mou-r26-center mou-r26-lp-block">
    PENGURUS CABANG<br>
    LEMBAGA PENDIDIKAN MA'ARIF NU<br>
    {{ $kopWilayahLp }}
</p>

<p class="mou-r26-center mou-r26-nomor" style="margin-top:10px;">
    <strong>NOMOR:</strong>
    @if (! empty($nomorLpHtml))
        {!! $nomorLpHtml !!}
    @else
        <span style="font-family: DejaVu Sans, monospace;">{{ $nomorLp }}</span>
    @endif
</p>
<p class="mou-r26-center mou-r26-nomor">
    <strong>NOMOR:</strong>
    @if (! empty($nomorSekolahHtml))
        {!! $nomorSekolahHtml !!}
    @else
        <span style="font-family: DejaVu Sans, monospace;">{{ $nomorSekolah }}</span>
    @endif
</p>

<p style="margin-top:12px;">
    Pada hari ini <strong>{{ $d->translatedFormat('l') }}</strong>, tanggal <strong>{{ $d->translatedFormat('j') }}</strong>
    bulan <strong>{{ $d->translatedFormat('F') }}</strong> tahun <strong>{{ $d->translatedFormat('Y') }}</strong>,
    bertempat di <strong>{{ $kotaTempat }}</strong>, kami yang bertanda tangan di bawah ini:
</p>

<p><strong>Nama</strong> : {{ $reg->nama_kepala ?? '………………' }}</p>
<p><strong>Jabatan</strong> : Kepala lembaga</p>
<p><strong>Nama Lembaga</strong> : {{ $reg->nama_lembaga }}</p>
<p><strong>Alamat Lembaga</strong> : {{ $reg->alamatLengkap() }}</p>
<p>
    berkedudukan di {{ $reg->alamatLengkap() }}, dalam hal ini bertindak untuk dan atas <strong>{{ $reg->nama_lembaga }}</strong>,
    selanjutnya disebut sebagai <strong>PIHAK KESATU</strong>.
</p>

<p><strong>Nama</strong> : {{ $cab?->mou_penandatangan_nama ?? '………………' }}</p>
<p><strong>Jabatan</strong> : {!! nl2br(e($cab?->mou_penandatangan_jabatan ?? 'Ketua LP Ma\'arif NU PCNU')) !!}</p>
<p>
    Berkedudukan di {{ $kotaKedua }}, dalam hal ini bertindak untuk dan atas nama {{ $lpPcnu }}, selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong>.
</p>

<p>
    <strong>PIHAK KESATU</strong> dan <strong>PIHAK KEDUA</strong> secara bersama-sama disebut <strong>PARA PIHAK</strong>,
    sepakat untuk mengadakan kerja sama dalam bidang pengembangan pendidikan dan kelembagaan dengan ketentuan sebagai berikut:
</p>

<p class="mou-r26-pasal-h"><strong>PASAL 1</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">MAKSUD DAN TUJUAN</p>
<p>
    Maksud Nota Kesepahaman ini adalah untuk menjalin kerja sama sinergis antara <strong>{{ $reg->nama_lembaga }}</strong> dan {{ $lpPcnu }}.
    Tujuannya adalah meningkatkan mutu pendidikan, penguatan karakter aswaja, serta pengembangan potensi siswa dan kompetensi guru di lingkungan
    <strong>{{ $reg->nama_lembaga }}</strong> melalui program-program yang diselenggarakan oleh {{ $lpPcnu }}.
</p>

<p class="mou-r26-pasal-h"><strong>PASAL 2</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">RUANG LINGKUP KERJA SAMA</p>
<p>Ruang lingkup kerja sama ini :</p>
<ol class="mou-r26-ol">
    <li>Pembinaan Kelembagaan dan Sumber Daya Manusia (SDM) meliputi supervisi, pendidikan, pelatihan guru, penguatan manajemen, dan pendampingan akreditasi madrasah/sekolah.</li>
    <li>Kegiatan/Kompetisi/Perlombaan prestasi siswa, guru, dan lembaga yang diselenggarakan oleh LP Ma'arif NU.</li>
    <li>Evaluasi Pembelajaran (Assessment): Pengelolaan sistem Assessment madrasah/sekolah yang dikoordinasikan oleh {{ $lpPcnu }}.</li>
    <li>Pembentukan dan Koordinasi kegiatan Komisariat IPNU/IPPNU di satuan pendidikan.</li>
    <li>Keikutsertaan Satuan Komunitas Pramuka Ma'arif NU (SAKOMA NU), pembinaan gugus depan dan keikutsertaan dalam kegiatan perkemahan atau kepanduan di bawah naungan Satuan Komunitas Pramuka Ma'arif NU (SAKOMA NU).</li>
</ol>

<p class="mou-r26-pasal-h"><strong>PASAL 3</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">HAK DAN KEWAJIBAN</p>
<p><strong>HAK PIHAK KESATU:</strong></p>
<ol class="mou-r26-ol">
    <li>Mendapatkan layanan pembinaan dan akses informasi kegiatan dari Pihak Kedua.</li>
    <li>Mengikutsertakan siswa dan guru dalam agenda resmi Pihak Kedua.</li>
    <li>Mendapatkan sertifikat satuan pendidikan Maarif NU.</li>
</ol>
<p><strong>KEWAJIBAN PIHAK KESATU:</strong></p>
<ol class="mou-r26-ol">
    <li>Mematuhi regulasi dan ketentuan organisasi yang ditetapkan oleh Pihak Kedua.</li>
    <li>Memberikan kontribusi administratif sesuai kesepakatan untuk setiap program yang diikuti.</li>
</ol>
<p><strong>HAK PIHAK KEDUA:</strong></p>
<ol class="mou-r26-ol">
    <li>Menerima laporan partisipasi dan kontribusi administratif dari Pihak Kesatu.</li>
</ol>
<p><strong>KEWAJIBAN PIHAK KEDUA:</strong></p>
<ol class="mou-r26-ol">
    <li>Memberikan bimbingan, materi evaluasi, dan pendampingan teknis kepada Pihak Kesatu.</li>
</ol>

<p class="mou-r26-pasal-h"><strong>PASAL 4</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">JANGKA WAKTU</p>
<p>
    Nota Kesepahaman ini berlaku sampai dengan tidak ada pembaharuan MoU, terhitung sejak ditandatangani antara PARA PIHAK.
</p>

<p class="mou-r26-pasal-h"><strong>PASAL 5</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">SANKSI</p>
<p>
    Apabila PIHAK KESATU tidak berkomitmen dengan isi MoU, {{ $lpPcnu }} berhak memutus MoU melalui ketentuan mekanisme lembaga.
</p>

<p class="mou-r26-pasal-h"><strong>PASAL 6</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">PENYELESAIAN SENGKETA</p>
<ol class="mou-r26-ol">
    <li>Apabila ada perselisihan antara PIHAK SATU dan PIHAK KEDUA diselesaikan dengan cara musyawarah dan mufakat.</li>
    <li>Apabila tidak tercapai kemufakatan maka akan diselesaikan melalui jalur hukum yang berlaku di Indonesia.</li>
</ol>

<p class="mou-r26-pasal-h"><strong>PASAL 7</strong></p>
<p class="mou-r26-pasal-h" style="margin-top:0;">PEMBAHARUAN MoU</p>
<p>
    Apabila dipandang perlu (sesuai dengan kebutuhan regulasi situasi dan kondisi) maka akan dilakukan pembaharuan MoU.
</p>

<p style="margin-top:10px;"><strong>PENUTUP</strong></p>
<p>
    Demikian Nota Kesepahaman ini dibuat rangkap 2 (dua) dan ditandatangani kedua belah pihak pada tanggal yang sama, bermeterai cukup,
    masing-masing mempunyai kekuatan hukum yang sama.
</p>
