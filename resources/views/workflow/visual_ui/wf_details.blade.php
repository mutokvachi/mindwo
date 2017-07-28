<input class="dx-cms-workflow-form-input-workflow_id" type="hidden" value="{{ $item_id }}"/> 
<input class="dx-cms-workflow-form-input-list_id" type="hidden" value="{{ $wf_register_id }}"/>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-title">{{ trans('workflow.title') }}</div>
    <textarea class="form-control dx-cms-workflow-form-input-title" name="title" rows="4" maxlength="255" >{{ ($workflow ? $workflow->title : '') }}</textarea>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-description">{{ trans('workflow.description') }}</div>
    <textarea class="form-control dx-cms-workflow-form-input-description" name="title" rows="4">{{($workflow ? $workflow->description : '')}}</textarea>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-is_custom_approve">{{ trans('workflow.is_custom_approve') }}</div>
    <div style="height: 32px;">
        <input 
            type="checkbox" 
            class="dx-bool dx-cms-workflow-form-input-is_custom_approve" 
            {{ ($workflow && $workflow->is_custom_approve ? 'checked' : '') }}
            data-off-text="{{ trans('fields.no') }}" 
            data-on-text="{{ trans('fields.yes') }}" />
    </div>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-valid_from">{{ trans('workflow.valid_from') }}</div>
    <div class='input-group'>
        <span class='input-group-btn'>
            <button type='button' class='btn btn-white dx-cms-workflow-form-input-valid_from-calc'><i class='fa fa-calendar'></i></button>
        </span>
        <input class='form-control dx-cms-workflow-form-input-valid_from' type='text' value="{{($workflow && $workflow->valid_from ? (new DateTime($workflow->valid_from))->format(config('dx.txt_date_format')) : '') }}" />
    </div>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-valid_to">{{ trans('workflow.valid_to') }}</div>
    <div class='input-group'>
        <span class='input-group-btn'>
            <button type='button' class='btn btn-white dx-cms-workflow-form-input-valid_to-calc'><i class='fa fa-calendar'></i></button>
        </span>
        <input class='form-control dx-cms-workflow-form-input-valid_to' type='text' value="{{($workflow && $workflow->valid_to ? (new DateTime($workflow->valid_to))->format(config('dx.txt_date_format')) : '') }}" />
    </div>
</div>
