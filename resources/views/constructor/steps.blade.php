<div class="mt-element-step">
  <div class="row step-line">
    @for($i = 0, $j = array_search($step, $steps); $i < count($steps); $i++)
      <div class="col-md-2 mt-step-col
        dx-step-{{ $steps[$i] }}
        {{ $i == 0 ? 'col-md-offset-1 first' : '' }}
        {{ $i == count($steps) - 1 ? 'last' : '' }}
        {{ $i < $j ? 'done' : '' }}
        {{ $step == $steps[$i] ? 'active' : '' }}
        {{ $list_id ? 'link' : '' }}"
        data-url="{{ $list_id ? "/constructor/register/{$list_id}".($i ? "/{$steps[$i]}" : '' ) : '' }}">
        <div class="mt-step-number bg-white">{{ $i + 1 }}</div>
        <div class="mt-step-title uppercase font-grey-cascade">{{ trans('constructor.step_' . $steps[$i]) }}</div>
        <div class="mt-step-content font-grey-cascade">{{ trans('constructor.step_' . $steps[$i] . '_desc') }}</div>
      </div>
    @endfor
  </div>
</div>
