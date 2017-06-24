<div class='form-group pull-right'>
    <label class='col-sm-2 control-label' style='margin-top: 8px;'>{{ trans('constructor.menu.lbl_site') }}:</label>
    <div class='col-sm-10' style='padding-right: 0; margin-right: -15px;'>
        <div class='input-group' style="width: 200px;">
            <select class='form-control dx-sites-cbo'>
                @foreach($sites_items as $item)
                    <option 

                        @if ($item->id == $site_id)
                            selected
                        @endif                                                

                        value="{{ $item->id }}">{{ $item->title }}</option>
                @endforeach
            </select>
            {{--
            <span class="input-group-btn">
                <button class='btn btn-white dx-site-edit-btn' type='button'><i class='fa fa-edit'></i></button>                                                    
            </span>
            --}}
        </div>
    </div>
</div>
