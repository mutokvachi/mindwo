@extends('frame')

@section('main_custom_css')        
    @include('pages.view_css_includes')
    <link href="{{ elixir('css/elix_education.css') }}" rel="stylesheet" />
@stop

@section('main_content')
<div class="dx-edu-course-page" data-dx-subject_id="{{ $subject->id }}">  
    <div class="portlet light">
        <div class="portlet-title">
            <div class="caption">
                <i class="{{ 'fa fa-university' }}"></i>
                <span class="caption-subject bold uppercase">{{ $subject->title }}</span>
            </div>
        </div>
        <div class="portlet-body">
            <div style="margin-bottom: 10px;">  
                <a href="{{Request::root()}}/edu/catalog" class="btn btn-sm btn-default ">Atgriezties uz katalogu</a>
                <?php 
                    $availability = $subject->getAvailability();              
                ?>
               @if($availability->group_count > 0)
                    @if($availability->is_not_full >= 1)
                        <a href="{{Request::root()}}/edu/registration/{{ $subject->id }}" class="btn btn-sm btn-primary">Pieteikties</a>   
                    @else 
                        <button class="btn btn-sm btn-danger disabled">Visas grupas ir pilnas</button>
                    @endif 
                @else
                    <button class="btn btn-sm btn-danger disabled">Šobrīd nav pieejams</button>
                @endif
            </div>
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="#dx-edu-course-tab-details" data-toggle="tab"> Apraksts </a>
                </li>
                <li>
                    <a href="#dx-edu-course-tab-time" data-toggle="tab"> Laiks, ilgums, cena </a>
                </li>
                <li>
                    <a href="#dx-edu-course-tab-teachers" data-toggle="tab"> Pasniedzēji </a>
                </li>
                <li>
                    <a href="#dx-edu-course-tab-contact" data-toggle="tab"> Kontakti </a>
                </li>
                <li>
                    <a href="#dx-edu-course-tab-feedback" data-toggle="tab"> Atsauksmes </a>
                </li>
            </ul>
            <div class="tab-content dx-edu-tab-content">
                <div class="tab-pane fade active in" id="dx-edu-course-tab-details">
                    @include('pages.education.course.details')
                </div>
                <div class="tab-pane fade" id="dx-edu-course-tab-time">
                    @include('pages.education.course.time')
                </div>
                <div class="tab-pane fade" id="dx-edu-course-tab-teachers">
                    @include('pages.education.course.teachers')
                </div>
                <div class="tab-pane fade" id="dx-edu-course-tab-contact">
                    @include('pages.education.course.contact')
                </div>
                <div class="tab-pane fade" id="dx-edu-course-tab-feedback">
                    @include('pages.education.course.feedback')
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('main_custom_javascripts')
    @include('pages.view_js_includes')
    <script src = "{{ elixir('js/elix_education.js') }}" type='text/javascript'></script>
@stop