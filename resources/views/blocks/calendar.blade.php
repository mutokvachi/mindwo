<div class="portlet dx-block-container-calendar" data-dx_block_init="0" style="background-color: white; padding: 20px;" 
     data-source_id="{{ $source_id }}"
     data-show_holidays="{{ $show_holidays }}"
     data-show_birthdays="{{ $show_birthdays }}"
     data-profile-url = "{{ $profile_url }}">
    <div class="portlet-title">
        <div class="caption font-grey-cascade uppercase">{{ $block_title }}</div>
        <div class="tools">
            <a class="collapse" href="javascript:;"> </a>                                       
        </div>
    </div>
    <div class="portlet-body">
        <div class="dx-widget-calendar"></div>
    </div>
</div>