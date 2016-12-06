<div class="portlet light tasks-widget bordered">
    <div class="portlet-title">
        <div class="caption">
            <i class="icon-share font-dark hide"></i>
            <span class="caption-subject font-dark bold uppercase">{{ trans('task_widget.widget_title') }}</span>
            <span class="caption-helper dx-task-view-title" style='text-transform: lowercase;'>{{ $self->current_view['title'] }}</span>
            <span class="badge badge-info dx-task-count"> {{ count($self->tasks_rows) }}</span>
        </div>
        <div class="actions">
            <div class="btn-group">
                <a class="btn blue-oleo btn-circle btn-sm" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false"> {{ trans('task_widget.lbl_filter') }}
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    @foreach($self->views_rows as $view)
                        @if ($view['code'] == 'DIVIDER')
                            <li class="divider"> </li>
                        @else
                            <li>
                                <a href="javascript:;" data-code='{{ $view['code'] }}' class='dx-tasks-filter'> {{ $view['title'] }} </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <div class="portlet-body">
        <div class="task-content">
            @include('blocks.taskslist.tasks')
        </div>
        <div class="task-footer">
            <div class="btn-arrow-link pull-right">
                <a href="javascript:;">See All Records</a>
                <i class="icon-arrow-right"></i>
            </div>
        </div>
    </div>
</div>