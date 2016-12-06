@if (count($self->tasks_rows))
    <div class="mt-actions">
    @foreach($self->tasks_rows as $task)
        @include('blocks.taskslist.taskrow', ['task' => $task])
    @endforeach
    </div>
@endif

