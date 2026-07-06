import QRCode from 'qrcode';

function initPresensiKartu() {
    const canvas = document.getElementById('presensi-kartu-qr');
    const kode = document.getElementById('presensi-kartu-root')?.dataset?.kode;

    if (!canvas || !kode) {
        return;
    }

    QRCode.toCanvas(canvas, kode, { width: 220, margin: 1, errorCorrectionLevel: 'M' })
        .catch((error) => {
            console.error('Gagal membuat QR kartu presensi:', error);
            const fallback = document.getElementById('presensi-kartu-qr-fallback');
            if (fallback) {
                fallback.classList.remove('hidden');
            }
        });
}

document.addEventListener('DOMContentLoaded', initPresensiKartu);
