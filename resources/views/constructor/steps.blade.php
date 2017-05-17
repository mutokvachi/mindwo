<div class="mt-element-step">
  <div class="row step-line">
    <div class="dx-step-naming col-md-3 mt-step-col first {{ $step == 'names' ? 'active' : 'done' }}">
      <div class="mt-step-number bg-white">1</div>
      <div class="mt-step-title uppercase font-grey-cascade">Naming</div>
      <div class="mt-step-content font-grey-cascade">Register & item titles</div>
    </div>
    <div class="dx-step-fields col-md-3 mt-step-col
      {{ $step == 'fields' ? 'active' : '' }}
      {{ in_array($step, ['rights', 'menu']) ? 'done' : '' }}">
      <div class="mt-step-number bg-white">2</div>
      <div class="mt-step-title uppercase font-grey-cascade">Fields</div>
      <div class="mt-step-content font-grey-cascade">View & form fields</div>
    </div>
    <div class="dx-step-roles col-md-3 mt-step-col
      {{ $step == 'rights' ? 'active' : '' }}
      {{ $step == 'menu' ? 'done' : '' }}">
      <div class="mt-step-number bg-white">3</div>
      <div class="mt-step-title uppercase font-grey-cascade">Rights</div>
      <div class="mt-step-content font-grey-cascade">Assign users roles</div>
    </div>
    <div class="dx-step-navigation col-md-3 mt-step-col last {{ $step == 'menu' ? 'active' : '' }}">
      <div class="mt-step-number bg-white">4</div>
      <div class="mt-step-title uppercase font-grey-cascade">Navigation</div>
      <div class="mt-step-content font-grey-cascade">Setup link in menu</div>
    </div>
  </div>
</div>
