<?php
$emp_docs_list = App\Libraries\DBHelper::getListByTable('in_employees_personal_docs');

if ($emp_docs_list) {
    $emp_docs_list_id = $emp_docs_list->id;
    $emp_docs_fld_id = DB::table('dx_lists_fields')->select('id')
                    ->where('list_id', '=', $emp_docs_list->id)
                    ->where('db_name', '=', 'file_name')
                    ->first()->id;
} else {
    $emp_docs_list_id = 0;
    $emp_docs_fld_id = 0;
}

if ($user->doc_country_id) {
    $selected_country_id = $user->doc_country_id;
} elseif ($user->country_id) {
    $selected_country_id = $user->country_id;
} else {
    $selected_country_id = 0;
}
?>
<div id="dx-emp-pers-docs-panel" data-user-id="{{ $user->id }}" 
     data-date-format="{{ config('dx.txt_date_format') }}" 
     data-locale='{{ Lang::locale() }}'
     data-emp-docs-list-id='{{ $emp_docs_list_id }}'
     data-emp-docs-fld-id='{{ $emp_docs_fld_id }}'>
    <div class='row dx-emp-pers-docs-country-row'>
        <div class='col-lg-4 col-md-12'>
            <div>{{ trans('employee.personal_docs.country') }}</div>
            <select class='form-control dx-not-focus' id="dx-emp-pers-docs-country">
                @foreach (App\Models\Country::all() as $country)            
                <option value="{{$country->id}}" {{ $selected_country_id == $country->id ? 'selected' : '' }}>{{ $country->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div>
        <hr />
    </div>
    <div class="row visible-lg" style="margin-top: 10px;">
        <div class="col-lg-2 dx-emp-pers-docs-table-col">   
            {{ trans('employee.personal_docs.personal_doc_type') }}
        </div>
        <div class="col-lg-2 dx-emp-pers-docs-table-col">   
            {{ trans('employee.personal_docs.doc_nr') }}
        </div>
        <div class="col-lg-2 dx-emp-pers-docs-table-col">   
            {{ trans('employee.personal_docs.valid_to') }}
        </div>
        <div class="col-lg-2 dx-emp-pers-docs-table-col">   
            {{ trans('employee.personal_docs.publisher') }}
        </div>
        <div class="col-lg-3 dx-emp-pers-docs-table-col">   
            {{ trans('employee.personal_docs.file') }}
        </div>
    </div>
    <div id="dx-emp-pers-docs-table"></div>
    <div id='dx-emp-pers-docs-table-history'></div>
    <div id="dx-emp-pers-docs-new-row">
        <div class="dx-emp-pers-docs-row row">
            <input class="dx-emp-pers-docs-id-input" type="hidden" />
            <input class="dx-emp-pers-docs-type-input" type="hidden" />    
            <div class="col-lg-2 dx-emp-pers-docs-table-col">
                <div class='hidden-lg dx-emp-pers-docs-label-mobile'>{{ trans('employee.personal_docs.personal_doc_type') }}</div>
                <label class="dx-emp-pers-docs-type-label"></label>
            </div>
            <div class="col-lg-2 dx-emp-pers-docs-table-col"> 
                <div class="hidden-lg dx-emp-pers-docs-label-mobile">{{ trans('employee.personal_docs.doc_nr') }}</div>
                <input class="form-control dx-emp-pers-docs-docnr-input" type="text" maxlength="500" />  
            </div>
            <div class="col-lg-2 dx-emp-pers-docs-table-col">           
                <div class="hidden-lg dx-emp-pers-docs-label-mobile">{{ trans('employee.personal_docs.valid_to') }}</div>
                <div class='input-group'>
                    <span class='input-group-btn'>
                        <button type='button' class='btn btn-white dx-emp-pers-docs-validto-input-calc'><i class='fa fa-calendar'></i></button>
                    </span>
                    <input class='form-control dx-emp-pers-docs-validto-input' type='text' />
                </div>
            </div>
            <div class="col-lg-2 dx-emp-pers-docs-table-col">  
                <div class="hidden-lg dx-emp-pers-docs-label-mobile">{{ trans('employee.personal_docs.publisher') }}</div>
                <input class="form-control dx-emp-pers-docs-publisher-input" type="text" maxlength="500" />   
            </div>
            <div class="col-lg-4 dx-emp-pers-docs-table-col">
                <div class="col-lg-11 dx-emp-pers-docs-table-col1">
                    <div class="hidden-lg dx-emp-pers-docs-label-mobile">{{ trans('employee.personal_docs.file') }}</div>
                    <div class="dx-emp-pers-docs-file-input">
                        <div class='fileinput fileinput-new input-group' data-provides='fileinput' style="width: 100%;" dx_file_field_id="{{ $emp_docs_fld_id }}">
                            <div class='form-control'>
                                <span class='fileinput-filename truncate dx-emp-pers-docs-file-input-download'></span>
                            </div>  
                            <span class='input-group-addon btn btn-default btn-file dx-emp-pers-docs-file-input-set-btn'>
                                <span class='fileinput-new'><i class='fa fa-file'></i></span>
                                <span class='fileinput-exists'><i class='fa fa-file'></i></span>
                                <input type='file' name='file_guid' class="dx-emp-pers-docs-file-input-file" data-tooltip-title="{{ trans('fields.btn_set') }}" />
                                <input class='fileinput-remove-mark dx-emp-pers-docs-file-input-remove' type='hidden' value='0' name = 'file_guid_remove' />
                            </span>
                            <a href='#' class='input-group-addon btn btn-default fileinput-exists dx-emp-pers-docs-file-input-remove-btn' data-dismiss='fileinput' data-tooltip-title="{{ trans('fields.btn_remove') }}">
                                <i class='fa fa-times'></i>
                            </a>
                            <input class="dx-emp-pers-docs-file-input-isset" type="hidden" name='file_guid_is_set' value="0" />                        
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 dx-emp-pers-docs-table-col2">
                    <button class="btn btn-white dx-emp-pers-docs-clear-btn" data-tooltip-title="{{ trans('employee.personal_docs.clear_doc') }}">
                        <i class='fa fa-trash-o'></i>
                    </button>
                </div>
            </div>
            <div class='hidden-lg'>
                <hr />
            </div>
        </div>
    </div>
</div>