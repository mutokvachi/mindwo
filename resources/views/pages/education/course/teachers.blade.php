<div class="dx-edu-course-tab-container">
    @if($subject->teachers->count() > 0)
    @foreach($subject->teachers as $teacher)
    <div style="border-bottom: 1px solid #ddd; padding-bottom:20px; margin-bottom:20px;">        
        <div class="row">            
            <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12" style='margin-bottom: 10px;'>
                <img style='width: 100%; max-width:200px;' class="" 
                    alt=""  
                    src="{{Request::root()}}/{{ $teacher->picture_guid ? 'img/' . $teacher->picture_guid : 'assets/global/avatars/default_avatar_big.jpg' }}" />
            </div>
            <div class="col-lg-11 col-md-10 col-sm-9 col-xs-12">
                <h4>{{ $teacher->display_name }}</h4>
                <p>
                    {!! $teacher->introduction !!}
                </p>
            </div>
        </div>    
        <div class="row">          
            <div class="col-lg-11 col-md-10 col-sm-9 col-xs-12">            

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
    @else
    Kursam nav norādīts neviens pasniedzējs
    @endif
</div>