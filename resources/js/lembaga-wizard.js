/**
 * Wizard pendaftaran lembaga (multi-step, satu form).
 * Harus memanggil Alpine.data() sebelum Alpine.start() — impor Alpine di sini.
 */
import Alpine from 'alpinejs';

Alpine.data('lembagaWizard', (initialStep = 1, skipFiles = {}) => ({
    skipFiles: skipFiles && typeof skipFiles === 'object' ? skipFiles : {},
    step: Math.min(Math.max(1, Number(initialStep) || 1), 5),
    maxStep: 5,

    init() {
        this.$nextTick(() => {
            window.dispatchEvent(new CustomEvent('lembaga-wizard-step', { detail: { step: this.step } }));
        });
    },

    scrollTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    validateStep(s) {
        if (s === 1) {
            const npsn = document.getElementById('npsn');
            const nama = document.getElementById('nama_lembaga');
            const jenjang = document.getElementById('jenjang');
            const waktu = document.getElementById('waktu_belajar');
            const kkm = document.getElementById('status_kkm');
            const komite = document.getElementById('komite');
            const jumlahMurid = document.getElementById('jumlah_murid');

            if (!npsn?.value?.trim() || !/^\d{8}$/.test(npsn.value.trim())) {
                npsn?.setCustomValidity('NPSN harus 8 digit angka.');
                npsn?.reportValidity();
                npsn?.setCustomValidity('');

                return false;
            }

            if (!nama?.value?.trim()) {
                nama?.setCustomValidity('Nama lembaga wajib diisi.');
                nama?.reportValidity();
                nama?.setCustomValidity('');

                return false;
            }

            if (!jenjang?.value) {
                jenjang?.focus();

                return false;
            }

            if (!waktu?.value) {
                waktu?.focus();

                return false;
            }

            if (!kkm?.value) {
                kkm?.focus();

                return false;
            }

            if (!komite?.value) {
                komite?.focus();

                return false;
            }

            const jm = jumlahMurid?.value?.trim();
            const jmNum = jm !== undefined && jm !== '' ? Number.parseInt(jm, 10) : NaN;
            if (jm === undefined || jm === '' || ! Number.isFinite(jmNum) || jmNum < 0 || jmNum > 999999) {
                jumlahMurid?.setCustomValidity('Isi jumlah murid terkini (bilangan bulat 0–999999).');
                jumlahMurid?.reportValidity();
                jumlahMurid?.setCustomValidity('');

                return false;
            }
        }

        if (s === 2) {
            const kel = document.getElementById('lembaga_wilayah_kel');
            if (! kel?.value) {
                kel?.focus();

                return false;
            }

            const aj = document.getElementById('lembaga_alamat_jalan');
            if (! aj?.value?.trim()) {
                aj?.setCustomValidity('Isi alamat atau pilih titik di peta.');
                aj?.reportValidity();
                aj?.setCustomValidity('');

                return false;
            }
        }

        if (s === 3) {
            const ids = ['foto_papan_nama', 'foto_gedung_depan', 'foto_kelas', 'foto_halaman'];

            for (const id of ids) {
                if (this.skipFiles?.[id]) {
                    continue;
                }

                const el = document.getElementById(id);

                if (!el?.files?.length) {
                    el?.setCustomValidity('Unggah foto untuk langkah ini.');
                    el?.reportValidity();
                    el?.setCustomValidity('');

                    return false;
                }
            }
        }

        if (s === 5) {
            const on = document.getElementById('operator_name');
            const oe = document.getElementById('operator_email');

            if (!on?.value?.trim()) {
                on?.setCustomValidity('Nama operator wajib diisi.');
                on?.reportValidity();
                on?.setCustomValidity('');

                return false;
            }

            if (!oe?.value?.trim()) {
                oe?.setCustomValidity('Email operator wajib diisi.');
                oe?.reportValidity();
                oe?.setCustomValidity('');

                return false;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(oe.value.trim())) {
                oe.setCustomValidity('Format email tidak valid.');
                oe.reportValidity();
                oe.setCustomValidity('');

                return false;
            }
        }

        return true;
    },

    next() {
        if (!this.validateStep(this.step)) {
            return;
        }

        if (this.step < this.maxStep) {
            this.step += 1;
            this.$nextTick(() => {
                this.scrollTop();
                window.dispatchEvent(new CustomEvent('lembaga-wizard-step', { detail: { step: this.step } }));
            });
        }
    },

    prev() {
        if (this.step > 1) {
            this.step -= 1;
            this.$nextTick(() => {
                this.scrollTop();
                window.dispatchEvent(new CustomEvent('lembaga-wizard-step', { detail: { step: this.step } }));
            });
        }
    },

    submitWizard() {
        for (let i = 1; i <= this.maxStep; i += 1) {
            if (!this.validateStep(i)) {
                this.step = i;
                this.$nextTick(() => {
                    this.scrollTop();
                    window.dispatchEvent(new CustomEvent('lembaga-wizard-step', { detail: { step: this.step } }));
                });

                return;
            }
        }

        this.$refs?.form?.submit();
    },

    onFormSubmit() {
        if (this.step < this.maxStep) {
            this.next();
        } else {
            this.submitWizard();
        }
    },
}));
