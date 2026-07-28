{{--
    Flatpickr-backed date + time picker.

    Usage: put data-aa-datetime on any <input type="datetime-local"> and include
    this partial once on the page:

        <input type="datetime-local" name="scheduled_at" data-aa-datetime ...>
        @include('partials._datetime_picker')

    Why not the native picker: the browser decides when its popup closes, so
    after choosing a date and a time it sits open until you click elsewhere.
    This swaps in a Flatpickr calendar that closes itself once the time is set.

    The original input is kept (name and value untouched) and turned into a
    hidden field, so what gets posted stays byte-identical to what the native
    datetime-local control posted — "Y-m-dTH:i". Anything already parsing
    scheduled_at server-side keeps working unchanged.

    Assets are injected programmatically rather than via plain <script src>
    tags, because this partial is also rendered into the Leads popup, and a
    <script src> written through innerHTML would never execute.
--}}
<script>
(function () {
    // One initialiser per page, no matter how many times the partial lands.
    if (window.AADateTimePicker) { window.AADateTimePicker.scan(); return; }

    var CSS_URL = @json(asset('assets/libs/flatpickr/flatpickr.min.css'));
    var JS_URL  = @json(asset('assets/libs/flatpickr/flatpickr.min.js'));

    // How long to wait after the last time tweak before closing. Long enough to
    // type "0230" or click the hour arrows a few times without it shutting
    // mid-edit; short enough to still feel automatic.
    var CLOSE_DELAY = 800;

    function ensureCss() {
        if (document.querySelector('link[data-aa-fp]')) return;
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = CSS_URL;
        link.setAttribute('data-aa-fp', '1');
        document.head.appendChild(link);
    }

    function ensureJs(done) {
        if (window.flatpickr) return done();

        var tag = document.querySelector('script[data-aa-fp]');
        if (tag) return tag.addEventListener('load', done);

        tag = document.createElement('script');
        tag.src = JS_URL;
        tag.setAttribute('data-aa-fp', '1');
        tag.addEventListener('load', done);
        document.head.appendChild(tag);
    }

    /**
     * Close the calendar shortly after the user settles on a time.
     *
     * Only real interaction inside the .flatpickr-time row counts. Flatpickr
     * fills the hour/minute boxes by assigning .value directly when a day is
     * clicked, and assigning .value fires no events — so picking a day cannot
     * trip this, which is what keeps the calendar open for the time step.
     */
    function autoCloseOnTime(instance) {
        var timeRow = instance.calendarContainer.querySelector('.flatpickr-time');
        if (!timeRow) return;

        var timer;

        /**
         * Fold whatever the hour/minute boxes currently show into the selected
         * date. Flatpickr only commits a typed time on blur (it binds increment
         * and blur, never input), so closing without this would silently discard
         * a time the user typed. Reading the boxes covers typing, the arrows and
         * the AM/PM toggle alike.
         */
        function commitTime() {
            if (!instance.selectedDates.length) return;

            var hour = parseInt(instance.hourElement.value, 10);
            var minute = parseInt(instance.minuteElement.value, 10);
            if (isNaN(hour) || isNaN(minute)) return;

            if (!instance.config.time_24hr && instance.amPM) {
                hour = (hour % 12) + (instance.amPM.textContent === 'PM' ? 12 : 0);
            }

            var picked = new Date(instance.selectedDates[0]);
            picked.setHours(hour, minute, 0, 0);
            instance.setDate(picked, true);   // true → fires onChange, which syncs the hidden field
        }

        function scheduleClose() {
            clearTimeout(timer);
            if (!instance.selectedDates.length) return;   // time without a date is meaningless
            timer = setTimeout(function () {
                commitTime();
                instance.close();
            }, CLOSE_DELAY);
        }

        timeRow.addEventListener('input', scheduleClose);   // typing
        timeRow.addEventListener('wheel', scheduleClose);   // scroll over hour/minute
        timeRow.addEventListener('click', function (e) {
            // The arrows and the AM/PM toggle each commit a time in one click.
            if (e.target.closest('.arrowUp, .arrowDown, .flatpickr-am-pm')) scheduleClose();
        });

        instance.config.onClose.push(function () { clearTimeout(timer); });
    }

    function initOne(input) {
        if (input.dataset.aaDtReady) return;
        input.dataset.aaDtReady = '1';

        // Visible field the calendar hangs off. The original keeps the name and
        // the posted value; hiding it also kills the native picker.
        var display = document.createElement('input');
        display.type = 'text';
        display.className = input.className;
        display.readOnly = true;                            // pick, don't type
        display.placeholder = input.getAttribute('placeholder') || 'Select date & time';
        input.parentNode.insertBefore(display, input.nextSibling);

        var initial = input.value ? new Date(input.value.replace(' ', 'T')) : null;
        input.type = 'hidden';

        // Mirror the chosen moment back in the native datetime-local format and
        // tell listeners, since assigning .value fires nothing on its own — the
        // inspection edit screen auto-saves off exactly these events.
        function sync(date) {
            var next = date
                ? window.flatpickr.formatDate(date, 'Y-m-d') + 'T' + window.flatpickr.formatDate(date, 'H:i')
                : '';

            // Committing on close re-states a time that is often already set;
            // staying quiet when nothing moved keeps the auto-save to one call.
            if (next === input.value) return;

            input.value = next;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }

        window.flatpickr(display, {
            enableTime: true,
            time_24hr: false,
            minuteIncrement: 5,
            dateFormat: 'd M Y, h:i K',      // display only — `input` holds the real value
            defaultDate: initial,
            onReady: function (dates, str, instance) { autoCloseOnTime(instance); },
            onChange: function (dates) { sync(dates[0] || null); },
        });
    }

    function scan() {
        var fields = document.querySelectorAll('input[data-aa-datetime]:not([data-aa-dt-ready])');
        if (!fields.length) return;

        ensureCss();
        ensureJs(function () {
            fields.forEach(initOne);
        });
    }

    window.AADateTimePicker = { scan: scan };

    scan();
    // The popup markup may be injected before the fields exist in the DOM.
    document.addEventListener('DOMContentLoaded', scan);
})();
</script>
