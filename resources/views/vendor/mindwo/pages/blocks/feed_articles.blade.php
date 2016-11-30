@if (count($articles_items))
    <div class="dx-article-feed-wrapper">
        <div class="dx-article-feed-content"  id="feed_area_{{ $block_guid }}" >
             <div class="article_row_area">
                @foreach($articles_items as $article)
                    @include('mindwo/pages::elements.articles_ele')
                @endforeach
                @if ($top_count == 0)
                    {!! $articles_items->render() !!}
                @endif
            </div>
        </div>
    </div>
@endif 
        
    
