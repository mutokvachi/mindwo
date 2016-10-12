<div class='dx-department-tree-container' style="margin-left: 20px;">    
    @if ($node_count > 0)
        {!! $tree !!}
    @else
        <div class="alert alert-danger" role="alert" style='margin-top: 20px;'>Uzņēmumam nav norādīta neviena struktūrvienība.</div>
    @endif
</div>
