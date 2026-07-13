{{--
    Shared notifications partial: toastr flash messages + SweetAlert2 confirms.
    Include once per page. Use on a delete form:  <form ... data-confirm="..."> ,
    or in JS:  aaConfirm({...}) / aaConfirmDelete({...})  (both return a Promise).
--}}
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
(function () {
    // --- Flash messages -> toastr (auto-dismissing, like the rest of the CRM) ---
    function flash() {
        if (typeof toastr === 'undefined') return;
        toastr.options = {
            closeButton: true, progressBar: true, positionClass: 'toast-top-right',
            timeOut: 4000, extendedTimeOut: 1500, preventDuplicates: true
        };
        @if (session('success')) toastr.success(@json(session('success')), 'Success'); @endif
        @if (session('error'))   toastr.error(@json(session('error')), 'Error'); @endif
        @if (session('warning')) toastr.warning(@json(session('warning'))); @endif
        @if ($errors->any())     toastr.error(@json($errors->first()), 'Please check'); @endif
    }

    // --- Reusable SweetAlert2 confirmation ---
    window.aaConfirm = function (opts) {
        opts = opts || {};
        return Swal.fire({
            title: opts.title || 'Are you sure?',
            text:  opts.text  || '',
            icon:  opts.icon  || 'warning',
            showCancelButton: true,
            confirmButtonColor: opts.confirmColor || '#f46a6a',
            cancelButtonColor:  '#74788d',
            confirmButtonText:  opts.confirmText || 'Yes',
            reverseButtons: true
        });
    };
    window.aaConfirmDelete = function (opts) {
        return aaConfirm(Object.assign({
            title: 'Delete?', text: 'This action cannot be undone.', confirmText: 'Yes, delete it!'
        }, opts || {}));
    };

    // --- Auto-wire any <form data-confirm="..."> to a SweetAlert2 prompt ---
    function wireConfirms() {
        document.addEventListener('submit', function (e) {
            var form = e.target.closest('form[data-confirm]');
            if (!form || form.dataset.confirmed === '1') return;
            e.preventDefault();
            aaConfirmDelete({
                title: form.dataset.confirmTitle || 'Delete?',
                text:  form.dataset.confirm,
                confirmText: form.dataset.confirmText || 'Yes, delete it!'
            }).then(function (r) {
                if (r.isConfirmed) { form.dataset.confirmed = '1'; form.submit(); }
            });
        }, true);
    }

    document.addEventListener('DOMContentLoaded', function () { flash(); wireConfirms(); });
})();
</script>
