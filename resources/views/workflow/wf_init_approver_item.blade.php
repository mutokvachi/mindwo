<li class="dd-item" data-id="{{ $employee_id }}">
    <div class="dd-handle dd3-handle"> </div>
    <div class="dd3-content">
        <div class="row">
            <div class="col-md-8">
                <b>{{ $display_name }}</b>&nbsp;<small>{{ $position_title }}</small>
                @if ($subst_info)
                    &nbsp;<a href="#" style="cursor: help;"><i class="fa fa-exchange" title="{{ trans('workflow.wf_init_substit_title') }}:&nbsp;{{ $subst_info }}"></i></a>
                @endif
            </div>
            <div class="col-md-3" style="margin-top: -4px;">
                {{ trans('workflow.wf_init_due_label') }}: <input type="text" maxlength="2" name="due_days" value="{{ $due_days }}" style="width: 30px;"/>&nbsp; {{ trans('workflow.wf_init_due_days') }}
            </div>
            <div class="col-md-1">
                <a href="JavaScript:;" title="Noņemt" class="pull-right dx-cms-approver-remove" dx_is_init="0"><i class="fa fa-trash-o"></i></a>
            </div>
        </div>
    </div>
</li>
