<h3 class="page-title">Personāla izmaiņas</h3>

@if ($is_search)
<div class="well dx-empl-changes-search">
    <form action='{{ Request::root() }}/{{ Request::path() }}' method='POST' class='search-tools-form'>
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label>Meklēšanas frāze</label>
                    <input type="text" class="form-control" name='criteria' value='{{ ($criteria) ? $criteria : "" }}' autofocus>
                </div>
            </div>

            <div class="col-lg-6">
                
                <div class="form-group">
                    <label>Izmaiņu datums</label>
                               <div class="input-group" id="defaultrange" style='min-width: 240px;'>

                                   <input type="text" class="form-control" readonly style='background-color: white; cursor: pointer;'>
                                   <span class="input-group-btn">
                                       <button class="btn default date-range-toggle" type="button">
                                           <i class="fa fa-calendar"></i>
                                       </button>
                                   </span>
                               </div> 
                </div>
            </div>
        </div>
        
        <div class="row" style="margin-bottom: 10px;">
            <div class="col-lg-6">
                <label>Uzņēmums</label>
                <div class="input-group" style="width: 100%;">                    

                    <select class="form-control" name="source_id" style='width: 100%;'>
                        <option value="0" {{ ($source_id == 0) ? 'selected' : '' }}>Visi uzņēmumi</option>
                        <option disabled>──────────</option>
                        @foreach($sources as $source)
                            <option value='{{ $source->id }}' {{ ($source_id == $source->id) ? 'selected' : '' }}>{{ $source->title }}</option>
                        @endforeach
                    </select>

                </div>  
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 col-md-7 col-sm-7 col-xs-12">
                
                    <label> <input type="checkbox" class="empl-checks dx-grid-input-check" name='ch_new' value='1' {{ ($is_new) ? 'checked' : '' }}> Jaunie&nbsp;&nbsp;</label>
                    <label> <input type="checkbox" class="empl-checks dx-grid-input-check" name='ch_change' value='1' {{ ($is_change) ? 'checked' : '' }}> Izmaiņas&nbsp;&nbsp;</label>
                    <label> <input type="checkbox" class="empl-checks dx-grid-input-check" name='ch_leave' value='1' {{ ($is_leave) ? 'checked' : '' }}> Atbrīvotie</label>
                
            </div>

            <div class="col-lg-6 col-md-5 col-sm-5 col-xs-12">                
                <button class="btn blue-soft search-simple-btn search-simple-bottom pull-right" type="submit">Meklēt</button>
                <a class="dx-clear-link pull-right" style="margin-right: 10px; margin-top: 5px; display: none;" title='Notīrīt meklēšanas kritērijus'><i class="fa fa-eraser"></i> Notīrīt kritērijus</a>
           </div>
        </div>
        
        <input type='hidden' name='date_from' value='{{ $date_from }}' />
        <input type='hidden' name='date_to' value='{{ $date_to }}' />

        {!! csrf_field() !!}
    </form>
</div>
@endif

@if (count($changes_items))

@if ($criteria || $date_from || $date_to)
<div class="alert alert-success" role="alert">Atrasto ierakstu skaits: <b>{{ $rows_count }}</b></div>
@endif

<div class="portlet" style='background-color: white; padding: 10px;'>
    <div class="portlet-body" id="feed_area_{{ $block_guid }}">
        <div class="dx_empl_change_row_area">
            <div class="table-responsive">
                <table border="0" width="100%" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Darbinieks</th>
                            <th></th>
                            <th>Šobrīd</th>
                            <th>Vēsture</th>
                            <th style="text-align: center;">Spēkā no</th>
                        </tr>
                    <thead>
                    <tbody>
                        @foreach($changes_items as $item)
                        <tr>
                            <td>
                                <img alt="{{ $item->employee_name }}" class="m-b dx_empl_change_pic" src="{{Request::root()}}/img/{{ ($item->picture_guid) ? 'avatar/120x160/' . $item->picture_guid . '.jpg' : $avatar }}">
                            </td>
                            <td>
                                <span class="bold">{{ $item->employee_name }}</span><br>
                                <a href="mailto:{{ $item->email}}">{{ strtolower($item->email) }}</a>
                            </td>
                            <td>
                                <span class="dx_empl_change_label">Jaunais amats:</span><br>
                                    @if (($item->new_position) == null)
                                    -
                                    @else
                                <span class="bold"> {{ $item->new_position }} </span>
                                    @endif<br>
                                <span class="dx_empl_change_label">Jaunā struktūrvienība:</span><br>
                                @if (($item->new_department ) == null)
                                -
                                @else
                                    <span class="bold">{{ $item->new_source_title }}<br />{{ $item->new_department }} </span>
                                @endif
                            </td>
                            <td>
                                <span class="dx_empl_change_label">Iepriekšējais amats:</span><br>
                                @if (($item->old_position) == null)
                                -
                                @else
                                <span class="bold">{{ $item->old_position }}</span>
                                @endif
                                <br>
                                <span class="dx_empl_change_label">Iepriekšējā struktūrvienība:</span><br>
                                @if (($item->old_department ) == null)
                                -
                                @else
                                <span class="bold">{{ $item->old_source_title }}<br />{{ $item->old_department }}</span>
                                @endif
                            </td>
                            <td align="center">
                                {!! short_date($item->valid_from) !!}
                                @if (!$item->old_source_id || !$item->new_source_id)
                                <div class="text-center" style="margin-top: 10px;">
                                    @if (!$item->old_source_id)
                                    <span class="badge badge-success">Jauns</span>
                                    @endif

                                    @if (!$item->new_source_id)
                                    <span class="badge badge-default">Atbrīvots</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {!! $changes_items->appends(['criteria' => utf8_encode($criteria), 'date_from' => $date_from, 'date_to' => $date_to, 'ch_new' => $is_new, 'ch_change' => $is_change, 'ch_leave' => $is_leave, 'source_id' => $source_id])->render() !!}
        </div>
    </div>
</div>
@else
<div class="alert alert-info" role="alert">Nav atrasts neviens atbilstošs ieraksts.</div>
@endif
