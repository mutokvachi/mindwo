<div class="portfolio-content portfolio-1 dx-block-container-galery" dx_block_init="0" dx_skip = "{{ $skip }}" dx_article_page_id = "{{ $article_page_id }}" dx_source_id = "{{ $source_id }}" dx_filt_source_id = "{{ $filt_source_id }}" dx_filt_year = "{{ $filt_year }}">
    
    <div id="js-filters-juicy-projects" class="cbp-l-filters-button">
        <div data-filter="*" class="cbp-filter-item-active cbp-filter-item btn dark btn-outline uppercase"> Visi
            <div class="cbp-filter-counter"></div>
        </div>
        
        @foreach($types as $type)
        <div data-filter=".{{ $type->code }}" class="cbp-filter-item btn dark btn-outline uppercase"> {{ $type->name }}
            <div class="cbp-filter-counter"></div>
        </div>
        @endforeach        
    </div>
    
    <div class="well">
        <form action='{{ Request::root() }}/{{ Request::path() }}' method='POST' id="search_form" class="search-tools-form">
            <div class="row">
                <div class="col-sm-5 col-lg-2">
                    <div class="form-group">
                        <label>Gads</label>
                        <select class="form-control" name="year">
                            <option value="0" {{ ($filt_year == 0) ? 'selected' : '' }}>-- Visi --</option>
                            @foreach($years as $item)
                                <option value='{{ $item->y }}' {{ ($filt_year == $item->y) ? 'selected' : '' }}>{{ $item->y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-sm-5 col-lg-4">
                    @if ($source_id == 0)
                        <div class="form-group">
                            <label>Sabiedrība</label>
                            <select class="form-control" name="source_id">
                                <option value="0" {{ ($filt_source_id == 0) ? 'selected' : '' }}>-- Visi --</option>
                                @foreach($sources as $source)
                                    <option value='{{ $source->id }}' {{ ($filt_source_id == $source->id) ? 'selected' : '' }}>{{ $source->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>

                <div class="col-sm-2 col-lg-6">
                    <button class="btn blue-soft search-article-btn search-article-bottom pull-right" type="submit">Meklēt</button>
                </div>
            </div>

            {!! csrf_field() !!}
        </form>
    </div>
    
    
    @if (count($galeries) > 0)
        <div class="panel">
            <div class="panel-body">
                <div id="js-grid-juicy-projects" class="cbp">
                    @include('blocks.galeries_items')            
                </div>
                <div id="js-loadMore-juicy-projects" class="cbp-l-loadMore-button">
                    <a href="#" class="cbp-l-loadMore-link btn blue-soft" 
                       rel="nofollow" 
                       @if ($is_last)
                         style="display: none" 
                       @endif
                       id='load_more_link'>VAIRĀK</a>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info" role="alert" style='margin-top: 60px;'>Nav atrasts neviens atbilstošs ieraksts.</div>
    @endif 
</div>