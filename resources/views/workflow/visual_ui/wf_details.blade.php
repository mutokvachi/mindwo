<input class="dx-cms-workflow-form-input-workflow_id" type="hidden" value="{{ $item_id }}"/> 
<input class="dx-cms-workflow-form-input-list_id" type="hidden" value="{{ $wf_register_id }}"/> 
<div class="form-group">
    <div class="dx-cms-workflow-form-label-list">{{ trans('workflow.list') }}</div>
    <input class="form-control dx-cms-workflow-form-input-list" type="text" maxlength="500" disabled value="{{ $wf_register_name }}"/>  
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-title">{{ trans('workflow.title') }}</div>
    <textarea class="form-control" id="dx-cms-workflow-form-input-title" name="title" rows="4" maxlength="255" {{ ($is_disabled) ? 'disabled' : '' }} >{{ ($workflow ? $workflow->title : '') }}</textarea>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-description">{{ trans('workflow.description') }}</div>
    <textarea class="form-control" id="dx-cms-workflow-form-input-description" name="title" rows="4" maxlength="255" {{ ($is_disabled) ? 'disabled' : '' }} >{{($workflow ? $workflow->description : '')}}</textarea>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-is_custom_approve">{{ trans('workflow.is_custom_approve') }}</div>
    <div>
        <input {{ ($is_disabled) ? 'disabled' : '' }} 
            type="checkbox" 
            class="dx-bool" 
            {{ ($workflow && $workflow->is_custom_approve ? 'checked' : '') }}
        data-off-text="{{ trans('fields.no') }}" 
        data-on-text="{{ trans('fields.yes') }}" 
        id='dx-cms-workflow-form-input-is_custom_approve'/>
    </div>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-valid_from">{{ trans('workflow.valid_from') }}</div>
    <div class='input-group'>
        <span class='input-group-btn'>
            <button type='button' class='btn btn-white dx-cms-workflow-form-input-valid_from-calc'><i class='fa fa-calendar'></i></button>
        </span>
        <input class='form-control dx-cms-workflow-form-input-valid_from' type='text' {{ ($is_disabled) ? 'disabled' : '' }} value="{{($workflow ? $workflow->valid_from : '') }}" />
    </div>
</div>
<div class="form-group">
    <div class="dx-cms-workflow-form-label-valid_to">{{ trans('workflow.valid_to') }}</div>
    <div class='input-group'>
        <span class='input-group-btn'>
            <button type='button' class='btn btn-white dx-cms-workflow-form-input-valid_to-calc'><i class='fa fa-calendar'></i></button>
        </span>
        <input class='form-control dx-cms-workflow-form-input-valid_to' type='text' {{ ($is_disabled) ? 'disabled' : '' }} value="{{($workflow ? $workflow->valid_to : '') }}" />
    </div>
</div>
