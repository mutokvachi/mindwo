<div class="dx-edu-course-tab-container">    
    @if($subject->coordinator)
        <div class="row">            
            <div class="col-lg-1 col-md-2 col-sm-3 col-xs-12" style='margin-bottom: 10px;'>
                <img style='width: 100%; max-width:200px;' class="" 
                    alt=""  
                    src="{{Request::root()}}/{{ $subject->coordinator->user->picture_guid ? 'img/' . $subject->coordinator->user->picture_guid : 'assets/global/avatars/default_avatar_big.jpg' }}" />
            </div>
            <div class="col-lg-11 col-md-10 col-sm-9 col-xs-12">
                <div style="margin-bottom:10px;">
                    <h4>{{ $subject->coordinator->user->display_name }}</h4>
                    @if($subject->coordinator->job_title)
                    <small>{{ $subject->coordinator->job_title }}</small>
                    @endif
                </div>
                @if($subject->coordinator->phone)
                    <div>
                        <b>Tālrunis: </b>{{ $subject->coordinator->phone }}
                    </div>
                @endif
                @if($subject->coordinator->mobile)
                    <div>
                        <b>Mobilais tālrunis: </b> {{ $subject->coordinator->mobile }}
                    </div>
                @endif
                @if($subject->coordinator->email)
                    <div>
                        <b>E-pasts: </b><a href="{{ $subject->coordinator->email }}">{{ $subject->coordinator->email }}</a>
                    </div>
                @endif
                @if($subject->coordinator->organization && $subject->coordinator->organization->address)
                <div>
                    <b>Adrese: </b>{{ $subject->coordinator->organization->address }}
                </div>
                @endif
            </div>
        </div>
    @else
        Kursam nav norādīts koordinators    
    @endif
</div>