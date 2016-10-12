@extends('main')

@section('content')

<div class="portlet">
    <div class="portlet-title">
        <div class="caption font-grey-cascade uppercase">Informācija par darbinieku</div>
        <div class="tools">
            <a class="collapse" href="javascript:;"> </a>                                       
        </div>
    </div>
    
    <div class="portlet-body" style='padding-right: 30px; padding-left: 30px;'>
        <div class="row">
            <div class="col-lg-4">
                <div class="col-lg-12"  style='margin-bottom: 10px;'>
                    <img src="{{Request::root()}}/img/{{ $item->picture_guid }}" alt="{{ $item->display_name }}" class="img-circle">
                </div>
            </div>
            <div class="col-lg-8">
                <div class="col-lg-12">
                    <h2>{{ $item->display_name }}</h2>
                </div>
                <div class="col-lg-12">
                    <h5>{{ $item->department }}</h5>
                </div>
                <div class="col-lg-12" style='margin-bottom: 10px;'>
                    <h5>{{ $item->position }}</h5>
                </div>
            </div>
            
            <div class="col-lg-12"  style='margin-bottom: 10px; margin-top: 10px;'>
                <span>Tālrunis: <b>{{ $item->phone }}</b>
            </div>
            
            <div class="col-lg-12"  style='margin-bottom: 10px;'>
                <span>E-pasts: <b>{{ $item->email }}</b>
            </div>        

            <div class='col-lg-12'>
                <p style='margin-left: 0px;'>{!! $item->description !!}</p>
            </div>
        </div>
    </div>
</div>

@stop

