<div class="dx-edu-course-tab-container">    
    @if($subject->coordinator)
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
    @endif
</div>