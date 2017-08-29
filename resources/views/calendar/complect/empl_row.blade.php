<div class="dx-empl-info {{ ($empl->is_member) ? 'dx-is-member' : 'dx-is-avail' }}" data-empl-id={{ $empl->id }}>
    <a class="dx-empl-cmd dx-empl-add" href="javascript:;" title="Pievienot grupai kā dalībnieku"><i class="fa fa-plus-square-o"></i></a>
    <a class="dx-empl-cmd dx-empl-remove {{ (isset($dont_tool)) ? $dont_tool : '' }}" href="javascript:;" title="Izņemt darbinieku no grupas"><i class="fa fa-trash-o"></i></a>
    <div class="dx-empl-main">
        <a class="dx-empl-name" href="javascript:;" title="Atvērt dalībnieka kartiņu">{{ $empl->display_name}}</a><span class="dx-empl-code">{{ $empl->person_code}}</span>
        <br>
        <span>{{$empl->job_title}}</span>
    </div>
</div>