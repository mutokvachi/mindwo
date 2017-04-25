<div class='dx-view-edit-form' data-meeting-id = '{{ $meeting_row->id }}'>
    <div class='row' style="margin-left: 5px; margin-right: 5px;">
        <div class="form-group col-lg-12">
            <label for="view_title">                        
                <span class="dx-fld-title">{{ trans('grid.lbl_view_title') }}</span>
            </label>
            <div class='pull-right' style='position: absolute; right: 0px; top: -2px; padding-right: 20px;'>
                <span style="display: {{ ($is_my_view) ? 'none' : 'inline'}}"><input style="display: {{ ($is_default) ? 'none' : 'inline'}}" type='checkbox' name='is_default' {{ ($is_default) ? 'checked' : '' }}> {{ trans('grid.ch_is_default') }}</span>
                <span style="display: {{ ($is_default) ? 'none' : 'inline'}}"><input type='checkbox' name='is_my_view' {{ ($is_my_view) ? 'checked' : '' }}> {{ trans('grid.ch_is_for_me') }}</span>
            </div>            
            <input class="form-control" autocomplete="off" type="text" name="view_title" maxlength="100" value="{{ $view_title }}">
        </div>
    </div>
    <div class='row' style="margin-left: 5px; margin-right: 5px;">
        <div class='col-lg-6'>
            <div class="portlet box grey-salsa">
                <div class="portlet-title">
                    <div class="caption">{{ trans('grid.lbl_available') }}</div>
                    <div class="actions">
                        <input class="dx-search" type='text' value="" placeholder="{{ trans('grid.lbl_search') }}" />
                    </div>
                </div>
                <div class="portlet-body dx-fields-body">
                    <div class="dx-fields-container">
                        <div class="dd dx-cms-nested-list dx-available">
                            <ol class="dd-list">
                                @foreach($list_fields as $item)
                                    @include('blocks.view.view_field_item', ['field' => $item])
                                @endforeach        
                            </ol>
                        </div>
                    </div>
                </div>
            </div>        
        </div>
        <div class='col-lg-6'>
         <div class="portlet box green-haze">
            <div class="portlet-title">
                <div class="caption">{{ trans('grid.lbl_used') }}</div> 
                <div class="actions">
                    <input class="dx-search" type='text' value="" placeholder="{{ trans('grid.lbl_search') }}" />
                </div>
            </div>
            <div class="portlet-body dx-fields-body">
                <div class="dx-fields-container">
                    <div class="dd dx-cms-nested-list dx-used">
                        <ol class="dd-list">
                            @foreach($view_fields as $item)
                                @include('blocks.view.view_field_item', ['field' => $item])
                            @endforeach        
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>