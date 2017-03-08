<table border="0" width="100%" style="margin-bottom: -15px;" class="ie9-fix-placeholders">
    <tr>
        <td width="49%">{{ trans('article.lbl_pub_date') }}:</td>
        <td width="2%"></td>
        <td width="49%"></td>
    </tr>
</table>

<div class="input-group" id="defaultrange" style='width: 49%; min-width: 240px;'>
    <input type="text" class="form-control" readonly style='background-color: white; cursor: pointer;' placeholder="{{ trans('article.lbl_pub_date') }}">
    <span class="input-group-btn">
        <button class="btn default date-range-toggle" type="button">
            <i class="fa fa-calendar"></i>
        </button>
    </span>
</div> 
   
<input type="hidden" name='type' value='{{ $type_id }}' id="search_type" />
<input type="hidden" name="searchType" value="{{ trans('search_top.news') }}" />

<input type="hidden" name="pick_date_from" value="{{ $date_from }}" />
<input type="hidden" name="pick_date_to" value="{{ $date_to }}" />