<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            if (! Schema::hasColumn('cabangs', 'mou_lp_next_sequence')) {
                $table->unsignedInteger('mou_lp_next_sequence')->nullable()->after('kode');
            }
            if (! Schema::hasColumn('cabangs', 'mou_lp_number_digits')) {
                $table->unsignedTinyInteger('mou_lp_number_digits')->default(4)->after('mou_lp_next_sequence');
            }
            if (! Schema::hasColumn('cabangs', 'mou_lp_number_suffix')) {
                $table->string('mou_lp_number_suffix', 191)->nullable()->after('mou_lp_number_digits');
            }
            if (! Schema::hasColumn('cabangs', 'mou_penandatangan_nama')) {
                $table->string('mou_penandatangan_nama', 191)->nullable()->after('mou_lp_number_suffix');
            }
            if (! Schema::hasColumn('cabangs', 'mou_penandatangan_jabatan')) {
                $table->text('mou_penandatangan_jabatan')->nullable()->after('mou_penandatangan_nama');
            }
            if (! Schema::hasColumn('cabangs', 'mou_surat_kota')) {
                $table->string('mou_surat_kota', 100)->nullable()->after('mou_penandatangan_jabatan');
            }
            if (! Schema::hasColumn('cabangs', 'mou_stempel_path')) {
                $table->string('mou_stempel_path', 2048)->nullable()->after('mou_surat_kota');
            }
        });

        Schema::table('lembaga_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('lembaga_registrations', 'mou_draft_pdf_path')) {
                $table->string('mou_draft_pdf_path', 2048)->nullable()->after('materai_path');
            }
            if (! Schema::hasColumn('lembaga_registrations', 'e_sertifikat_pdf_path')) {
                $table->string('e_sertifikat_pdf_path', 2048)->nullable()->after('mou_draft_pdf_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lembaga_registrations', function (Blueprint $table) {
            $table->dropColumn(['mou_draft_pdf_path', 'e_sertifikat_pdf_path']);
        });

        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropColumn([
                'mou_lp_next_sequence',
                'mou_lp_number_digits',
                'mou_lp_number_suffix',
                'mou_penandatangan_nama',
                'mou_penandatangan_jabatan',
                'mou_surat_kota',
                'mou_stempel_path',
            ]);
        });
    }
};
