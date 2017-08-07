<div class='input-group pull-right col-md-3'>
    <select class='form-control dx-rooms-cbo'>
        @foreach($rooms_cbo as $room)
            <option 

                @if ($room->id == $current_room_id)
                    selected
                @endif                                                

                value="{{ $room->id }}">{{ $room->title }}</option>
        @endforeach
    </select>
       
    <span class="input-group-btn">
        <button class='btn btn-white dx-room-edit-btn' type='button' style="border: 1px solid #c2cad8!important; margin-left: -2px!important;"><i class='fa fa-edit'></i></button>                                                    
    </span>
       
</div>