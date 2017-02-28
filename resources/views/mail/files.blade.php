@if(isset($attachments))
  @foreach($attachments as $attachment)
    <tr class="template-download">
      <td class="name" width="30%">
        <span>{{ $attachment->file_name }}</span>
      </td>
      <td class="size" width="40%">
        <span>{{ $attachment->formatFileSize() }}</span>
      </td>
      <td colspan="2"></td>
      <td class="delete" width="10%" align="right">
        @if(isset($mode))
          <button class="btn default btn-sm delete-attachment-button" data-url="{{ route('mail_delete_attachment', ['id' => $attachment->id]) }}">
            <i class="fa fa-times"></i>
          </button>
        @endif
      </td>
    </tr>
  @endforeach
@endif