<div class="mt-element-step">
  <div class="row step-line">
    <div class="dx-step-naming col-md-3 mt-step-col first {{ $step == 'names' ? 'active' : 'done' }}">
      <div class="mt-step-number bg-white">1</div>
      <div class="mt-step-title uppercase font-grey-cascade">{{ trans('constructor.step_names') }}</div>
      <div class="mt-step-content font-grey-cascade">{{ trans('constructor.step_names_desc') }}</div>
    </div>
    <div class="dx-step-fields col-md-3 mt-step-col
      {{ $step == 'columns' ? 'active' : '' }} {{ in_array($step, ['fields', 'rights', 'menu']) ? 'done' : '' }}">
      <div class="mt-step-number bg-white">2</div>
      <div class="mt-step-title uppercase font-grey-cascade">{{ trans('constructor.step_columns') }}</div>
      <div class="mt-step-content font-grey-cascade">{{ trans('constructor.step_columns_desc') }}</div>
    </div>
    <div class="dx-step-fields col-md-3 mt-step-col
      {{ $step == 'fields' ? 'active' : '' }} {{ in_array($step, ['rights', 'menu']) ? 'done' : '' }}">
      <div class="mt-step-number bg-white">3</div>
      <div class="mt-step-title uppercase font-grey-cascade">{{ trans('constructor.step_fields') }}</div>
      <div class="mt-step-content font-grey-cascade">{{ trans('constructor.step_fields_desc') }}</div>
    </div>
    <div class="dx-step-roles col-md-3 mt-step-col last
      {{ $step == 'rights' ? 'active' : '' }} {{ $step == 'menu' ? 'done' : '' }}">
      <div class="mt-step-number bg-white">4</div>
      <div class="mt-step-title uppercase font-grey-cascade">{{ trans('constructor.step_rights') }}</div>
      <div class="mt-step-content font-grey-cascade">{{ trans('constructor.step_rights_desc') }}</div>
    </div>
  </div>
</div>
