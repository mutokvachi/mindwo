<div>
    <input {{ ($is_disabled) ? 'disabled' : '' }} type="checkbox" class="dx-bool" {{ $sel_yes }} data-off-text="{{ trans('fields.no') }}" data-on-text="{{ trans('fields.yes') }}" name='{{ $item_field }}' />
</div>