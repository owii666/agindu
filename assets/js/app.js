/**
 * AGINDU - Main Application Scripts
 */

document.addEventListener('DOMContentLoaded', function () {
    // Mobile Sidebar Toggle
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.getElementById('appSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (toggleBtn && sidebar && overlay) {
        toggleBtn.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }

    // Auto-dismiss alert boxes after 6 seconds
    const alerts = document.querySelectorAll('.alert-box');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.4s ease';
            alert.style.opacity = '0';
            setTimeout(function () {
                if (alert.parentNode) alert.parentNode.removeChild(alert);
            }, 400);
        }, 6000);
    });
});

/**
 * Salin teks ke papan klip dengan feedback tombol
 */
function copyTextToClipboard(text, buttonElement, successText = 'Tautan Tersalin') {
    if (!navigator.clipboard) {
        // Fallback untuk browser lama
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
            document.execCommand('copy');
            showCopyFeedback(buttonElement, successText);
        } catch (err) {
            alert('Gagal menyalin tautan: ' + err);
        }
        document.body.removeChild(textarea);
        return;
    }

    navigator.clipboard.writeText(text).then(function () {
        showCopyFeedback(buttonElement, successText);
    }).catch(function (err) {
        alert('Gagal menyalin tautan: ' + err);
    });
}

function showCopyFeedback(buttonElement, text) {
    if (!buttonElement) return;
    const originalHtml = buttonElement.innerHTML;
    buttonElement.setAttribute('disabled', 'true');
    buttonElement.innerHTML = `
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"></polyline>
        </svg>
        <span>${text}</span>
    `;
    setTimeout(function () {
        buttonElement.innerHTML = originalHtml;
        buttonElement.removeAttribute('disabled');
    }, 2200);
}
