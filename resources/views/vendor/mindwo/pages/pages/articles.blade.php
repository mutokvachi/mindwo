@extends('frame')

@section('main_custom_css')
   
    <link href="{{Request::root()}}/mindwo/plugins/cubeportfolio/css/cubeportfolio.css" rel="stylesheet" />
    <link href="{{Request::root()}}/mindwo/plugins/datetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <link href="{{Request::root()}}/mindwo/css/pages/search_tools.css" rel="stylesheet" type="text/css" />
    
    <style>       
        .cbp-filter-item {
            padding: 6px 12px!important;
        }
        
        /* Satura tipu filtri */
        .cbp-filter-counter {
            z-index: 9999;
        }
        
        .cbp-l-filters-button {
            margin-bottom: 0px!important;
        }
        
        #search_form_pick_date_to, #search_form_pick_date_from {
            width: 100%!important;
        }
    </style>
    
    @if (count($articles) > 0)
        <style>
            @foreach($types as $type)
                @if ($type->count > 0)                     
                    @foreach($types as $tp)
                        .type_frame_{{ $type->id }} .type_{{ $tp->id}}
                        {
                            display: {{ ($type->id == $tp->id || $type->id == 0 ) ? 'block' : 'none' }};
                        }
                    @endforeach
                @endif
            @endforeach
        </style>
    @endif
     
    @include('mindwo/pages::blocks.feed_articles_css')
@stop

@section('main_content')
    <h3 class="page-title">Portāls
        <small>meklēšana</small>
    </h3>

    <div class="dx-articles-page"
        dx_block_guid = "{{ $block_guid }}"
        dx_articles_count = "{{ count($articles) }}"
        >
        @if ($mode=='search')
            
            @include('mindwo/pages::search_tools.search_form', [
                'criteria_title' => 'Meklēšanas frāze',
                'fields_view' => 'mindwo/pages::search_tools.article_fields',
                'form_url' => 'search'
            ])        
        
        @else
            <form action='{{Request::root()}}/raksti_{{ $tag_id }}' method='POST' id="search_form">
                <input type="hidden" name='type' value='{{ $type_id }}' id="search_type">
                <input type="hidden" name="searchType" value="Ziņas" />
                {!! csrf_field() !!}
            </form>
        @endif

        <input type='hidden' id='current_type' value='{{ $type_id }}' />

            @if (count($articles))
                <div class="portlet-body type_frame_0"  id="feed_area_{{ $block_guid }}" >
                    <div class="portfolio-content portfolio-1">
                        <div id="js-filters-juicy-projects" class="cbp-l-filters-button">                    
                            @foreach($types as $type)
                                @if ($type->count > 0)
                                    <div data-filter=".{{ $type->id }}" class="cbp-filter-item btn dark btn-outline uppercase {{ ($type->id == $type_id) ? "cbp-filter-item-active" : "" }}" dx_id="{{ $type->id }}"> {{ $type->name }}
                                        <div class="cbp-filter-counter">{{ $type->count }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class='tab-content'>
                        <div class="tab-pane fade active in article_row_area" id='tab_{{ $type->id }}'>
                            @foreach($articles as $article)
                                @include('mindwo/pages::elements.articles_ele')
                            @endforeach
                            {!! $articles->appends(['criteria' => urlencode($criteria), 'searchType' => urlencode('Ziņas'), 'type' => $type_id])->render() !!}
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    Nav atrasts neviens atbilstošs ieraksts.
                </div>
            @endif
    </div>
@stop

@section('main_custom_javascripts')
    @if ($mode=='search')
            {!! $picker_from_js !!}
            {!! $picker_to_js !!}

    @endif
    
    <script src="{{ Request::root() }}/mindwo/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js" type="text/javascript"></script>
    <script src="{{ Request::root() }}/mindwo/plugins/datetimepicker/jquery.datetimepicker.js" type="text/javascript"></script>
    <script src="{{ Request::root() }}/mindwo/plugins/jscroll/jquery.jscroll.js" type="text/javascript"></script>
    
    <script src='{{Request::root()}}/mindwo/pages/search_tools.js' type='text/javascript'></script>
    <script src='{{Request::root()}}/mindwo/pages/articles.js' type='text/javascript'></script>
    
@stop