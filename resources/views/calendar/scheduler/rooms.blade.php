<div class='input-group pull-right col-md-5'>   
    <select class='form-control dx-rooms-cbo'>
        <?php $cur_org = ""; ?>
        @foreach($rooms_cbo as $room)
            @if ($cur_org != $room->organization)
                @if ($cur_org != "")
                    </optgroup>
                @endif
                <optgroup label="{{ $room->organization }}">
                <?php $cur_org = $room->organization; ?>
            @endif
                
            <option 
                @if ($room->id == $current_room_id)
                    selected
                @endif                                                

                value="{{ $room->id }}">{{ $room->title }}</option>
        @endforeach
        @if ($cur_org != "")
            </optgroup>
        @endif
    </select>
       
    <span class="input-group-btn">
        <button class='btn btn-white dx-room-edit-btn' type='button' style="border: 1px solid #c2cad8!important; margin-left: -2px!important;" title="Atvērt telpas kartiņu"><i class='fa fa-edit'></i></button>
        <button class='btn btn-white dx-room-new-btn' type='button' style="border: 1px solid #c2cad8!important; margin-left: -2px!important;" title="Izveidot jaunu telpu"><i class='fa fa-file-o'></i></button>                                
    </span>
       
</div>