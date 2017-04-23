<li>
    <a href="{{ url('/meetings/' . $meeting->meeting_type_id . '/' . $meeting->id) }}">{{ long_date($meeting->meeting_time) }}</a>
</li>