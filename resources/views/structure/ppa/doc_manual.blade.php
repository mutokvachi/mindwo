@extends('frame')

@section('main_content')
    
    <div class='portlet'>
        <div class='portlet-title'>
            <div class="caption font-grey-cascade uppercase">SVS MEDUS Rokasgrāmata</div>                
        </div>
        <div class='portlet-body'>
            {!! format_html_img(Request::root(), get_portal_config('USER_MANUAL_INTRO')) !!}
            <div class="faq-page faq-content-1">
                <div class="faq-content-container">
                    <div class="row">
                        
                            @foreach($groups_rows as $group)
                                <div class="col-md-12">
                                    
                                    <div class="faq-section ">
                                        <h2 class="faq-title uppercase font-blue">{{ $group->title }}</h2>
                                        <div class="panel-group accordion faq-content" id="accordion3">
                                            @foreach($group->rows_lists as $key => $list)
                                            <div class="panel panel-default" id="list_{{ $list->id }}">
                                                <div class="panel-heading">
                                                    <h4 class="panel-title">
                                                        <i class="fa fa-circle"></i>
                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion3" href="#collapse_{{$group->id}}_{{$key}}" aria-expanded="false"> {{ $list->list_title }}</a>
                                                    </h4>
                                                </div>
                                                <div id="collapse_{{$group->id}}_{{$key}}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                                    <div class="panel-body">
                                                        @include('structure.ppa.doc_register', ['list' => $list, 'is_html' => 0])
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop
