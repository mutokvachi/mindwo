<script>

    /**
     *
     * Calculate the time remaining.
     * Calculate the difference between given time and the current time.
     *
     */
    function getTimeRemaining(end_time) {

        // Convert the time to a usable format
        var t = Date.parse(end_time) - Date.parse(new Date());

        /**
         * Convert the milliseconds to days, hours, minutes, and seconds
         *
         * 1. Divide milliseconds by 1000 to convert to seconds.
         * 2. Divide the total seconds by 60 and grab the remainder. (We don’t need all of the seconds).
         * 3. Round down to nearest whole number to get complete second.
         *
         * */
        var seconds = Math.floor((t / 1000) % 60);
        var minutes = Math.floor((t / 1000 / 60) % 60);
        var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
        var days = Math.floor(t / (1000 * 60 * 60 * 24));

        return {
            'total': t,
            'days': days,
            'hours': hours,
            'minutes': minutes,
            'seconds': seconds
        };
    }

    /**
     *
     * Prepare clock for display.
     * Display the clock and stop when it reaches zero.
     *
     */
    function initializeClock(id, end_time) {

        // Get clock data and check if time is not expired
        var clock_data = getTimeRemaining(end_time);
        if (clock_data.total <= 0) {
            $('#countdown').hide('explode', 'slow');
            $('#success_text').show('blind', 'slow');

            // Time is expired.
            return;
        } else {
            $('#waiting_text').show();
            $('#countdown').show('blind');
        }

        var clock = document.getElementById(id);
        var daysSpan = clock.querySelector('.days');
        var hoursSpan = clock.querySelector('.hours');
        var minutesSpan = clock.querySelector('.minutes');
        var secondsSpan = clock.querySelector('.seconds');


        // Function to update only numbers instead of all whole clock
        function updateClock() {
            var t = getTimeRemaining(end_time);

            // Add a leading zeros and output remaining time
            daysSpan.innerHTML = t.days;
            //daysSpan.innerHTML = ('0' + t.days).slice(-2);
            hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
            minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
            secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

            // If the remaining time gets to zero, stop the clock.
            if (t.total <= 0) {
                clearInterval(time_interval);

                $('#countdown').hide('explode', 'slow');
                $('#success_text').show('blind', 'slow');
                $('#waiting_text').hide();
            }
        }

        // Run function once at first to avoid delay
        updateClock();

        // Set interval to update clock every second.
        var time_interval = setInterval(updateClock, 1000);
    }

    $(function () {

        // From view get our deadline
        var deadline_val = $("#deadline").val();
        var deadline_date = new Date(Date.parse(deadline_val));

        initializeClock('countdown', deadline_date);

    });

</script>