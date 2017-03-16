@if(count($employee->subordinates) > 0)
<div class="mt-element-list">
    <div class="mt-list-head list-simple font-white bg-green-jungle">
        <div class="list-head-title-container">
            <h4>
                {{ trans('empl_profile.direct_reporters') }}
                <span class="badge badge-primary">{{ count($employee->subordinates) }}</span>
            </h4>             
        </div>
    </div>
    <div class="mt-list-container list-simple dx-tile-direct-reporters" style="max-height: 250px; overflow-y: scroll">
        <ul>
            @foreach($employee->subordinates as $directReporter)
            <li class="mt-list-item">
                <div class="list-icon-container">
                    <i class="fa fa-user"></i>
                </div>
                <div class="list-item-content" style="padding-right: 0px; padding-left: 30px;">                    
                    <a href="{{Request::root()}}/employee/profile/{{ $directReporter->id }}">{{ $directReporter->display_name }}</a>                    
                </div>
            </li>
            @endforeach
        </ul>
    </div>
</div>   
@endif