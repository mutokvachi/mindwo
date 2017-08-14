<div class="dx-edu-course-tab-container">
    @foreach($subject->teachers as $teacher)
        <h4>Vārds, uzvārds</h4>
        <div>
            <div class="row">
                <div class="col-lg-11 col-md-10 col-sm-9 col-xs-12">
                    <p>
                        {{ $teacher->display_name }}
                    </p>
                    <p>
                        {{ $teacher->introduction }}
                    </p>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12">
                    <img style='width: 100%; max-width:200px' class="" 
                        alt=""  
                        src="{{Request::root()}}/{{ 1==0 ? 'img/' . $course->teacher->picture_guid : 'assets/global/avatars/default_avatar_big.jpg' }}" />
                </div>
            </div>
        </div>

        @if($teacher->experience)
        <h4>Darba pieredze</h4>
        <div>
            {!! $teacher->experience !!}
        </div>
        @endif

        @if($teacher->education)
        <h4>Izglītība</h4>
        <div>
            {!! $teacher->education !!}
        </div>
        @endif

        @if($teacher->additional_info)
        <h4>Papildu informācija</h4>
        <div>
            {!! $teacher->additional_info !!}
        </div>
        @endif
    @endforeach
</div>