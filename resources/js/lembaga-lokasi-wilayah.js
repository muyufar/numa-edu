/**
 * Langkah lokasi pendaftaran lembaga: wilayah Indonesia (API ref/wilayah) + peta OSM (Leaflet)
 * + pencarian & reverse geocode via proxy Nominatim (OpenStreetMap).
 */
import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

function normStr(v) {
    return String(v ?? '')
        .toLowerCase()
        .trim()
        .replace(/\s+/g, ' ');
}

export function registerLembagaLokasiWilayah() {
    document.addEventListener('alpine:init', () => {
        Alpine.data('lembagaLokasiWilayah', (initial = {}) => ({
            provinces: [],
            regencies: [],
            districts: [],
            villages: [],
            kodeProv: '',
            namaProv: '',
            kodeKab: '',
            namaKab: '',
            kodeKec: '',
            namaKec: '',
            kodeKel: '',
            namaKel: '',
            wilayahErr: '',
            alamatJalan: String(initial.alamat_jalan ?? ''),
            rt: String(initial.rt ?? ''),
            rw: String(initial.rw ?? ''),
            kodepos: String(initial.kodepos ?? ''),
            mapErr: '',
            manualLat: '',
            manualLng: '',
            coordErr: '',
            searchQuery: '',
            searchErr: '',
            searchLoading: false,
            searchResults: [],
            mapBooted: false,
            map: null,
            marker: null,
            nomBase:
                typeof initial.nominatimBase === 'string' && initial.nominatimBase.trim() !== ''
                    ? initial.nominatimBase.replace(/\/$/, '')
                    : '/ref/nominatim',
            base:
                typeof initial.base === 'string' && initial.base.trim() !== ''
                    ? initial.base.replace(/\/$/, '')
                    : '/ref/wilayah',
            initialNames: {
                provinsi: String(initial.provinsi ?? ''),
                kabupaten_kota: String(initial.kabupaten_kota ?? ''),
                kecamatan: String(initial.kecamatan ?? ''),
                desa_kelurahan: String(initial.desa_kelurahan ?? ''),
            },

            cStr(v) {
                if (v === null || v === undefined) {
                    return '';
                }

                return String(v).trim();
            },

            sameCode(a, b) {
                return this.cStr(a) === this.cStr(b);
            },

            parseCoord(raw) {
                const t = String(raw ?? '')
                    .trim()
                    .replace(/\s+/g, '')
                    .replace(',', '.');

                return Number.parseFloat(t);
            },

            syncCoordInputs(lat, lng) {
                if (! Number.isFinite(lat) || ! Number.isFinite(lng)) {
                    return;
                }
                this.manualLat = String(Number(lat.toFixed(7)));
                this.manualLng = String(Number(lng.toFixed(7)));
            },

            async fetchJson(url) {
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                if (! res.ok) {
                    throw new Error(String(res.status));
                }
                const raw = await res.text();
                const s = String(raw || '').replace(/^\uFEFF/, '');
                const i = s.search(/[\[{]/);
                const jsonText = i >= 0 ? s.slice(i) : s;

                return JSON.parse(jsonText);
            },

            pickByName(list, name) {
                const n = normStr(name);
                if (! n || ! Array.isArray(list) || list.length === 0) {
                    return null;
                }
                let x = list.find((p) => normStr(p.name) === n);
                if (x) {
                    return x;
                }
                x = list.find((p) => normStr(p.name).includes(n) || n.includes(normStr(p.name)));

                return x || null;
            },

            refreshSelectBindings() {
                const pairs = [
                    ['lembaga_wilayah_prov', this.kodeProv],
                    ['lembaga_wilayah_kab', this.kodeKab],
                    ['lembaga_wilayah_kec', this.kodeKec],
                    ['lembaga_wilayah_kel', this.kodeKel],
                ];
                for (const [id, val] of pairs) {
                    const el = document.getElementById(id);
                    if (! el || el.tagName !== 'SELECT') {
                        continue;
                    }
                    el.value = this.cStr(val);
                }
            },

            syncNamaProv() {
                const x = this.provinces.find((p) => this.sameCode(p.code, this.kodeProv));
                this.namaProv = x ? x.name : '';
            },
            syncNamaKab() {
                const x = this.regencies.find((p) => this.sameCode(p.code, this.kodeKab));
                this.namaKab = x ? x.name : '';
            },
            syncNamaKec() {
                const x = this.districts.find((p) => this.sameCode(p.code, this.kodeKec));
                this.namaKec = x ? x.name : '';
            },
            syncNamaKel() {
                const x = this.villages.find((p) => this.sameCode(p.code, this.kodeKel));
                this.namaKel = x ? x.name : '';
            },

            async loadProvinces() {
                this.wilayahErr = '';
                try {
                    const j = await this.fetchJson(`${this.base}/provinces`);
                    this.provinces = j.data ?? [];
                } catch {
                    this.wilayahErr = 'Wilayah: gagal memuat provinsi.';
                    this.provinces = [];
                }
            },

            async loadRegencies(clearLower) {
                if (clearLower) {
                    this.kodeKab = '';
                    this.kodeKec = '';
                    this.kodeKel = '';
                    this.districts = [];
                    this.villages = [];
                }
                this.regencies = [];
                if (! this.kodeProv) {
                    return;
                }
                try {
                    const j = await this.fetchJson(`${this.base}/regencies/${encodeURIComponent(this.kodeProv)}`);
                    this.regencies = j.data ?? [];
                } catch {
                    this.wilayahErr = 'Wilayah: gagal memuat kabupaten/kota.';
                }
            },

            async loadDistricts(clearLower) {
                if (clearLower) {
                    this.kodeKec = '';
                    this.kodeKel = '';
                    this.villages = [];
                }
                this.districts = [];
                if (! this.kodeKab) {
                    return;
                }
                try {
                    const j = await this.fetchJson(`${this.base}/districts/${encodeURIComponent(this.kodeKab)}`);
                    this.districts = j.data ?? [];
                } catch {
                    this.wilayahErr = 'Wilayah: gagal memuat kecamatan.';
                }
            },

            async loadVillages(clearLower) {
                if (clearLower) {
                    this.kodeKel = '';
                }
                this.villages = [];
                if (! this.kodeKec) {
                    return;
                }
                try {
                    const j = await this.fetchJson(`${this.base}/villages/${encodeURIComponent(this.kodeKec)}`);
                    this.villages = j.data ?? [];
                } catch {
                    this.wilayahErr = 'Wilayah: gagal memuat kelurahan/desa.';
                }
            },

            async onProvChange() {
                this.wilayahErr = '';
                this.syncNamaProv();
                await this.loadRegencies(true);
                this.syncNamaKab();
                this.syncNamaKec();
                this.syncNamaKel();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async onKabChange() {
                this.wilayahErr = '';
                this.syncNamaKab();
                await this.loadDistricts(true);
                this.syncNamaKec();
                this.syncNamaKel();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async onKecChange() {
                this.wilayahErr = '';
                this.syncNamaKec();
                await this.loadVillages(true);
                this.syncNamaKel();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async onKelChange() {
                this.syncNamaKel();
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async syncCodesFromNames() {
                const n = this.initialNames;
                const p = this.pickByName(this.provinces, n.provinsi);
                if (p) {
                    this.kodeProv = p.code;
                    this.syncNamaProv();
                    await this.loadRegencies(false);
                    const kab = this.pickByName(this.regencies, n.kabupaten_kota);
                    if (kab) {
                        this.kodeKab = kab.code;
                        this.syncNamaKab();
                        await this.loadDistricts(false);
                        const kec = this.pickByName(this.districts, n.kecamatan);
                        if (kec) {
                            this.kodeKec = kec.code;
                            this.syncNamaKec();
                            await this.loadVillages(false);
                            const kel = this.pickByName(this.villages, n.desa_kelurahan);
                            if (kel) {
                                this.kodeKel = kel.code;
                                this.syncNamaKel();
                            }
                        }
                    }
                }
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            onWizardStep(e) {
                const step = e?.detail?.step;
                if (step === 2) {
                    this.$nextTick(() => {
                        this.ensureMap();
                    });
                }
            },

            pinIcon() {
                return L.divIcon({
                    className: 'lembaga-osm-pin',
                    html: '<div style="width:14px;height:14px;background:#0d4a2c;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,.35)"></div>',
                    iconSize: [18, 18],
                    iconAnchor: [9, 9],
                });
            },

            ensureMap() {
                if (this.mapBooted && this.map) {
                    this.map.invalidateSize();

                    return;
                }
                const el = document.getElementById('lembaga-osm-map');
                if (! el || el.offsetParent === null) {
                    return;
                }

                const centerLat = -2.5;
                const centerLng = 118;
                this.map = L.map(el, { scrollWheelZoom: true }).setView([centerLat, centerLng], 5);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                }).addTo(this.map);

                const pin = this.pinIcon();

                this.map.on('click', async (ev) => {
                    const { lat, lng } = ev.latlng;
                    if (! this.marker) {
                        this.marker = L.marker([lat, lng], { icon: pin }).addTo(this.map);
                    } else {
                        this.marker.setLatLng([lat, lng]);
                    }
                    this.syncCoordInputs(lat, lng);
                    await this.reverseAt(lat, lng);
                });

                this.mapBooted = true;
                setTimeout(() => this.map?.invalidateSize(), 200);
            },

            placeMarker(lat, lng) {
                if (! this.map) {
                    return;
                }
                const pin = this.pinIcon();
                if (! this.marker) {
                    this.marker = L.marker([lat, lng], { icon: pin }).addTo(this.map);
                } else {
                    this.marker.setLatLng([lat, lng]);
                }
            },

            async reverseAt(lat, lng) {
                this.mapErr = '';
                try {
                    const url = `${this.nomBase}/reverse?lat=${encodeURIComponent(lat)}&lon=${encodeURIComponent(lng)}`;
                    const res = await fetch(url, { headers: { Accept: 'application/json' } });
                    const j = await res.json();
                    if (! res.ok) {
                        throw new Error(j.error || 'reverse');
                    }
                    if (j.display_name) {
                        this.alamatJalan = j.display_name;
                    }
                    const pc = j.address?.postcode;
                    if (pc) {
                        this.kodepos = String(pc).replace(/\s+/g, '').slice(0, 16);
                    }
                    await this.applyGuessFromNominatim(j.address || {});
                } catch {
                    this.mapErr = 'Gagal mengambil alamat dari titik peta.';
                }
            },

            async applyGuessFromNominatim(addr) {
                const provGuess = addr.state || addr.region || '';
                const kabGuess = addr.county || addr.city || addr.municipality || '';
                const kecGuess = addr.city_district || addr.suburb || addr.town || '';
                const kelGuess = addr.village || addr.hamlet || addr.neighbourhood || '';

                if (provGuess && this.provinces.length) {
                    const p = this.pickByName(this.provinces, provGuess);
                    if (p && ! this.sameCode(this.kodeProv, p.code)) {
                        this.kodeProv = p.code;
                        this.syncNamaProv();
                        await this.loadRegencies(true);
                    }
                }
                if (kabGuess && this.regencies.length) {
                    const k = this.pickByName(this.regencies, kabGuess);
                    if (k && ! this.sameCode(this.kodeKab, k.code)) {
                        this.kodeKab = k.code;
                        this.syncNamaKab();
                        await this.loadDistricts(true);
                    }
                }
                if (kecGuess && this.districts.length) {
                    const k = this.pickByName(this.districts, kecGuess);
                    if (k && ! this.sameCode(this.kodeKec, k.code)) {
                        this.kodeKec = k.code;
                        this.syncNamaKec();
                        await this.loadVillages(true);
                    }
                }
                if (kelGuess && this.villages.length) {
                    const k = this.pickByName(this.villages, kelGuess);
                    if (k) {
                        this.kodeKel = k.code;
                        this.syncNamaKel();
                    }
                }
                await this.$nextTick();
                this.refreshSelectBindings();
            },

            async runMapSearch() {
                this.searchErr = '';
                this.searchResults = [];
                const q = this.searchQuery.trim();
                if (q.length < 3) {
                    this.searchErr = 'Ketik minimal 3 karakter untuk mencari.';

                    return;
                }

                this.searchLoading = true;
                try {
                    const params = new URLSearchParams({ q, limit: '8' });
                    const res = await fetch(`${this.nomBase}/search?${params.toString()}`, {
                        headers: { Accept: 'application/json' },
                    });
                    const data = await res.json();
                    if (! res.ok) {
                        const msg = typeof data?.error === 'string' ? data.error : 'Gagal mencari.';

                        throw new Error(msg);
                    }
                    if (! Array.isArray(data)) {
                        throw new Error('invalid');
                    }
                    this.searchResults = data
                        .map((row) => ({
                            lat: Number.parseFloat(row.lat),
                            lon: Number.parseFloat(row.lon),
                            display_name: String(row.display_name || ''),
                            address: row.address && typeof row.address === 'object' ? row.address : {},
                        }))
                        .filter((row) => Number.isFinite(row.lat) && Number.isFinite(row.lon));

                    if (this.searchResults.length === 0) {
                        this.searchErr = 'Tidak ada hasil di Indonesia. Coba kata kunci lain.';
                    }
                } catch {
                    this.searchErr = 'Gagal mencari lokasi. Coba lagi.';
                } finally {
                    this.searchLoading = false;
                }
            },

            async pickSearchResult(row) {
                this.searchErr = '';
                this.ensureMap();
                await this.$nextTick();
                if (! this.map) {
                    return;
                }

                const lat = row.lat;
                const lng = row.lon;
                this.map.setView([lat, lng], Math.max(this.map.getZoom(), 15));
                this.placeMarker(lat, lng);
                this.syncCoordInputs(lat, lng);

                if (row.display_name) {
                    this.alamatJalan = row.display_name;
                }
                const pc = row.address?.postcode;
                if (pc) {
                    this.kodepos = String(pc).replace(/\s+/g, '').slice(0, 16);
                }
                await this.applyGuessFromNominatim(row.address || {});

                this.searchResults = [];
                setTimeout(() => this.map?.invalidateSize(), 150);
            },

            async applyManualCoordinates() {
                this.coordErr = '';
                this.mapErr = '';

                const lat = this.parseCoord(this.manualLat);
                const lng = this.parseCoord(this.manualLng);

                if (! Number.isFinite(lat) || ! Number.isFinite(lng)) {
                    this.coordErr = 'Isi lintang dan bujur dengan angka yang valid (titik atau koma sebagai desimal).';

                    return;
                }
                if (lat < -90 || lat > 90 || lng < -180 || lng > 180) {
                    this.coordErr = 'Lintang harus antara -90 dan 90; bujur antara -180 dan 180 (WGS84).';

                    return;
                }

                this.ensureMap();
                await this.$nextTick();
                if (! this.map) {
                    this.coordErr = 'Peta belum siap. Buka kembali langkah Lokasi.';

                    return;
                }

                this.map.setView([lat, lng], Math.max(this.map.getZoom(), 16));
                this.placeMarker(lat, lng);
                this.syncCoordInputs(lat, lng);
                await this.reverseAt(lat, lng);
                setTimeout(() => this.map?.invalidateSize(), 150);
            },

            async init() {
                this._onStepListener = (e) => this.onWizardStep(e);
                window.addEventListener('lembaga-wizard-step', this._onStepListener);
                await this.loadProvinces();
                await this.syncCodesFromNames();
            },

            destroy() {
                if (this._onStepListener) {
                    window.removeEventListener('lembaga-wizard-step', this._onStepListener);
                }
                if (this.map) {
                    this.map.remove();
                    this.map = null;
                    this.marker = null;
                    this.mapBooted = false;
                }
            },
        }));
    });
}
