<div style="padding-left: 20px;">
<div class='col-lg-12'>
    <div class="row">
        <div class="col-lg-3">
            {{ trans('mindwo/pages::calendar.event_name') }}:
        </div>
        <div class="col-lg-9">
            <b>{{ $title }}</b>
        </div>
    </div>

    @if ($time_from)
    <div class="row" style='margin-bottom: 10px; margin-top: 10px;'>
        <div class="col-lg-3">
            @if ($time_from != $time_to)
                {{ trans('mindwo/pages::calendar.start_time') }}:
            @else
                {{ trans('mindwo/pages::calendar.date_time') }}:
            @endif
        </div>
        <div class="col-lg-9">
            {!! $time_from !!}
        </div>
    </div>
    @endif
    
    @if ($time_to)
        @if ($time_from != $time_to)
            <div class="row" style='margin-bottom: 10px; margin-top: 10px;'>
                <div class="col-lg-3">
                    {{ trans('mindwo/pages::calendar.end_time') }}:
                </div>
                <div class="col-lg-9">
                    {!! $time_to !!}
                </div>
            </div>
        @endif
    @endif
    
    @if ($address)
        
        <div class="row" style='margin-bottom: 10px; margin-top: 10px;'>
            <div class="col-lg-3">
                {{ trans('mindwo/pages::calendar.place') }}:
            </div>
            <div class="col-lg-9">
                {{ $address }}
            </div>
        </div>
        
    @endif
</div>
@if ($picture)
    <div class="col-lg-12">
        <img class="img-responsive img-thumbnail" src='{{Request::root()}}/img/{{ $picture }}' alt='{{ trans('mindwo/pages::calendar.form_title') }}' title ='{{ $title }}'/>
    </div>
@endif

@if ($description)
    <div class="col-lg-12" style='margin-top:20px;'>
        {!! $description !!}
    </div>
@endif
</div>