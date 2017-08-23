<div class="dx-edu-course-tab-container">
    @if(1==0)
    <h4>Ilgums</h4>
    <div>
        {!! $subject->duration !!}
    </div>
    @endif

    @if(1==0)
    <h4>Cena</h4>
    <div>
        {!! $subject->price !!}
    </div>
    @endif

    <h4>Grupas</h4>
    @foreach($subject->avaliableGroups as $group)
    <div class="panel panel-default" style='border: 1px solid #ddd;'>
        <div class="panel-heading">
            <h3 class="panel-title">{{ $group->title }}</h3>
        </div>
        @if(count($group->days) <= 0)
        <div class="panel-body">            
            <i>Grupai nav sastādīts grafiks</i>         
        </div>
         @endif
        @if(count($group->days) > 0)
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th> Datums </th>
                        <th> Laiks </th>
                        <th> Pasniedzēji </th>
                        <th> Adrese </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($group->days as $day)
                    <tr>
                        <td> {{ $day->lesson_date->format(config('dx.txt_date_format')) }} </td>
                        <td> {{ date('H:i', strtotime($day->time_from)) }} - {{ date('H:i', strtotime($day->time_to)) }} </td>
                        <td> 
                            @if(count($day->teachers) > 0)
                                <?php $counter = 0; ?>
                                @foreach($day->teachers as $teacher)
                                    @if($counter > 0), @endif
                                    <?php ++$counter; ?>
                                    {{ $teacher->display_name }}                                    
                                @endforeach
                            @else
                                Nav norādīti
                            @endif
                        </td>
                        <td> 
                            @if($day->room && $day->room->organization && $day->room->organization->address)                            
                                {{ $day->room->organization->address }}
                                @if($day->room->room_nr)
                                    , telpa {{ $day->room->room_nr }} 
                                @endif
                            @elseif($group->organization && $group->organization->address)
                                {{ $group->organization->address }}
                            @else
                                Nav norādīta
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>                           
        @endif
    </div>
    @endforeach
</div>