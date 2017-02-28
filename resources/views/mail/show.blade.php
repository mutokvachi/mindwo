@extends('mail.common')

@section('title', trans('mail.view_message'))

@section('mail_content')
  <div class="inbox-wrapper" data-id="{{ $message->id }}" data-folder="{{ $message->folder }}">
    <div class="inbox-header inbox-view-header">
      <h1 class="pull-left">{{ $message->subject }}
        <a href="{{ route('mail_'.$message->folder) }}"> {{ trans('mail.'.$message->folder) }} </a>
      </h1>
      {{--
      <div class="pull-right">
        <a href="javascript:;" class="btn btn-icon-only dark btn-outline">
          <i class="fa fa-print"></i>
        </a>
      </div>
      --}}
    </div>
    <div class="inbox-view-info">
      <div class="row">
        <div class="col-md-7">
            To
          <span class="sbold">
            @for($i = 0, $list = $message->getPlainRecipientsList(); $i < count($list); $i++)
              {{ $list[$i]['text'] }}{{ ($i < count($list) - 1) ? ', ' : '' }}
            @endfor
          </span>
          @if($message->folder == 'scheduled')
            {{ trans('mail.scheduled_on') }}
            {{ $message->formatDate($message->send_time) }}
          @elseif($message->folder == 'draft')
            {{ trans('mail.saved_on') }}
            {{ $message->formatDate($message->modified_time) }}
          @else
            {{ trans('mail.sent_on') }}
            {{ $message->formatDate($message->sent_time) }}
          @endif
        </div>
        <div class="col-md-5 inbox-info-btn">
          <div class="btn-group">
            <a class="btn btn-sm blue dropdown-toggle sbold" href="javascript:;" data-toggle="dropdown"> {{ trans('mail.actions') }}
              <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu pull-right">
              @if($message->folder == 'scheduled')
                <li>
                  <a href="javascript:;" class="inbox-edit-btn"><i class="fa fa-edit"></i> {{ trans('mail.edit') }}</a>
                </li>
              @endif
              <li>
                <a href="javascript:;" class="inbox-delete-btn"><i class="fa fa-trash-o"></i> {{ trans('mail.delete') }}</a>
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
      <table role="presentation" class="table table-striped margin-top-10">
        <tbody class="files">
          @include('mail.files')
        </tbody>
      </table>
      
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
  </div>
@endsection