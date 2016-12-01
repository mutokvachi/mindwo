<div class="portlet widget-profile" dx_block_id="widget_profile">    
    <div class="portlet-body">
        <div class="mt-widget-1">
            <div class="mt-icon">
                <a href="#">
                    <i class="icon-plus"></i>
                </a>
            </div>
            <div class="mt-img">
                <img src="{{ $user->getAvatar() }}"> </div>
            <div class="mt-body">
                <h3 class="mt-username"><a href="{{ Config::get('dx.employee_profile_page_url') . $user->id }}" title="View profile">{{ $user->display_name }}</a></h3>
                <p class="mt-user-title"> {{ $user->position }} </p>
            </div>
        </div>
    </div>
</div>