@extends('frame')

@section('main_custom_css')
   
    <link href="{{ elixir('css/elix_articles.css') }}" rel="stylesheet" />
    
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
        
        .daterangepicker {
            z-index: 99000!important;
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
     
    @include('blocks.feed_articles_css')
    
    @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id'))
        @include('pages.view_css_includes')
    @endif
@stop

@section('main_content')
    <h3 class="page-title">{{ trans('article.page_title') }}
        <small>{{ trans('article.sub_title') }}</small>
    </h3>
    
    <div class="dx-articles-page"
        dx_block_guid = "{{ $block_guid }}"
        dx_articles_count = "{{ count($articles) }}"
        dx_mode = "{{ $mode }}"
        dx_current_type = "{{ $type_id }}"
        dx_search_criteria = "{{ urlencode($criteria) }}"
        dx_search_searchType = "{{ urlencode(trans('search_top.news')) }}"
        dx_search_pick_date_from = "{{ $date_from }}"
        dx_search_pick_date_to = "{{ $date_to }}"
        >
        @if ($mode=='search')
            
            @include('search_tools.search_form', [
                'criteria_title' => trans('article.search_placeholder'),
                'fields_view' => 'search_tools.article_fields',
                'form_url' => 'search'
            ])        
        
        @else
            <form action='{{Request::root()}}/raksti_{{ $tag_id }}' method='POST' id="search_form">
                <input type="hidden" name='type' value='{{ $type_id }}' id="search_type">
                <input type="hidden" name="searchType" value="{{ urlencode(trans('search_top.news')) }}" />
                {!! csrf_field() !!}
            </form>
        @endif        

            @if (count($articles))
                <div class="portlet-body type_frame_0"  id="feed_area_{{ $block_guid }}" >
                    <div class="portfolio-content portfolio-1">
                        <div id="js-filters-juicy-projects" class="cbp-l-filters-button">                    
                            @foreach($types as $type)
                                @if ($type->count > 0)
                                    <div data-filter=".{{ $type->id }}" class="cbp-filter-item btn dark btn-outline uppercase {{ ($type->id == $type_id) ? "cbp-filter-item-active" : "" }}" dx_id="{{ $type->id }}" dx_count="{{ $type->count }}"> {{ $type->name }}
                                        <div class="cbp-filter-counter">{{ $type->count }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class='tab-content'>
                        <div class="tab-pane fade active in article_row_area" id='tab_{{ $type->id }}'>
                            @foreach($articles as $article)
                                @include('elements.articles_ele')
                            @endforeach
                            {!! $articles->appends(['criteria' => urlencode($criteria), 'searchType' => urlencode(trans('search_top.news')), 'type' => $type_id, 'pick_date_from' => $date_from, 'pick_date_to' => $date_to])->render() !!}
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    {{ trans('search_top.nothing_found') }}
                </div>
            @endif
    </div>
@stop

@section('main_custom_javascripts')   
    <script src = "{{ elixir('js/elix_articles.js') }}" type='text/javascript'></script>
    @if (Auth::check() && Auth::user()->id != Config::get('dx.public_user_id'))
        @include('pages.view_js_includes')
    @endif
@stop