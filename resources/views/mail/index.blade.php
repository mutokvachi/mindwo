@extends('mail.common')

@section('title', trans('mail.'.$folderId))

@section('mail_content')
  <table class="table table-striped table-advance table-hover folder-{{ $folderId }}">
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
              <li>
                <a href="javascript:;" class="inbox-delete">
                  <i class="fa fa-trash-o"></i> {{ trans('mail.delete') }} </a>
              </li>
            </ul>
          </div>
        </th>
        <th class="pagination-control" colspan="3">
          @if($count = count($messages))
            <span class="pagination-info">
              {{ ($start = ($page - 1) * $itemsPerPage) + 1 }}
              -
              {{ ($counts[$folderId] - $start) > $itemsPerPage ? $start + $itemsPerPage : $counts[$folderId] }}
              of
              {{ $counts[$folderId] }}
            </span>
            @if($page > 1)
              <a class="btn btn-sm blue btn-outline" href="{{ route(Route::currentRouteName(), ['page' => $page - 1]) }}">
                <i class="fa fa-angle-left"></i>
              </a>
            @endif
            @if($page < $pageCount)
              <a class="btn btn-sm blue btn-outline" href="{{ route(Route::currentRouteName(), ['page' => $page + 1]) }}">
                <i class="fa fa-angle-right"></i>
              </a>
            @endif
          @endif
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach($messages as $message)
        <tr class="{{-- $message->is_read ? '' : 'unread' --}}" data-messageid="{{ $message->id }}">
          <td class="inbox-small-cells">
            <label class="mt-checkbox mt-checkbox-single mt-checkbox-outline">
              <input type="checkbox" class="mail-checkbox" value="{{ $message->id }}" />
              <span></span>
            </label>
          </td>
          <td class="inbox-small-cells">
            <i class="fa fa-star"></i>
          </td>
          <td class="view-message hidden-xs">
            @for($i = 0, $list = $message->getPlainRecipientsList(); $i < count($list); $i++)
              {{ $list[$i]['text'] }}{{ ($i < count($list) - 1) ? ', ' : '' }}
            @endfor
          </td>
          <td class="view-message ">{{ $message->subject }}</td>
          <td class="view-message inbox-small-cells">
            @if(strlen($message->attachments))
              <i class="fa fa-paperclip"></i>
            @endif
          </td>
          <td class="view-message text-right">
            @if($folderId == 'sent')
              {{ $message->formatDate($message->sent_time) }}
            @elseif($folderId == 'draft')
              @if($message->modified_time)
                {{ $message->formatDate($message->modified_time) }}
              @else
                {{ $message->formatDate($message->created_time) }}
              @endif
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection