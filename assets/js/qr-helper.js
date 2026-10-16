/**
 * AGINDU - QR Code Helper
 * Menggunakan library lokal QRCode.js tanpa CDN
 */

function renderQrCode(containerId, textUrl, size = 180) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '';

    try {
        new QRCode(container, {
            text: textUrl,
            width: size,
            height: size,
            colorDark: "#172820",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.M
        });
    } catch (e) {
        console.error('Gagal membuat QR Code:', e);
        container.innerHTML = '<p style="color:var(--danger);font-size:12px;">Gagal memuat modul QR</p>';
    }
}

/**
 * Unduh hasil render QR sebagai gambar PNG
 */
function downloadQrPng(containerId, filename = 'agindu-batch-qr.png') {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Cari elemen canvas atau img yang dihasilkan QRCode.js
    const canvas = container.querySelector('canvas');
    const img = container.querySelector('img');

    let dataUrl = null;

    if (canvas) {
        dataUrl = canvas.toDataURL('image/png');
    } else if (img && img.src && img.src.startsWith('data:image')) {
        dataUrl = img.src;
    }

    if (!dataUrl) {
        alert('Gambar QR belum siap diunduh. Tunggu 1 detik lalu coba kembali.');
        return;
    }

    const downloadLink = document.createElement('a');
    downloadLink.href = dataUrl;
    downloadLink.download = filename;
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}

/**
 * Buka dialog cetak browser khusus halaman label QR
 */
function printQrLabel() {
    window.print();
}
