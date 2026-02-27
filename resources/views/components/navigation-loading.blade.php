<div id="nav-loading-overlay" class="nav-loading-overlay" aria-live="polite" aria-hidden="true">
    <div class="nav-loading-card">
        <div class="nav-loading-spinner" aria-hidden="true"></div>
        <div class="nav-loading-title">Loading page</div>
        <div class="nav-loading-subtitle">Please wait, this may take a moment.</div>
    </div>
</div>

<style>
    .nav-loading-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(17, 24, 39, 0.35);
        backdrop-filter: blur(2px);
    }

    .nav-loading-card {
        min-width: 260px;
        max-width: 90vw;
        padding: 16px 18px;
        border-radius: 14px;
        background: #ffffff;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.2);
        text-align: center;
    }

    .nav-loading-spinner {
        width: 36px;
        height: 36px;
        margin: 0 auto 10px;
        border-radius: 9999px;
        border: 3px solid #e5e7eb;
        border-top-color: #2563eb;
        animation: nav-spin 0.8s linear infinite;
    }

    .nav-loading-title {
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .nav-loading-subtitle {
        margin-top: 4px;
        font-size: 12px;
        color: #4b5563;
    }

    @keyframes nav-spin {
        to { transform: rotate(360deg); }
    }
</style>

<script>
    (function () {
        const overlayId = 'nav-loading-overlay';
        let timer = null;

        function getOverlay() {
            return document.getElementById(overlayId);
        }

        function showLoaderWithDelay() {
            clearTimeout(timer);
            timer = setTimeout(() => {
                const overlay = getOverlay();
                if (!overlay) return;
                overlay.style.display = 'flex';
                overlay.setAttribute('aria-hidden', 'false');
            }, 220);
        }

        function hideLoader() {
            clearTimeout(timer);
            const overlay = getOverlay();
            if (!overlay) return;
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
        }

        function shouldHandleAnchor(anchor, event) {
            if (!anchor) return false;
            const href = anchor.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return false;
            if (anchor.hasAttribute('download')) return false;
            if ((anchor.getAttribute('target') || '').toLowerCase() === '_blank') return false;
            if (anchor.hasAttribute('data-no-loading')) return false;
            if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
            try {
                const url = new URL(anchor.href, window.location.href);
                if (url.origin !== window.location.origin) return false;
            } catch (e) {
                return false;
            }
            return true;
        }

        document.addEventListener('click', function (event) {
            const anchor = event.target.closest('a');
            if (!shouldHandleAnchor(anchor, event)) return;
            showLoaderWithDelay();
        }, true);

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!form || form.hasAttribute('data-no-loading')) return;
            showLoaderWithDelay();
        }, true);

        window.addEventListener('pageshow', hideLoader);
        window.addEventListener('DOMContentLoaded', hideLoader);
    })();
</script>
