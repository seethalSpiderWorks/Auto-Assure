{{--
    "Copy Client Link" — the shareable report URL for this inspection.
    The link carries the encrypted inspection id, so the customer can open only
    their own report. Expects $inspection and, optionally, $btnClass.
--}}
@php $clientReportUrl = $inspection->reportUrl(); @endphp
<button type="button"
        class="{{ $btnClass ?? 'btn btn-light btn-sm' }}"
        title="{{ $clientReportUrl }}"
        data-client-report-url="{{ $clientReportUrl }}"
        onclick="copyClientReportLink(this)"><i class="bx bx-link"></i> Copy Client Link</button>

@once
    <script>
        function copyClientReportLink(btn) {
            var url = btn.getAttribute('data-client-report-url');
            var done = function () {
                var html = btn.innerHTML;
                btn.innerHTML = '<i class="bx bx-check"></i> Link Copied';
                setTimeout(function () { btn.innerHTML = html; }, 1600);
            };

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(url).then(done, function () { window.prompt('Copy this link:', url); });
                return;
            }

            // http:// pages (no secure context) have no clipboard API — fall back
            // to a hidden textarea, and to a prompt if even that is blocked.
            var ta = document.createElement('textarea');
            ta.value = url;
            ta.style.position = 'fixed';
            ta.style.opacity = '0';
            document.body.appendChild(ta);
            ta.select();
            try { document.execCommand('copy') ? done() : window.prompt('Copy this link:', url); }
            catch (e) { window.prompt('Copy this link:', url); }
            document.body.removeChild(ta);
        }
    </script>
@endonce
