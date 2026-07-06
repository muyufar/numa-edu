import axios from 'axios';
import { Html5Qrcode } from 'html5-qrcode';
import * as faceapi from '@vladmandic/face-api';

const MODEL_URL = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.7.14/model';

function initPresensiScan() {
    const root = document.getElementById('presensi-scan-root');
    if (!root) {
        return;
    }

    const type = root.dataset.type;
    const barcodeUrl = root.dataset.barcodeUrl;
    const faceUrl = root.dataset.faceUrl;
    const enrollUrlTemplate = root.dataset.enrollUrlTemplate;
    const today = root.dataset.today;
    const people = JSON.parse(root.dataset.people || '[]');

    const logList = document.getElementById('presensi-scan-log-list');
    const kelasSelect = document.getElementById('presensi-scan-kelas');
    const enrollSelect = document.getElementById('presensi-face-enroll-select');
    const enrollBtn = document.getElementById('presensi-face-enroll-btn');

    let html5QrCode = null;
    let faceModelsLoaded = false;
    let faceStream = null;
    let lastScanAt = 0;

    axios.defaults.headers.common['X-CSRF-TOKEN'] = root.dataset.csrf;
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    people.forEach((person) => {
        const opt = document.createElement('option');
        opt.value = person.id;
        opt.textContent = person.nama + (person.has_face ? ' ✓' : '');
        enrollSelect?.appendChild(opt);
    });

    function appendLog(message, ok = true) {
        const li = document.createElement('li');
        li.className = ok ? 'text-emerald-800' : 'text-red-700';
        li.textContent = `[${new Date().toLocaleTimeString()}] ${message}`;
        logList?.prepend(li);
    }

    async function postBarcode(kode) {
        const now = Date.now();
        if (now - lastScanAt < 2000) {
            return;
        }
        lastScanAt = now;

        try {
            const { data } = await axios.post(barcodeUrl, { kode, tanggal: today });
            appendLog(data.message, data.ok);
        } catch (error) {
            const msg = error.response?.data?.message || error.message;
            appendLog(msg, false);
        }
    }

    async function postFace(descriptor) {
        try {
            const payload = {
                descriptor,
                tanggal: today,
            };
            if (kelasSelect?.value) {
                payload.kelas_id = kelasSelect.value;
            }
            const { data } = await axios.post(faceUrl, payload);
            appendLog(data.message, data.ok);
            return data.ok;
        } catch (error) {
            const msg = error.response?.data?.message || error.message;
            appendLog(msg, false);
            return false;
        }
    }

    async function startBarcodeScanner() {
        if (html5QrCode) {
            return;
        }

        html5QrCode = new Html5Qrcode('presensi-barcode-reader');
        await html5QrCode.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            (decoded) => postBarcode(decoded),
            () => {}
        );
    }

    async function stopBarcodeScanner() {
        if (!html5QrCode) {
            return;
        }
        try {
            await html5QrCode.stop();
            html5QrCode.clear();
        } catch (e) {
            // ignore
        }
        html5QrCode = null;
    }

    async function loadFaceModels() {
        if (faceModelsLoaded) {
            return;
        }
        await Promise.all([
            faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
            faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
            faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
        ]);
        faceModelsLoaded = true;
    }

    async function startFaceCamera() {
        await loadFaceModels();
        const video = document.getElementById('presensi-face-video');
        faceStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
        video.srcObject = faceStream;
        document.getElementById('presensi-face-capture')?.removeAttribute('disabled');
    }

    async function captureFaceDescriptor() {
        const video = document.getElementById('presensi-face-video');
        const detection = await faceapi
            .detectSingleFace(video, new faceapi.TinyFaceDetectorOptions())
            .withFaceLandmarks()
            .withFaceDescriptor();

        if (!detection) {
            appendLog('Wajah tidak terdeteksi. Coba lagi.', false);
            return null;
        }

        return Array.from(detection.descriptor);
    }

    document.querySelectorAll('.presensi-scan-tab').forEach((btn) => {
        btn.addEventListener('click', async () => {
            const tab = btn.dataset.tab;
            document.querySelectorAll('.presensi-scan-tab').forEach((b) => {
                b.classList.remove('bg-nu-primary', 'text-white');
                b.classList.add('border', 'border-gray-200', 'bg-white', 'text-gray-700');
            });
            btn.classList.add('bg-nu-primary', 'text-white');
            btn.classList.remove('border', 'border-gray-200', 'bg-white', 'text-gray-700');

            document.querySelectorAll('.presensi-scan-panel').forEach((panel) => panel.classList.add('hidden'));
            document.getElementById(`presensi-scan-panel-${tab}`)?.classList.remove('hidden');

            if (tab === 'barcode') {
                if (faceStream) {
                    faceStream.getTracks().forEach((t) => t.stop());
                    faceStream = null;
                }
                await startBarcodeScanner();
            } else {
                await stopBarcodeScanner();
            }
        });
    });

    document.getElementById('presensi-barcode-submit')?.addEventListener('click', () => {
        const kode = document.getElementById('presensi-barcode-manual')?.value?.trim();
        if (kode) {
            postBarcode(kode);
        }
    });

    document.getElementById('presensi-face-start')?.addEventListener('click', async () => {
        try {
            await startFaceCamera();
            appendLog('Kamera wajah aktif.');
        } catch (e) {
            appendLog('Gagal membuka kamera: ' + e.message, false);
        }
    });

    document.getElementById('presensi-face-capture')?.addEventListener('click', async () => {
        const descriptor = await captureFaceDescriptor();
        if (descriptor) {
            await postFace(descriptor);
        }
    });

    enrollSelect?.addEventListener('change', () => {
        if (enrollSelect.value) {
            enrollBtn?.removeAttribute('disabled');
        } else {
            enrollBtn?.setAttribute('disabled', 'disabled');
        }
    });

    enrollBtn?.addEventListener('click', async () => {
        const personId = enrollSelect?.value;
        if (!personId) {
            return;
        }

        if (!faceStream) {
            try {
                await startFaceCamera();
            } catch (e) {
                appendLog('Gagal membuka kamera: ' + e.message, false);
                return;
            }
        }

        const descriptor = await captureFaceDescriptor();
        if (!descriptor) {
            return;
        }

        const url = enrollUrlTemplate.replace('__ID__', personId);

        try {
            const { data } = await axios.post(url, { descriptor });
            appendLog(data.message, data.ok);
            if (data.ok) {
                const opt = enrollSelect.selectedOptions[0];
                if (opt && !opt.textContent.includes('✓')) {
                    opt.textContent += ' ✓';
                }
            }
        } catch (error) {
            appendLog(error.response?.data?.message || error.message, false);
        }
    });

    startBarcodeScanner().catch((e) => appendLog('Kamera barcode: ' + e.message, false));
}

document.addEventListener('DOMContentLoaded', initPresensiScan);
