@extends('mail.common')

@section('title', trans('mail.view_message'))

@section('mail_content')
  <div class="inbox-header inbox-view-header">
    <h1 class="pull-left">{{ $message->subject }}
      {{--
      <a href="javascript:;"> Inbox </a>
      --}}
    </h1>
    <div class="pull-right">
      <a href="javascript:;" class="btn btn-icon-only dark btn-outline">
        <i class="fa fa-print"></i>
      </a>
    </div>
  </div>
  <div class="inbox-view-info">
    <div class="row">
      <div class="col-md-7">
        {{--
        <img src="../assets/pages/media/users/avatar1.jpg" class="inbox-author">
        --}}
        To
        <span class="sbold">
          @for($i = 0, $list = $message->getPlainRecipientsList(); $i < count($list); $i++)
            {{ $list[$i]['text'] }}{{ ($i < count($list) - 1) ? ', ' : '' }}
          @endfor
        </span>
        on {{ $message->formatDate($message->sent_time) }}
      </div>
      <div class="col-md-5 inbox-info-btn">
        <div class="btn-group">
          <button data-messageid="23" class="btn green reply-btn">
            <i class="fa fa-reply"></i> {{ trans('mail.reply') }}
            <i class="fa fa-angle-down"></i>
          </button>
          <ul class="dropdown-menu pull-right">
            <li>
              <a href="javascript:;" data-messageid="23" class="reply-btn">
                <i class="fa fa-reply"></i> {{ trans('mail.reply') }}</a>
            </li>
            <li>
              <a href="javascript:;">
                <i class="fa fa-arrow-right reply-btn"></i> {{ trans('mail.forward') }}</a>
            </li>
            <li>
              <a href="javascript:;">
                <i class="fa fa-print"></i> {{ trans('mail.print') }}</a>
            </li>
            <li class="divider"></li>
            <li>
              <a href="javascript:;">
                <i class="fa fa-ban"></i> {{ trans('mail.spam') }}</a>
            </li>
            <li>
              <a href="javascript:;">
                <i class="fa fa-trash-o"></i> {{ trans('mail.delete') }}</a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="inbox-view">
    {!! $message->body !!}
  </div>
  <hr>
  <div class="inbox-attached">
    <!--
    <div class="margin-bottom-15">
      <span>{{ trans('mail.attachments') }} — </span>
      <a href="javascript:;">{{ trans('mail.download_all') }}</a>
      <a href="javascript:;">{{ trans('mail.view_all_images') }}</a>
    </div>
    <div class="margin-bottom-25">
      <img src="../assets/pages/media/gallery/image4.jpg">
      <div>
        <strong>image4.jpg</strong>
        <span>173K </span>
        <a href="javascript:;">{{ trans('mail.view') }}</a>
        <a href="javascript:;">{{ trans('mail.download') }}</a>
      </div>
    </div>
    -->
  </div>
@endsection