<div class="dx-edu-course-tab-container">
    @foreach($subject->teachers as $teacher)
    <div style="border-bottom: 1px solid #ddd; padding-bottom:20px; margin-bottom:20px;">
        <h4>{{ $teacher->display_name }}</h4>
        <div class="row">            
            <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12 col-lg-push-11 col-md-push-10 col-sm-push-9" style='margin-bottom: 10px;'>
                <img style='width: 100%; max-width:200px;' class="" 
                    alt=""  
                    src="{{Request::root()}}/{{ $teacher->picture_guid ? 'img/' . $teacher->picture_guid : 'assets/global/avatars/default_avatar_big.jpg' }}" />
            </div>
            <div class="col-lg-11 col-md-10 col-sm-9 col-xs-12 col-lg-pull-1 col-md-pull-2 col-sm-pull-3">
                <p>
                    {!! $teacher->introduction !!}
                </p>

                @if($teacher->experience)
                <h5>Darba pieredze</h5>
                <div>
                    {!! $teacher->experience !!}
                </div>
                @endif

                @if($teacher->education)
                <h5>Izglītība</h5>
                <div>
                    {!! $teacher->education !!}
                </div>
                @endif

                @if($teacher->additional_info)
                <h5>Papildu informācija</h5>
                <div>
                    {!! $teacher->additional_info !!}
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>