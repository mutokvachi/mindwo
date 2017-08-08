@foreach($results as $res)  
<?php 
if($res->gr_id){
    $group = \App\Models\Education\SubjectGroup::find($res->gr_id);
    $days = $group->days()->orderBy('lesson_date')->get();
}
?>    
<div class='row' style="margin-bottom: 10px; padding-bottom:10px; border-bottom: 1px solid #eee; margin-left: 5px; margin-right: 5px;">
    <div class='col-lg-1 col-md-2'>
        <div style="width:60px; height:60px; background-color: #40574d; float:right; margin-top: 10px;">
            <i class="{{ $group->icon ? $group->icon : 'fa fa-university' }}" style="color:white; font-size:24px; margin-left:18px; margin-top:22px;"> </i>                     
        </div>
    </div>
    <div class='col-lg-11 col-md-10'>
        <div>
            <h4>{{ $res->title }}</h4>
        </div>
        <div>
            <div style="margin-bottom:3px;">
                @if($res->gr_id)
                    @if(count($days) > 1)
                    <span style="font-weight:bold;">{{ $days->get(0)->lesson_date->format('d.m.Y') }} - {{ $days->get(count($days) - 1)->lesson_date->format('d.m.Y') }}</span>
                    @elseif(count($days) > 0)
                    <span style="font-weight:bold;">{{ $days->first()->lesson_date->format('d.m.Y') }}</span> 
                    {{ date('H:i', strtotime($days->first()->time_from)) . ' - ' . date('H:i', strtotime($days->first()->time_to)) }}
                    @endif
                @endif
            </div>
            <div>
                <a href="{{Request::root()}}/edu/course/{{ $group->id }}" class="btn btn-default btn-sm">Uzzināt vairāk</a>
                @if($res->gr_id && $res->gr_id > 0)
                    @if($group->members->count() >= $group->seats_limit)
                    <button class="btn btn-sm btn-danger disabled">Grupa ir pilna</button>
                    @else
                    <a href="{{Request::root()}}/edu/registration/{{ $group->id }}" class="btn btn-sm btn-primary">Pieteikties</a>
                    @endif
                @endif
            </div>                        
        </div>
    </div>
</div>
@endforeach