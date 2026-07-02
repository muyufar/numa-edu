import Alpine from 'alpinejs';

export function registerWilayahAlamat() {
    document.addEventListener('alpine:init', () => {
        Alpine.data('wilayahAlamat', (initial = {}) => ({
            provinces: [],
            regencies: [],
            districts: [],
            villages: [],
            kodeProv: '',
            namaProv: initial.nama_provinsi ?? '',
            kodeKab: '',
            namaKab: initial.nama_kabupaten ?? '',
            kodeKec: '',
            namaKec: initial.nama_kecamatan ?? '',
            kodeKel: '',
            namaKel: initial.nama_kelurahan ?? '',
            err: '',
            base:
                typeof initial.base === 'string' && initial.base.trim() !== ''
                    ? initial.base.replace(/\/$/, '')
                    : '/ref/wilayah',

            cStr(v) {
                if (v === null || v === undefined) {
                    return '';
                }

                return String(v).trim();
            },

            sameCode(a, b) {
                return this.cStr(a) === this.cStr(b);
            },

            async fetchJson(url) {
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!res.ok) {
                    throw new Error(String(res.status));
                }
                const raw = await res.text();
                // Defensive: strip BOM / stray bytes and parse from first { or [
                const s = String(raw || '').replace(/^\uFEFF/, '');
                const i = s.search(/[\{\[]/);
                const jsonText = i >= 0 ? s.slice(i) : s;
                return JSON.parse(jsonText);
            },

            ensureOptionRow(list, code, name) {
                const c = this.cStr(code);
                if (!c || !Array.isArray(list)) {
                    return;
                }
                if (list.some((p) => this.sameCode(p.code, c))) {
                    return;
                }
                const label = this.cStr(name) || c;
                list.push({ code: c, name: label });
            },

            async init() {
                this.kodeProv = this.cStr(initial.kode_provinsi);
                this.kodeKab = this.cStr(initial.kode_kabupaten);
                this.kodeKec = this.cStr(initial.kode_kecamatan);
                this.kodeKel = this.cStr(initial.kode_kelurahan);

                await this.loadProvinces();
                this.ensureOptionRow(this.provinces, this.kodeProv, this.namaProv);
                await this.$nextTick();

                if (this.kodeProv) {
                    await this.loadRegencies(false);
                    this.ensureOptionRow(this.regencies, this.kodeKab, this.namaKab);
                    await this.$nextTick();
                }
                if (this.kodeKab) {
                    await this.loadDistricts(false);
                    this.ensureOptionRow(this.districts, this.kodeKec, this.namaKec);
                    await this.$nextTick();
                }
                if (this.kodeKec) {
                    await this.loadVillages(false);
                    this.ensureOptionRow(this.villages, this.kodeKel, this.namaKel);
                    await this.$nextTick();
                }
                this.syncAllNames();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            /** Memaksa select native mengikuti nilai x-model setelah opsi dinamis tercipta. */
            refreshSelectBindings() {
                const pairs = [
                    ['wilayah_prov', this.kodeProv],
                    ['wilayah_kab', this.kodeKab],
                    ['wilayah_kec', this.kodeKec],
                    ['wilayah_kel', this.kodeKel],
                ];
                for (const [id, val] of pairs) {
                    const el = document.getElementById(id);
                    if (!el || el.tagName !== 'SELECT') {
                        continue;
                    }
                    const v = this.cStr(val);
                    el.value = v;
                }
            },

            syncAllNames() {
                this.syncNamaProv();
                this.syncNamaKab();
                this.syncNamaKec();
                this.syncNamaKel();
            },

            syncNamaProv() {
                const x = this.provinces.find((p) => this.sameCode(p.code, this.kodeProv));
                if (x) {
                    this.namaProv = x.name;
                } else if (!this.cStr(this.kodeProv)) {
                    this.namaProv = '';
                }
            },
            syncNamaKab() {
                const x = this.regencies.find((p) => this.sameCode(p.code, this.kodeKab));
                if (x) {
                    this.namaKab = x.name;
                } else if (!this.cStr(this.kodeKab)) {
                    this.namaKab = '';
                }
            },
            syncNamaKec() {
                const x = this.districts.find((p) => this.sameCode(p.code, this.kodeKec));
                if (x) {
                    this.namaKec = x.name;
                } else if (!this.cStr(this.kodeKec)) {
                    this.namaKec = '';
                }
            },
            syncNamaKel() {
                const x = this.villages.find((p) => this.sameCode(p.code, this.kodeKel));
                if (x) {
                    this.namaKel = x.name;
                } else if (!this.cStr(this.kodeKel)) {
                    this.namaKel = '';
                }
            },

            async loadProvinces() {
                this.err = '';
                try {
                    const j = await this.fetchJson(`${this.base}/provinces`);
                    this.provinces = j.data ?? [];
                } catch {
                    this.err = 'Wilayah: gagal memuat provinsi.';
                    this.provinces = [];
                }
            },

            async loadRegencies(clearLower) {
                if (clearLower) {
                    this.kodeKab = '';
                    this.namaKab = '';
                    this.kodeKec = '';
                    this.namaKec = '';
                    this.kodeKel = '';
                    this.namaKel = '';
                    this.districts = [];
                    this.villages = [];
                }
                this.regencies = [];
                if (!this.kodeProv) {
                    return;
                }
                try {
                    const j = await this.fetchJson(`${this.base}/regencies/${encodeURIComponent(this.kodeProv)}`);
                    this.regencies = j.data ?? [];
                } catch {
                    this.err = 'Wilayah: gagal memuat kabupaten/kota.';
                }
            },

            async loadDistricts(clearLower) {
                if (clearLower) {
                    this.kodeKec = '';
                    this.namaKec = '';
                    this.kodeKel = '';
                    this.namaKel = '';
                    this.villages = [];
                }
                this.districts = [];
                if (!this.kodeKab) {
                    return;
                }
                try {
                    const j = await this.fetchJson(`${this.base}/districts/${encodeURIComponent(this.kodeKab)}`);
                    this.districts = j.data ?? [];
                } catch {
                    this.err = 'Wilayah: gagal memuat kecamatan.';
                }
            },

            async loadVillages(clearLower) {
                if (clearLower) {
                    this.kodeKel = '';
                    this.namaKel = '';
                }
                this.villages = [];
                if (!this.kodeKec) {
                    return;
                }
                try {
                    const j = await this.fetchJson(`${this.base}/villages/${encodeURIComponent(this.kodeKec)}`);
                    this.villages = j.data ?? [];
                } catch {
                    this.err = 'Wilayah: gagal memuat kelurahan/desa.';
                }
            },

            async onProvChange() {
                this.err = '';
                this.syncNamaProv();
                await this.loadRegencies(true);
                this.syncAllNames();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async onKabChange() {
                this.err = '';
                this.syncNamaKab();
                await this.loadDistricts(true);
                this.syncAllNames();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async onKecChange() {
                this.err = '';
                this.syncNamaKec();
                await this.loadVillages(true);
                this.syncAllNames();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async onKelChange() {
                this.syncNamaKel();
                await this.$nextTick();
                this.refreshSelectBindings();
            },
        }));
    });
}
