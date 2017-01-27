@extends('mail.common')

@section('title', trans($mode == 'compose' ? 'mail.compose' : 'mail.edit'))

@section('mail_content')
  <form {!! $mode == 'edit' ? 'data-id="'.$message->id.'" data-folder="'.$message->folder.'"' : '' !!} class="inbox-compose form-horizontal" id="fileupload" action="#" method="POST" enctype="multipart/form-data">
    <div class="inbox-compose-btn">
      <button class="btn green inbox-send-btn">
        <i class="fa fa-check"></i> {{ trans('mail.send') }}
      </button>
      <button class="btn default inbox-discard-btn">{{ trans('mail.discard') }}</button>
      <button class="btn default inbox-draft-btn">{{ trans('mail.draft') }}</button>
    </div>
    <div class="inbox-form-group mail-to">
      <label class="control-label">{{ trans('mail.to') }}:</label>
      <div class="controls controls-to">
        <select name="to" class="form-control inbox-input-to" multiple="multiple">
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
        <input type="text" class="form-control inbox-input-subject" name="subject" value="{{ $mode == 'edit' ? $message->subject : '' }}">
      </div>
    </div>
    <div class="inbox-form-group">
      <textarea class="inbox-editor inbox-wysihtml5 form-control" name="message" rows="12">
        @if($mode == 'edit')
          {{ $message->body }}
        @endif
      </textarea>
    </div>
    {{--
    <div class="inbox-compose-attachment">
      <span class="btn green btn-outline fileinput-button">
        <i class="fa fa-plus"></i>
        <span> {{ trans('mail.attach') }}... </span>
        <input type="file" name="files[]" multiple>
      </span>
      <table role="presentation" class="table table-striped margin-top-10">
        <tbody class="files"></tbody>
      </table>
    </div>
    <script id="template-upload" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-upload fade">
          <td class="name" width="30%">
              <span>{%=file.name%}</span>
          </td>
          <td class="size" width="40%">
              <span>{%=o.formatFileSize(file.size)%}</span>
          </td>
          {% if (file.error) { %}
            <td class="error" width="20%" colspan="2">
              <span class="label label-danger">{{ trans('mail.error') }}</span>
              {%=file.error%}
            </td>
          {% } else if (o.files.valid && !i) { %}
            <td>
              <p class="size">{%=o.formatFileSize(file.size)%}</p>
              <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
              </div>
            </td>
          {% } else { %}
            <td colspan="2"></td>
          {% } %}
          <td class="cancel" width="10%" align="right">
            {% if (!i) { %}
              <button class="btn btn-sm red cancel">
                <i class="fa fa-ban"></i>
                <span>{{ trans('mail.cancel') }}</span>
              </button>
            {% } %}
          </td>
        </tr>
      {% } %}
    </script>
    <!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
      {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
          {% if (file.error) { %}
            <td class="name" width="30%">
                <span>{%=file.name%}</span>
            </td>
            <td class="size" width="40%">
                <span>{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td class="error" width="30%" colspan="2">
              <span class="label label-danger">{{ trans('mail.error') }}</span>
              {%=file.error%}
            </td>
          {% } else { %}
            <td class="name" width="30%">
              <a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size" width="40%">
              <span>{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td colspan="2"></td>
          {% } %}
          <td class="delete" width="10%" align="right">
            <button class="btn default btn-sm"
              data-type="{%=file.delete_type%}"
              data-url="{%=file.delete_url%}"
              {% if (file.delete_with_credentials) { %}
                data-xhr-fields='{"withCredentials":true}'
              {% } %}>
              <i class="fa fa-times"></i>
            </button>
          </td>
        </tr>
      {% } %}
    </script>
    --}}
    <div class="inbox-compose-btn">
      <button class="btn green inbox-send-btn">
        <i class="fa fa-check"></i> {{ trans('mail.send') }}
      </button>
      <button class="btn default inbox-discard-btn">{{ trans('mail.discard') }}</button>
      <button class="btn default inbox-draft-btn">{{ trans('mail.draft') }}</button>
    </div>
  </form>
@endsection