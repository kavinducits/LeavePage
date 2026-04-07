<script>
(function () {
    'use strict';

    if (window.AppPopup) {
        return;
    }

    var baseConfirmStyles = {
        confirmButtonColor: '#800020',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusCancel: true
    };

    function hasSwal() {
        return typeof window.Swal !== 'undefined' && typeof window.Swal.fire === 'function';
    }

    function fallbackText(value, defaultValue) {
        if (typeof value === 'string' && value.trim() !== '') {
            return value.trim();
        }
        return defaultValue;
    }

    window.AppPopup = {
        confirm: function (options) {
            var cfg = Object.assign({
                icon: 'warning',
                title: 'Please Confirm',
                text: 'Are you sure?',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }, baseConfirmStyles, options || {});

            if (!hasSwal()) {
                var ok = window.confirm(cfg.text || cfg.title || 'Are you sure?');
                return Promise.resolve({ isConfirmed: ok });
            }

            return window.Swal.fire(cfg);
        },

        success: function (message, title, timerMs) {
            var resolvedTimer = typeof timerMs === 'number' && timerMs > 0 ? timerMs : 1600;

            if (!hasSwal()) {
                return Promise.resolve();
            }

            return window.Swal.fire({
                icon: 'success',
                title: fallbackText(title, 'Success'),
                text: fallbackText(message, ''),
                showConfirmButton: false,
                timer: resolvedTimer,
                timerProgressBar: true
            });
        },

        error: function (message, title) {
            var msg = fallbackText(message, 'Something went wrong.');

            if (!hasSwal()) {
                window.alert(msg);
                return Promise.resolve();
            }

            return window.Swal.fire({
                icon: 'error',
                title: fallbackText(title, 'Error'),
                text: msg,
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        }
    };
})();
</script>
