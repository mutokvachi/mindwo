<hr>
@if ($article->source_tag_id)
    <a class="a_tag" target="_dx_portal" title="Atlasīt līdzīgas ziņas" href="{{Request::root()}}/datu_avota_raksti_{{ $article->source_tag_id }}"><i class="fa fa-tag"></i> {{ $article->source_tag_title }}</a>
@endif

@foreach($tags as $t)

    <a class="a_tag" target="_dx_portal" title="Atlasīt līdzīgas ziņas" href="{{Request::root()}}/raksti_{{ $t->id }}"><i class="fa fa-tag"></i> {!! $t->name !!}</a>

@endforeach