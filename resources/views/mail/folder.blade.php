@extends('mail.index')

@section('mail_content')
  <table class="table table-striped table-advance table-hover">
    <thead>
      <tr>
        <th colspan="3">
          <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
            <input type="checkbox" class="mail-group-checkbox" />
            <span></span>
          </label>
          <div class="btn-group input-actions">
            <a class="btn btn-sm blue btn-outline dropdown-toggle sbold" href="javascript:;" data-toggle="dropdown"> {{ trans('mail.actions') }}
              <i class="fa fa-angle-down"></i>
            </a>
            <ul class="dropdown-menu">
              {{--
              <li>
                <a href="javascript:;">
                  <i class="fa fa-pencil"></i> Mark as Read </a>
              </li>
              <li>
                <a href="javascript:;">
                  <i class="fa fa-ban"></i> Spam </a>
              </li>
              <li class="divider"> </li>
              --}}
              <li>
                <a href="javascript:;">
                  <i class="fa fa-trash-o"></i> {{ trans('mail.delete') }} </a>
              </li>
            </ul>
          </div>
        </th>
        <th class="pagination-control" colspan="3">
          @if($count = count($messages))
            <span class="pagination-info">
                1-{{ $count }} of {{ $count }}
            </span>
            <a class="btn btn-sm blue btn-outline">
              <i class="fa fa-angle-left"></i>
            </a>
            <a class="btn btn-sm blue btn-outline">
              <i class="fa fa-angle-right"></i>
            </a>
          @endif
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach($messages as $message)
        <tr class="{{ $message->is_read ? '' : 'unread' }}" data-messageid="{{ $message->id }}">
          <td class="inbox-small-cells">
            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
              <input type="checkbox" class="mail-checkbox" value="1" />
              <span></span>
            </label>
          </td>
          <td class="inbox-small-cells">
            <i class="fa fa-star"></i>
          </td>
          <td class="view-message hidden-xs">{{ $message->to }}</td>
          <td class="view-message ">{{ $message->subject }}</td>
          <td class="view-message inbox-small-cells">
            @if(strlen($message->attachments))
              <i class="fa fa-paperclip"></i>
            @endif
          </td>
          <td class="view-message text-right"> {{ $message->formatDate($message->sent_time) }} </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection