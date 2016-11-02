<table border="0" width="100%" style="margin-bottom: -15px;" class="ie9-fix-placeholders">
    <tr>
        <td width="100%">{{ trans('documents.lbl_source') }}</td>
    </tr>
</table>

<div class="input-group" style='width: 100%;'>
        <select class="form-control" name="source_id">
            <option value="0" {{ ($source_id == 0) ? 'selected' : '' }}>{{ trans('documents.lbl_all_sources') }}</option>
            <option disabled>──────────</option>
            @foreach($sources as $source)
                <option value='{{ $source->id }}' {{ ($source_id == $source->id) ? 'selected' : '' }}>{{ $source->title }}</option>
            @endforeach
        </select>
</div>

<table border="0" width="100%" style="margin-bottom: -15px;" class="ie9-fix-placeholders">
    <tr>
        <td width="50%"><div style="margin-left: 45px;">{{ trans('documents.lbl_reg_date') }}:</div></td>
        <td width="50%"><div style="margin-left: 15px;">{{ trans('documents.lbl_doc_kind') }}:</div></td>
    </tr>
</table>

<div class="row" style='margin-top: -10px;'>
    <div class="col-lg-6">
       <div class="input-group" id="defaultrange" style='min-width: 240px;'>
            <input type="text" class="form-control" readonly style='background-color: white; cursor: pointer;' placeholder="{{ trans('documents.lbl_reg_date') }}">
            <span class="input-group-btn">
                <button class="btn default date-range-toggle" type="button">
                    <i class="fa fa-calendar"></i>
                </button>
            </span>
        </div>                     
    </div>
    <div class="col-lg-6">
        <div class="input-group" style="width: 100%;">                    
            <select class="form-control" name="kind_id">
                <option value="0" {{ ($kind_id == 0) ? 'selected' : '' }}>{{ trans('documents.lbl_all_kinds') }}</option>
                <option disabled>──────────</option>
                @foreach($kinds as $kind)
                    <option value='{{ $kind->id }}' {{ ($kind_id == $kind->id) ? 'selected' : '' }}>{{ $kind->title }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<input type="hidden" name="searchType" value="{{ trans('search_top.documents') }}" />
<input type="hidden" name="pick_date_from" value="{{ $date_from }}" />
<input type="hidden" name="pick_date_to" value="{{ $date_to }}" />