@if ($meeting_row->group_type == $this::MEETING_PAST)
    <span class="label label-default"> Sapulce beigusies </span> 
@endif

@if ($meeting_row->group_type == $this::MEETING_FUTURE)
    <span class="label label-info"> Sagatavošanā </span> 
@endif

@if ($meeting_row->group_type == $this::MEETING_ACTIVE)
    <span class="label label-success"> Kārtējā sapulce </span> 
@endif