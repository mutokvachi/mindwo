<div class="panel article_item_row type_{{ $article->type_id }}">
    <div class="panel-body">
        <table width='100%' border='0' cellspacing="10">
            <tr>
                <td valign='top' width='205px' class="visible-lg">
                    <a class="no_hover dx-no-link-pic" target="_dx_portal" href="{{ $article->open_article_url }}" title="{{ $article->hover_hint }}">
                        <img class="img-responsive img-thumbnail" src="{{Request::root()}}/formated_img/medium/{{ ($article->picture_guid) ? $article->picture_guid : $article->placeholder_pic }}" alt="{{ $article->title }}" style='width: 180px;'>
                    </a>
                </td>
                <td valign='top'>
                    <h4 class="dx_article_title"><a class="dx-no-link-pic" target="_dx_portal" href="{{ $article->open_article_url }}">{{ $article->title }}</a></h4>
                    <div class="dx_article_date">
                        <a class="no_hover dx-no-link-pic" target="_dx_portal" href="{{ $article->open_article_url }}">
                            <span class="font-yellow-gold">{!! format_event_time($article->publish_time) !!}</span>
                            @if($article->type_picture)
                                <span style="color: #007CC4; margin-left: 5px;" title="{{ $article->hover_hint }}">
                                    <i class="fa {{ $article->type_picture  }}"></i>
                                </span>
                            @else
                                @if ($article->picture_galery_id > 0)
                                    <span style="color: #007CC4; margin-left: 5px;" title="Ziņai ir pievienota attēlu galerija">
                                        <i class="fa fa-picture-o"></i>
                                    </span>
                                @endif
                                @if ($article->video_galery_id > 0)
                                    <span style="color: #007CC4; margin-left: 5px;" title="Ziņai ir pievienota video galerija">
                                        <i class="fa fa-video-camera"></i>
                                    </span>
                                @endif
                                @if ($article->file_added_id > 0)
                                    <span style="color: #007CC4; margin-left: 5px;" title="Ziņai ir pievienota datne">
                                        <i class="fa fa-file-o"></i>
                                    </span>
                                @endif
                                @if ($article->content_id == 2)
                                    <span style="color: #007CC4; margin-left: 5px;" title='Ārējā saite'>
                                        <i class="fa fa-external-link"></i>
                                    </span>
                                @endif
                                @if ($article->content_id == 3)
                                    <span style="color: #007CC4; margin-left: 5px;" title='Lejuplādējama datne'>
                                        <i class="fa fa-download"></i>
                                    </span>
                                @endif
                            @endif
                        </a>
                    </div>
                    
                    <a class="no_hover hidden-lg dx-no-link-pic" target="_dx_portal" href="{{ $article->open_article_url }}" title="{{ $article->hover_hint }}">
                        <img class="img-responsive img-thumbnail" src="{{Request::root()}}/formated_img/medium/{{ ($article->picture_guid) ? $article->picture_guid : $article->placeholder_pic }}" alt="{{ $article->title }}" style='width: 100%;'>
                    </a>
                    
                    <a class="no_hover dx_article_text dx-no-link-pic" target="_dx_portal" href="{{ $article->open_article_url }}">
                        {!! $article->intro_text !!}
                    </a>
                    
                    @include('mindwo/pages::elements.article_tags', ['article' => $article, 'tags' => $article->tags])               
                    
                </td>
            </tr>
        </table>    
    </div>
</div>