<ul>
@foreach($days as $day)
    <li><b>{{ $day["title_subject"] }}</b>
        <br>        
        Nodarbības laiks: {{ short_date($day["lesson_date"]) }} {{ $day["time_from"] }} - {{ $day["time_to"] }}
        <br>
        Vieta: {{ $day["title_org"] }}, {{ $day["room_address"] }}, telpa nr. {{ $day["room_nr"]}}
    </li>
@endforeach
</ul>