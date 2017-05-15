<div class='fileinput fileinput-{{ $class_exist }} input-group' data-provides='fileinput' style="width: 100%;" dx_file_field_id="{{ $field_id }}" data-is-init="0">
    <div class='form-control'>
            @if (!(isset($is_pdf) && $is_pdf))
            <i class='glyphicon glyphicon-file fileinput-exists'></i> 
            @endif
            <span class='fileinput-filename truncate' style="max-width: 300px;">
                @if (isset($is_pdf) && $is_pdf && !$is_crypted)
                    <a href='{{Request::root()}}/web/viewer.html?file={{Request::root()}}/download_file_{{ $item_id }}_{{ $list_id }}_{{ $field_id }}.pdf' target="_blank">{{ $item_value }}</a>
                @else
                    @if ($item_value)
                        <a href='{{Request::root()}}/download_file_{{ $item_id }}_{{ $list_id }}_{{ $field_id }}' class='{{ ($is_crypted) ? "dx-crypto-field-file" : "" }}' data-masterkey-group="{{ $masterkey_group_id }}">{{ $item_value }}</a>
                    @endif
                @endif
            </span>
    </div>
    @if (!$is_disabled)
        <span class='input-group-addon btn btn-default btn-file'>
                <span class='fileinput-new'>{{ trans('fields.btn_set') }}</span>
                <span class='fileinput-exists'>{{ trans('fields.btn_change') }}</span>
                <input type='file' name='{{ $item_field }}' {{ ($is_required && !$item_value) ? 'required' : '' }} class='{{ ($is_crypted) ? "dx-crypto-field-file" : "" }}' data-masterkey-group="{{ $masterkey_group_id }}" data-required="{{ $is_required }}"/>
                <input class='fileinput-remove-mark' type='hidden' value='0' name = '{{ $item_field_remove }}' />
        </span>
        <a href='javascript:;' class='input-group-addon btn btn-default fileinput-exists' data-dismiss='fileinput'>{{ trans('fields.btn_remove') }}</a>
        <input type="hidden" name='{{ $item_field }}_is_set' value="{{ ($item_value) ? 1: '' }}" {{ ($is_required) ? 'required' : '' }} />
    @else
        @if ($down_guid && Config::get('dx.is_files_editor', false) && !$is_crypted)
            @if ($is_item_editable)
            <a href='mindwo://?url={{Request::root()}}/download_by_guid_{{ $down_guid }}?file_name={{ $item_value }}?field_name={{ $item_field }}?mode=write?download_guid={{ $down_guid }}' class='input-group-addon btn btn-default fileinput-exists'>{{ trans('fields.btn_file_edit') }}</a>
            @endif
            <a href='mindwo://?url={{Request::root()}}/download_by_guid_{{ $down_guid }}?file_name={{ $item_value }}?field_name={{ $item_field }}?mode=readonly?download_guid={{ $down_guid }}' class='input-group-addon btn btn-default fileinput-exists'>{{ trans('fields.btn_file_view') }}</a>
        @endif    
    @endif
</div>
@if (!$is_disabled)
<script>
    window.document.onload = function() {
        $(".fileinput[data-is-init=0]").each(function() {
            $(this).on('clear.bs.fileinput', function() {
                $(this).find('input[data-required=1]').prop('required', true);
            });

            $(this).on('change.bs.fileinput', function() {
                $(this).find('input[data-required=1]').prop('required', false);
                $(this).closest(".form-group").removeClass('has-error').find('.with-errors').html("");
            });

            $(this).attr('data-is-init', 1);
        });
    }
</script>
@endif