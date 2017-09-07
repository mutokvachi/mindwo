<ul>
@foreach($groups as $group)
    <li><b>{{ $group["title_subject"] }}</b><br>
        @if ($group["date_from"] == $group["date_to"])
            Nodarbības datums: {{ short_date($group["date_from"]) }}
        @else
            Nodarbības no {{ short_date($group["date_from"]) }} - {{ short_date($group["date_to"]) }}
        @endif
        <br>Vietu limits - {{ $group["title_org"] }}: <b>{{ $group["places_quota"] }}</b>
    </li>
@endforeach
</ul>