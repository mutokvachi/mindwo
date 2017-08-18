<div class="dx-stick-footer animated bounceInUp">
    <div class='row'>
      <div class='col-lg-2 col-md-3 hidden-sm hidden-xs dx-left dx-menu-builder-stick-title'>
          <i class="fa fa-calendar"></i>
          <span>{{ trans('calendar.scheduler.page_title') }}</span>
      </div>
      <div class='col-lg-10 col-md-9 col-sm-12 col-xs-12 dx-right'>
        <a href="javascript:;" class="btn btn-default dx-new-btn" title="{{ trans('calendar.scheduler.tooltip_new_subect') }}">
          {{ trans('calendar.scheduler.new_subj_btn') }} 
        </a>
        <a href="javascript:;" class="btn btn-default dx-new-group-btn">
          {{ trans('calendar.scheduler.new_group_btn') }} 
        </a>
        <a href="javascript:;" class="btn btn-default dx-complect-btn">
          <i class="fa fa-users"></i> {{ trans('calendar.scheduler.complect_btn') }}
        </a>
        <div class="btn-group dropup">
          <button type="button" class="btn btn-primary dx-publish-default"><i class="fa fa-globe"></i> {{ trans('calendar.scheduler.publish_btn') }}</button>
          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hover="dropdown">
            <span class="caret"></span>
            <span class="sr-only">{{ trans('calendar.scheduler.menu_lbl') }}</span>
          </button>
          <ul class="dropdown-menu">
              <li><a href="javascript:;" class="dx-publish-marked"><i class="fa fa-check-square-o"></i> {{ trans('calendar.scheduler.menu_publish_marked') }}</a></li>
              <li><a href="javascript:;" class="dx-publish-all"><i class="fa fa-square-o"></i> {{ trans('calendar.scheduler.menu_publish_all') }}</a></li> 
          </ul>
        </div>
          
        
      </div>
    </div>
</div>