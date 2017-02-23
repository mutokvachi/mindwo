<table border="0" width="100%" style="margin-bottom: -15px;" class="ie9-fix-placeholders">
    <tr>
        <td width="49%">{{ trans('empl_birth.lbl_dep') }}:</td>
        <td width="2%"></td>
        <td width="49%">{{ trans('empl_birth.lbl_unit') }}:</td>
    </tr>
</table>

<div class="input-group">                    

    <select class="form-control" name="source_id" style='width: 49%; margin-right: 2%;'>
        <option value="0" {{ ($source_id == 0) ? 'selected' : '' }}>{{ trans('empl_birth.lbl_all_dep') }}</option>
        <option disabled>──────────</option>
        @foreach($sources as $source)
            <option value='{{ $source->id }}' {{ ($source_id == $source->id) ? 'selected' : '' }}>{{ $source->title }}</option>
        @endforeach
    </select>

    <div class="input-group" style='width: 49%; margin-left: 2%; margin-top: 0px; margin-bottom: 0px;'>
        <input type="text" class="form-control" placeholder="{{ trans('empl_birth.lbl_unit') }}" name='department' value='{{ $department }}'>
        <span class="input-group-btn">
            <button class="btn btn-white dx-tree-value-clear-btn" type="button" style='margin-right: 2px;'><i class='fa fa-trash-o'></i></button>
        </span>
        <span class="input-group-btn">
            <button class="btn btn-white dx-tree-value-choose-btn" type="button"><i class='fa fa-plus'></i></button>
        </span>
    </div>

</div>

<table border="0" width="100%" style="margin-bottom: -15px;" class="ie9-fix-placeholders">
    <tr>
        <td width="49%">{{ trans('empl_birth.lbl_birth') }}:</td>
        <td width="2%"></td>
        <td width="49%"></td>
    </tr>
</table>

<div class="input-group" id="defaultrange" style='width: 49%; min-width: 240px;'>
    <input type="text" class="form-control" readonly style='background-color: white; cursor: pointer;' placeholder="{{ trans('empl_birth.lbl_birth') }}">
    <span class="input-group-btn">
        <button class="btn default date-range-toggle" type="button">
            <i class="fa fa-calendar"></i>
        </button>
    </span>
</div>    

<input type='hidden' name='param' value='OBJ=EMPLBIRTH' />
<input type='hidden' name='date_from' value='{{ $date_from }}' />
<input type='hidden' name='date_to' value='{{ $date_to }}' />

<input type="hidden" name="cabinet" value="" />
<input type="hidden" name="office_address" value="" />
<input type="hidden" name="searchType" value="{{ trans('search_top.employees') }}" />
<input type="hidden" name="manager_id" value="0" />
<input type="hidden" name="subst_empl_id" value="0" />
<input type="hidden" name="position" value="" />

<input type="hidden" name="is_from_link" value="0" />