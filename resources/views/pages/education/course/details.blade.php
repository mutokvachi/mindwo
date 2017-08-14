<div class="dx-edu-course-tab-container">
    <h4>Mācību kursa programma</h4>
    <div>
        {{ $subject->module->program->title }}
    </div>

    <h4>Mācību kursa modulis</h4>
    <div>
        {{ $subject->module->title }}
    </div>

    @if($subject->purpose)
    <h4>Mācību kursa mērķis</h4>
    <div>
        {!! $subject->purpose !!}
    </div>
    @endif

    @if($subject->target_audience)
    <h4>Mērķauditorija</h4>
    <div>
        {!! $subject->target_audience !!}
    </div>
    @endif

    @if($subject->prerequisites)
    <h4>Priekšnosacījumi</h4>
    <div>
        {!! $subject->prerequisites !!}
    </div>
    @endif

    @if($subject->topics)
    <h4>Tēmas</h4>
    <div>
        {!! $subject->topics !!}
    </div>
    @endif

    @if($subject->benefits)
    <h4>Dalībnieku ieguvumi</h4>
    <div>
        {!! $subject->benefits !!}
    </div>
    @endif
</div>