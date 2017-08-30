@foreach($groups as $group)                                        

    <div class='dx-event dx-group dx-status-{{ $group->status }}' data-subject-id="{{ $group->subject_id }}" data-group-id="{{ $group->id }}">
        <div>
            <span class="dx-item-title">{{ $group->title }}</span>
        </div>

        @if ($orgs_count > 1)
        <div class="dx-org-title">{{ $group->org_title }}</div>                                            
        @endif
        
        <div class="dx-limit-info">
            <span>Limits: {{ $group->places_quota }}</span> | 
            @if ($group->status == 'full')
                <span>Grupa pilna</span>
            @else
                <span>Brīvas vietas: {{ $group->places_quota - $group->member_count }}</span>
                @if ($group->signup_due)
                    <span class="dx-signup-due">Pieteikšanās termiņš: {{ long_date($group->signup_due) }}</span>
                @endif
            @endif
            <a class="pull-right dx-group-edit" href="javascript:;" data-group-id={{ $group->id }} data-org-id={{ $group->org_id }}>Komplektēt</a>
        </div>
    </div>

@endforeach