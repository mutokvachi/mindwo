@extends('meetings.common')

@section('title', long_date($meeting_row->meeting_time))

@section('meeting_status')
    @include('meetings.meeting_status',['meeting_row' => $meeting_row])     
@endsection

@section('meeting_menus')
    @if ($is_preparer && $meeting_row->group_type == $this::MEETING_FUTURE)
        <li><a href="javascript:;" class="dx-register-settings"><i class='fa fa-edit'></i> Rediģēt sapulci</a></li>
        <li><a href="javascript:;" class="dx-register-settings" data-id='{{ $meeting_row->id }}'><i class='fa fa-question-circle'></i> Iestatīt darba kārtību</a></li>
        <li role="separator" class="divider"></li>
        <li><a href="{{ url('/meetings/' . $meeting_type_row->id . '/' . $meeting_row->id . '/start') }}" class="dx-register-settings"><i class='fa fa-clock-o'></i> Sākt sapulci</a></li>
        <li role="separator" class="divider"></li>
    @endif
    
    @if ($is_moderator && $meeting_row->group_type == $this::MEETING_ACTIVE)
        <li><a href="{{ url('/meetings/' . $meeting_type_row->id . '/' . $meeting_row->id . '/next') }}" class="dx-register-settings"><i class='fa fa-angle-double-right'></i> Izskatīt nākamo jautājumu</a></li>
        <li><a href="{{ url('/meetings/' . $meeting_type_row->id . '/' . $meeting_row->id . '/end') }}" class="dx-register-settings"><i class='fa fa-times-circle-o'></i> Beigt sapulci</a></li>
        <li role="separator" class="divider"></li>
    @endif
        
    <li><a href="javascript:;" class="dx-register-settings"><i class='fa fa-file-text'></i> Protokols</a></li>
    <li><a href="javascript:;" class="dx-view-settings" title='Lejupielādēt sapulces darba kārtības visas datnes kā ZIP arhīvu'><i class='fa fa-download'></i> Lejupielādēt</a></li>
@endsection

@section('meeting_content')
  <table class="table table-striped table-advance table-hover">
    <thead>
      <tr>       
        <th>
          N.p.k.
        </th>
        <th>
          Reģ. nr.
        </th>
        <th>
          Darba kārtības jautājums
        </th>
        <th class="text-right">
          Dokumenti
        </th>
        <th>
          Ziņotāji
        </th>
        <th>
          Statuss
        </th>
      </tr>
    </thead>
    <tbody>
      @if (count($agendas))
        @foreach($agendas as $agenda)
          <tr data-id="{{ $agenda->id }}" class="{{ ($agenda->status_code == $this::AGENDA_IN_PROCESS) ? 'agenda_process' : '' }}">

            <td class="inbox-small-cells">
              {{ $agenda->order_index }}
            </td>
            
            <td>
            AG-{{ $agenda->order_index }}
            </td>
            
            <td class="view-message"><a href="javascript:;" class='dx-agenda-link'>{{ $agenda->title }}</a></td>
            
            <td class="view-message text-right">
                <i class="fa fa-paperclip"></i> 3
            </td>
            
            <td>
             Jānis Bērziņš
            </td>
            
            <td class="{{ ($agenda->status_code == $this::AGENDA_IN_PROCESS) ? 'agenda_process' : (($agenda->status_code == $this::AGENDA_PROCESSED) ? 'agenda_processed' : '') }}">
             {{ $agenda->status }}
            </td>
          </tr>
        @endforeach
      @endif
    </tbody>
  </table>

    @include('meetings.agenda_popup')
@endsection