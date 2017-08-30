@if (count($orgs) > 1)
    <div class='input-group pull-right col-md-5'>   
        <select class='form-control dx-orgs-cbo'>
            @foreach($orgs as $org)
                <option 
                    @if ($org->id == $current_org_id)
                        selected
                    @endif                                                

                    value="{{ $org->id }}">{{ $org->title }}</option>
            @endforeach
        </select>       
    </div>
@endif