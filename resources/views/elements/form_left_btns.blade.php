@if ($is_edit_rights && $form_is_edit_mode == 0 && $is_editable_wf == 1)
    <button  type='button' class='btn btn-primary dx-form-btn-edit'><i class="fa fa-pencil-square-o"></i> {{ trans('form.btn_edit') }}</button>
@endif

@if ($is_delete_rights && $item_id > 0 && $is_editable_wf == 1 && $form_is_edit_mode == 0)
    <button  type='button' class='btn btn-white dx-form-btn-delete'><i class="fa fa-trash-o"></i> {{ trans('form.btn_delete') }}</button>
@endif

@if ($is_edit_rights && $is_word_generation_btn && $is_editable_wf == 1 && $form_is_edit_mode == 0)
    <button  type='button' class='btn btn-white dx-form-btn-word' title='{{ trans('form.template.generate_hint') }}' 
        data-item-id='{{ $item_id }}'
        data-list-id='{{ $list_id }}'
        data-grid-htm-id='{{ $grid_htm_id }}'
        data-form-htm-id='{{ $frm_uniq_id }}'
    >
        <i class="fa fa-file-text-o"></i> {{ trans('form.template.doc_generate_btn') }}
    </button>
@endif

@if (Config::get('dx.is_tasks_logic', true) && ($workflow_btn == 1 || $workflow_btn == 3) && $form_is_edit_mode == 0 && $is_editable_wf == 1 && $is_edit_rights)    
    <button  type='button' class='btn btn-white dx-init-wf-btn'><font color="green"><i class="fa fa-play"></i></font> {{ trans('form.btn_start_workflow') }}</button>
@endif
@if ($form_is_edit_mode == 0 && Config::get('dx.is_tasks_logic', true))
    <button  type='button' class='btn btn-white dx-for-info-btn' title="{{ trans('form.btn_info_hint') }}">{{ trans('form.btn_info') }}

        &nbsp;<span class="badge badge-info dx-cms-info-task-count"
                    @if (count($info_tasks) == 0)
                        style="display: none;"
                    @endif
                    > {{ count($info_tasks) }} </span>

    </button>
@endif
<a href="{{Request::root()}}/web/viewer.html?file={{Request::root()}}/get_form_pdf_{{ $item_id }}_{{ $list_id }}.pdf" target="_blank" style="color: #333!important; text-decoration: none!important;"><button class='btn btn-white'><i class="fa fa-file-pdf-o"></i> {{ trans('form.btn_pdf') }}</button></a>
@include('forms.chat.button', ['item_id' => $item_id, 'list_id' => $list_id, 'form_title' => $form_title])