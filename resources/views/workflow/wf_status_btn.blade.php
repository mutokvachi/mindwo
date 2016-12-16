<div class="btn-group pull-right dx-wf-menu-group">
    <button type="button" class="btn dx-wf-menu-btn
        @if ($workflow_btn == 2)
            blue-hoki
        @endif

        @if ($workflow_btn == 3)
            red-soft
        @endif

        @if ($workflow_btn == 4)
            green-meadow
        @endif

        btn-sm btn-outline hover-initialized" data-toggle="dropdown" aria-expanded="true"

        style="border: 1px solid {{ ($workflow_btn == 2) ? '#67809F' : (($workflow_btn == 3) ? '#E43A45' : '#1BBC9B') }}!important;"> 
            <span class="dx-wf-menu-btn-title">
            @if ($workflow_btn == 2)
                {{ trans('task_form.doc_in_process') }}
            @endif

            @if ($workflow_btn == 3)
                {{ trans('task_form.doc_rejected') }}
            @endif

            @if ($workflow_btn == 4)
                {{ trans('task_form.doc_approved') }}
            @endif
            </span>
                <i class="fa fa-angle-down"></i>
    </button>

    <ul class="dropdown-menu pull-right" role="menu">                                                              
        <li>
            <a href="javascript:;" class="dx-menu-task-history">
                <i class="fa fa-tasks"></i> {{ trans('task_form.menu_task_history') }}</a>
        </li>
        @if ($workflow_btn == 2 && $is_wf_cancelable)
            <li class="divider dx-wf-divider-cancel"> </li>
            <li>
                <a href="javascript:;" class='dx-menu-cancel-workflow'>
                    <i class="fa fa-undo"></i> {{ trans('task_form.menu_cancel_wf') }}</a>
            </li>
        @endif
    </ul>   
</div>