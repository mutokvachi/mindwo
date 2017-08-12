<div class="dx-stick-footer animated bounceInUp">
    <div class='col-lg-2 col-md-3 hidden-sm hidden-xs dx-left dx-stick-footer-title'>
      <div class="row">
        <div class="col-md-12">
          <i class="fa fa-sitemap"></i>
          <span>{{ trans('constructor.title') }}</span>
        </div>
      </div>
    </div>
    <div class='col-lg-10 col-md-9 col-sm-12 col-xs-12 dx-right'>
      <div class="row">
        <div class="col-md-12" style="text-align: center">
          @if($step != 'names')
            <button id="prev_step" type="button" class="btn btn-primary dx-wizard-btn pull-left">
              <i class="fa fa-arrow-left"></i> {{ trans('constructor.back') }}
            </button>
          @endif
          @includeIf('constructor.'.$step.'_buttons')
          <button id="submit_step" type="button" class="btn {{ ($step == 'workflows') ? 'btn-white' : 'btn-primary'}} dx-wizard-btn pull-right">
            @if($step == 'workflows')
              {{ trans('constructor.view_list') }} <i class="fa fa-list"></i>
            @else
              {{ trans('constructor.next') }} <i class="fa fa-arrow-right"></i>
            @endif
          </button>
        </div>
      </div>
    </div>
</div>