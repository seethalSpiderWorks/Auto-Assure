{{--
    The shareable report URLs for this inspection — English and Arabic, the pair
    the legacy view-report screen prints ("English Link" / "Arabic Link"). Both
    carry the same unguessable token and open the same record; only the language
    differs. Expects $inspection and, optionally, $btnClass.
--}}
@php
    $clientReportUrl = $inspection->reportUrl();
    $clientReportUrlAr = $inspection->reportUrlAr();
@endphp
<button type="button"
        class="{{ $btnClass ?? 'btn btn-light btn-sm' }}"
        title="{{ $clientReportUrl }}"
        data-client-report-url="{{ $clientReportUrl }}"
        onclick="copyClientReportLink(this)"><i class="bx bx-link"></i> Copy Client Link (EN)</button>
<button type="button"
        class="{{ $btnClass ?? 'btn btn-light btn-sm' }}"
        title="{{ $clientReportUrlAr }}"
        data-client-report-url="{{ $clientReportUrlAr }}"
        onclick="copyClientReportLink(this)"><i class="bx bx-link"></i> نسخ الرابط (AR)</button>

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
