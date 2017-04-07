@if ($is_setting_rights)
    <div class='btn-group {{ $pull_class }} dx-register-tools'>
        <button  type='button' class='btn btn-white dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>
            <i class='fa fa-cog'></i> <i class='fa fa-caret-down'></i>
        </button>
        <ul class='dropdown-menu pull-right' style="z-index: 50000;">
            <li><a href='javascript:;' class="dx-register-settings">{{ trans('grid.menu_admin_settings') }}</a></li>
            <li><a href='javascript:;' class="dx-view-settings">{{ trans('grid.menu_view_settings') }}</a></li>
            <li><a href='javascript:;' class="dx-form-settings">{{ trans('grid.menu_form_settings') }}</a></li>
        </ul>
    </div>
@endif