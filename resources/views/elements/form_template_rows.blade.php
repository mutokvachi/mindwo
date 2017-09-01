@foreach($templates as $template)
    <div class='dx-templ-row' data-id="{{ $template->id }}">
        <div>
            <span class="dx-templ-title">{{ $template->title }}</span>
            @if ($template->file_guid)
                <a href="{{Request::root()}}/download_by_field_{{ $template->id }}_{{ $templ_list_id }}_file_name"><i class="fa fa-download"></i></a>
            @endif
        </div>

        @if ($template->description)
            <div>{{ $template->description}}</div>
        @endif

        <div style="margin-top: 10px;">
            <button class="btn btn-xs btn-default dx-templ-choose-btn">{{ trans('form.template.choose_btn') }}</button>        
        </div>
    </div>
@endforeach