@extends('constructor.common')

@section('constructor_content')
<div style="width: 75%; margin: 0 auto;">
  <div class="col-md-12">
    <div class='table-scrollable'>
      <table class="table table-bordered table-hover dx-constructor-roles-table">
        <tbody>
          @foreach($roles as $role)
            <tr>
              <td width="30%">
                <i class="fa fa-key"></i>
                <a href="javascript:;" class="dx-constructor-edit-role" data-role_id="{{ $role->pivot->id }}">{{ $role->title }}</a>
              </td>
              <td>
                @if($role->pivot->is_new_rights)
                  <label class="badge badge-default">{{ trans('constructor.is_new_rights') }}</label>
                @endif
                @if($role->pivot->is_edit_rights)
                  <label class="badge badge-default">{{ trans('constructor.is_edit_rights') }}</label>
                @endif
                @if($role->pivot->is_delete_rights)
                  <label class="badge badge-default">{{ trans('constructor.is_delete_rights') }}</label>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
