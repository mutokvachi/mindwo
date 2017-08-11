@foreach($results as $res)  
<div class='row' style="margin-bottom: 10px; padding-bottom:10px; border-bottom: 1px solid #eee; margin-left: 5px; margin-right: 5px;">
    <div class='col-lg-1 col-md-2'>
        <div style="width:60px; height:60px; background-color: #40574d; float:right; margin-top: 10px;">
            <i class="{{ 'fa fa-university' }}" style="color:white; font-size:24px; margin-left:18px; margin-top:22px;"> </i>                     
        </div>
    </div>
    <div class='col-lg-11 col-md-10'>
        <div>
            <h4>{{ $res->title }}</h4>
        </div>
        <div>
            <div style="margin-bottom:3px;">
                <span style="font-weight:bold;">
                    {{ $res->min_lesson_date ? date_create($res->min_lesson_date)->format('d.m.Y') : '' }} 
                    {{ $res->max_lesson_date && $res->max_lesson_date <> $res->min_lesson_date ? ' - ' . date_create($res->max_lesson_date)->format('d.m.Y') : '' }}
                </span>
            </div>
            <div>
                <a href="{{Request::root()}}/edu/course/{{ $res->id }}" class="btn btn-default btn-sm">Uzzināt vairāk</a>
                @if($res->group_count > 0)
                    @if($res->is_not_full == 1)
                        <a href="{{Request::root()}}/edu/registration/{{ $res->id }}" class="btn btn-sm btn-primary">Pieteikties</a>   
                    @else
                        <button class="btn btn-sm btn-danger disabled">Visas grupas ir pilnas</button>
                    @endif 
                @else
                    <button class="btn btn-sm btn-danger disabled">Šobrīd nav pieejams</button>
                @endif
            </div>                        
        </div>
    </div>
</div>
@endforeach