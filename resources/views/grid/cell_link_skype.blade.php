@if ($cell_value)
    <a href="skype:{{ $cell_value }}?chat" title="{{ trans('fields.hint_skype') }}"><i class="fa fa-skype"></i> {{ $cell_value }}</a>
@endif