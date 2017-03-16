<div class="portlet light" id="countdown-holder">
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12 text-center">
                <h4 id="waiting_text">{!!  $waiting_text !!}</h4>
                <h4 id="success_text">{!! $success_text !!}</h4>
                <table class="table countdown-timer" id="countdown">
                    <tbody>
                    <tr>
                        <td>
                            <span class="badge badge-info badge-roundless days">00</span>
                        </td>
                        <td>
                            <span class="badge badge-info badge-roundless hours">00</span>
                        </td>
                        <td>
                            <span class="badge badge-info badge-roundless minutes">00</span>
                        </td>
                        <td>
                            <span class="badge badge-info badge-roundless seconds">00</span>
                        </td>
                    </tr>
                    <tr class="timer-labels">
                        <td>{{ trans('countdown_timer.days') }}</td>
                        <td>{{ trans('countdown_timer.hours') }}</td>
                        <td>{{ trans('countdown_timer.minutes') }}</td>
                        <td>{{ trans('countdown_timer.seconds') }}</td>
                    </tr>
                    </tbody>
                </table>
                <input type="hidden" id="deadline" value="{{ $deadline }}">
            </div>
        </div>
    </div>
</div>