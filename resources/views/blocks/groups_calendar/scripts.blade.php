<script>

    $(function () {

        var group_calendar = window.group_calendar = {

            go: function (dir) {

                show_page_splash(1);

                var route = $('#fpcT').data('route-' + dir);

                $.get(route, function (calendar) {
                    $('#groups_calendar_holder').html(calendar);
                    hide_page_splash(1);
                });
            },
        }
    });

</script>