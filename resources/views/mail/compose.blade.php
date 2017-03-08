@extends('mail.common')

@section('title', trans($mode == 'compose' ? 'mail.compose' : 'mail.edit'))

@section('mail_content')
  <div class="inbox-wrapper" {!! $mode == 'edit' ? 'data-id="'.$message->id.'" data-folder="'.$message->folder.'"' : '' !!}>
    <form class="inbox-compose form-horizontal" id="composeform" action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
      <div class="inbox-compose-btn">
        <button class="btn green inbox-send-btn">
          <i class="fa fa-check"></i> {{ trans($mode == 'edit' && $message->folder == 'scheduled' ? 'mail.save' : 'mail.send') }}
        </button>
        <button class="btn default inbox-discard-btn">{{ trans('mail.discard') }}</button>
        <button class="btn default inbox-draft-btn">{{ trans('mail.draft') }}</button>
      </div>
      <div class="inbox-form-group mail-to">
        <label class="control-label">{{ trans('mail.to') }}:</label>
        <div class="controls controls-to">
          <select name="to" class="form-control inbox-input-to" multiple="multiple" style="width: 100%">
            @if($mode == 'compose')
              @if(strlen($toId))
                <option value="{{ $toId }}" selected>{{ $toTitle }}</option>
              @endif
            @else
              @foreach($message->getPlainRecipientsList() as $item)
                <option value="{{ $item['id'] }}" selected>{{ $item['text'] }}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>
      <div class="inbox-form-group">
        <label class="control-label">{{ trans('mail.subject') }}:</label>
        <div class="controls">
          <input type="text" class="form-control inbox-input-subject" name="subject"
            value="{{ $mode == 'edit' ? $message->subject : '' }}">
        </div>
      </div>
      <div class="inbox-form-group">
        <label class="control-label">{{ trans('mail.send_time') }}:</label>
        <div class="controls">
          <input type="text" class="form-control inbox-input-send_time" name="send_time"
            value="{{ ($mode == 'edit' && $message->send_time ) ? $message->formatDate($message->send_time, true) : '' }}">
        </div>
      </div>
      <div class="inbox-form-group">
        <textarea class="inbox-editor inbox-wysihtml5 form-control" name="message" style="height: 300px;">
          @if($mode == 'edit')
            {{ $message->body }}
          @endif
        </textarea>
      </div>
      <div class="inbox-compose-attachment">
        <table role="presentation" class="table table-striped margin-top-10">
          <tbody class="files">
            @if($mode == 'edit')
              @include('mail.files')
            @endif
          </tbody>
        </table>
        <span class="btn green btn-outline add-files-button">
          <i class="fa fa-plus"></i>
          <span> {{ trans('mail.attach') }}... </span>
        </span>
      </div>
      <div class="inbox-compose-btn">
        <button class="btn green inbox-send-btn">
          <i class="fa fa-check"></i>
            {{ trans($mode == 'edit' && $message->folder == 'scheduled' ? 'mail.save' : 'mail.send') }}
        </button>
        <button class="btn default inbox-discard-btn">{{ trans('mail.discard') }}</button>
        <button class="btn default inbox-draft-btn">{{ trans('mail.draft') }}</button>
      </div>
    </form>
  </div>
@endsection