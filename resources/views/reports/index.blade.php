@extends('reports.common')

@section('title', $group_row->title)
@section('icon', $group_row->icon)

@section('report_content')
  <table class="table table-striped table-advance table-hover">
    <thead>
      <tr>       
        <th colspan="2" style='vertical-align: middle;'>
          {{ trans('reports.page.report_name') }}
        </th>
        <th class="text-right">
          {{ trans('reports.page.last_viewed') }}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach($views as $view)
        <tr data-id="{{ $view->id }}">
          
          <td class="inbox-small-cells">
            <i class="fa fa-list"></i>
          </td>
          <td class="view-message"><a href="{{ url('/skats_') }}{{ $view->id }}" target="_blank">{{ $view->title }}</a></td>
          <td class="view-message text-right">
              @if ($view->last_viewed)
                {{ long_date($view->last_viewed) }}
              @else
                {{ trans('reports.page.never_viewed') }}
              @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
@endsection