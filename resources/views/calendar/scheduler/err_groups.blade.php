@foreach($groups as $group)
<div class='dx-event dx-group animated bounceInUp'>
    <span class="dx-item-title">{{ $group['group_title'] }}</span>
    <ul>
        @foreach($group['errors'] as $err)
        <li>{{ $err['err_text']}}: <a href='javascript:;' class='dx-err-action' data-list-id='{{ $err['list_id'] }}' data-item-id='{{ $err['item_id'] }}'>{{ $err['title']}}</a></li>
        @endforeach
    </ul>
    <div>
        <a class="btn btn-xs btn-primary btn-edit-err-group pull-right" href="javascript:;" data-group-id="{{ $group['group_id'] }}"> {{ trans('calendar.scheduler.btn_edit_err_group')}}</a><a class="dx-solved-group pull-right" href="javascript:;" style="margin-right: 10px">{{ trans('calendar.scheduler.btn_solve_err_group')}}</a>
    </div>
</div>
@endforeach