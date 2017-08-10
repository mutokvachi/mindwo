@foreach($results as $res)  
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
                <span style="font-weight:bold;">
                    {{ $res->min_lesson_date ? $res->min_lesson_date->format('d.m.Y') : '' }} {{ $res->max_lesson_date ? ' - ' .$res->max_lesson_date->format('d.m.Y') : '' }}
                </span>
            </div>
            <div>
                <a href="{{Request::root()}}/edu/course/{{ $group->id }}" class="btn btn-default btn-sm">Uzzināt vairāk</a>
                @if($res->is_not_full)
                    <button class="btn btn-sm btn-danger disabled">Visas grupas ir pilnas</button>
                @else
                    <a href="{{Request::root()}}/edu/registration/{{ $group->id }}" class="btn btn-sm btn-primary">Pieteikties</a>
                @endif
            </div>                        
        </div>
    </div>
</div>
@endforeach