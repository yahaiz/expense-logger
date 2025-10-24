/**
 * Datepicker Portal Helper
 * Moves .datepicker-plot-area to document.body and positions it fixed
 * so it can appear above modals and other clipped containers.
 */
(function() {
    function ensurePortal(dateInputSelector) {
        const input = document.querySelector(dateInputSelector);
        if (!input) return;

        // Use a MutationObserver to detect when the datepicker element is created
        // and move it into document.body. Also provide a short polling fallback
        // for cases where creation timing is inconsistent.
        const observer = new MutationObserver((mutations) => {
            for (const m of mutations) {
                for (const node of m.addedNodes) {
                    if (node.nodeType !== 1) continue;
                    if (node.classList && node.classList.contains('datepicker-plot-area')) {
                        movePicker(node);
                        return;
                    }
                    const nested = node.querySelector && node.querySelector('.datepicker-plot-area');
                    if (nested) {
                        movePicker(nested);
                        return;
                    }
                }
            }
        });

        observer.observe(document.body, { childList: true, subtree: true });

        // Also attempt to move on focus/click with a retry loop in case the
        // picker is created shortly after the event.
        input.addEventListener('focus', () => tryMoveWithRetries());
        input.addEventListener('click', () => tryMoveWithRetries());

        function tryMoveWithRetries(attempts = 0) {
            const picker = document.querySelector('.datepicker-plot-area');
            if (picker) {
                movePicker(picker);
                return;
            }
            if (attempts >= 12) return; // stop after ~1.2s
            setTimeout(() => tryMoveWithRetries(attempts + 1), 100);
        }

        function movePicker(picker) {
            if (!picker) return;

            // Append to body if not already
            if (picker.parentElement !== document.body) {
                document.body.appendChild(picker);
            }

            // Ensure fixed positioning and an extremely high z-index so the
            // picker sits above modals/backdrops.
            picker.style.position = 'fixed';
            picker.style.zIndex = '2147483647';
            picker.style.pointerEvents = 'auto';

            positionPicker(input, picker);

            // Reposition on scroll/resize
            window.addEventListener('scroll', () => positionPicker(input, picker), { passive: true });
            window.addEventListener('resize', () => positionPicker(input, picker));
        }

        function positionPicker(input, picker) {
            const rect = input.getBoundingClientRect();
            // Prefer below the input; if not enough space, show above
            const spaceBelow = window.innerHeight - rect.bottom;
            const spaceAbove = rect.top;
            const pickerHeight = picker.offsetHeight || 280;

            let top;
            if (spaceBelow < pickerHeight && spaceAbove > spaceBelow) {
                // place above
                top = rect.top - pickerHeight - 8;
            } else {
                // place below
                top = rect.bottom + 8;
            }

            // Align horizontally with the input but keep within viewport
            let left = rect.left;
            const maxLeft = window.innerWidth - picker.offsetWidth - 8;
            if (left > maxLeft) left = Math.max(8, maxLeft);
            if (left < 8) left = 8;

            picker.style.top = Math.round(top) + 'px';
            picker.style.left = Math.round(left) + 'px';
        }
    }

    // Expose helper
    window.DatepickerPortal = {
        ensure: ensurePortal
    };
})();
