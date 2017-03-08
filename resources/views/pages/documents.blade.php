@extends('frame')

@section('main_custom_css')
    <link href="{{Request::root()}}/{{ getIncludeVersion('css/pages/search_tools.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{Request::root()}}/{{ getIncludeVersion('metronic/global/plugins/bootstrap-daterangepicker/daterangepicker.min.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        .crit_label
        {
            color: silver;
        }
        
        #search_form_pick_date_to, #search_form_pick_date_from {
            width: 100%!important;
        }
        
        .documents-list .document {
                border-radius: 4px!important;
                -webkit-box-shadow: 0 1px 1px rgba(0,0,0,.15)!important;
                box-shadow: 0 1px 1px rgba(0,0,0,.15)!important;
        }

    </style>
    
    @include('pages.view_css_includes')
@stop        

@section('main_content')
    <h3 class="page-title">{{ trans('documents.page_title') }}
        <small>{{ trans('documents.page_subtitle') }}</small>
    </h3>

    <div class="dx-documents-page">        
                
        @include('search_tools.search_form', [
            'criteria_title' => trans("documents.criteria_title"),
            'fields_view' => 'search_tools.doc_fields',
            'form_url' => 'search'
        ])

        @if (count($docs))
            <div class='documents-list'>
                @foreach($docs as $item)
                <div class="panel document">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-2">
                                <a href="javascript:;" class="btn btn-block btn-primary dx-lotus-btn" list_id="{{ $item->list_id }}" item_id="{{ $item->item_id }}" title="{{ trans('documents.hint_open_document') }}">
                                    <strong>{{ $item->reg_nr }}</strong>
                                </a>
                            </div>
                            <div class="col-md-4">
                                <i class="fa fa-calendar-o"></i>&nbsp;<span>{{ short_date($item->reg_date) }}</span><br>                                
                                @if ($item->file_guid)
                                <a href="{{ Request::root() }}/download_by_field_{{ $item->item_id }}_{{ $item->list_id }}_{{ $item->file_field_name }}" title="{{ trans('documents.hint_file') }}"><i class="fa fa-file-o"></i> {{ $item->kind_title }}</a>
                                @else
                                    {{ $item->kind_title }}
                                @endif
                            </div>
                            <div class="col-md-6 text-muted">
                                    @if ($item->person_title)
                                        <i class="fa fa-info"></i> {{ $item->person_title }}
                                        <br />
                                    @endif
                                    {{ $item->description }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center">
                {!! $docs->appends(['criteria' => urlencode($criteria), 'source_id' => $source_id, 'kind_id' => $kind_id, 'pick_date_from' => $date_from, 'pick_date_to' => $date_to, 'searchType' => trans("search_top.documents")])->render() !!}
                <div style="color: silver; margin-top: 10px; margin-bottom: 20px;">{{ trans('documents.record_count') }} <b>{{ $total_count }}</b></span></div>
            </div>        
            
        @else
            <div class="alert alert-info" role="alert">{{ trans('search_top.nothing_found') }}</div>
        @endif
    </div>
@stop

@section('main_custom_javascripts')    
    <script src="{{Request::root()}}/{{ getIncludeVersion('metronic/global/plugins/moment.min.js') }}" type='text/javascript'></script>
    @include('pages.view_js_includes')
    
    <script src = "{{ elixir('js/elix_documents.js') }}" type='text/javascript'></script>
@stop