@extends('main')

@section('content')

<div class="portlet">
        <div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">Portāla raksts</div>
            <div class="tools">
                <a class="collapse" href="javascript:;"> </a>                                       
            </div>
        </div>
    <div class="portlet-body" style='padding-right: 30px; padding-left: 30px;'>
        <div class="row">
            <div class="col-lg-12" style='margin-bottom: 10px;'>
                <h2><a href="#">{{ $item->title }}</a></h2>
                <span>{!! format_event_time($item->publish_time) !!}</span>
            </div>
            
            <div class="col-lg-12"  style='margin-bottom: 10px;'>
                <img class="img-responsive img-thumbnail" src="{{Request::root()}}/img/{{ $item->picture_guid }}" alt="{{ $item->title }}" >
            </div>
            
            <div class='col-lg-12'>

                <p style='margin-left: 0px;'>{!! $item->intro_text !!}</p>
            </div>            

            <div class='col-lg-12'>
                <p style='margin-left: 0px;'>{!! $item->article_text !!}</p>
            </div>
        </div>
    </div>
</div>

@stop

