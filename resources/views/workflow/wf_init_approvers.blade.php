<div class="dd dx-cms-nested-list">
    <ol class="dd-list">
        @foreach($approvers as $item)
            @include('workflow.wf_init_approver_item', [
                'employee_id' => $item["employee_id"],
                'display_name' => $item["display_name"],
                'position_title' => $item["position_title"],
                'subst_info' => $item["subst_info"],
                'due_days' => $item["due_days"]
            ])
        @endforeach        
    </ol>
</div>