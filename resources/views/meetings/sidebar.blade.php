<div class="inbox-sidebar">
    @if ($is_preparer)
        <a href="{{ url('/mail/compose') }}" data-title="{{ trans('mail.compose') }}" class="btn red compose-btn btn-block">
          <i class="fa fa-file-o"></i> Jauna sapulce </a>
    @endif
<ul class="inbox-nav" 
    @if (!$is_preparer)
        style='margin-top: 0px!important;'
    @endif
    >
    @if (count($meetings_actual))
        
        <span class="label label-success"> Aktīvās </span>
        <div class="margin-top-5"></div>
        @foreach($meetings_actual as $meeting)
           @include('meetings.meeting_row', ['meeting' => $meeting])
        @endforeach
        
    @endif
    
    @if ($is_preparer && count($meetings_future))
        
        <span class="label label-info"> Sagatavošanā </span>
        <div class="margin-top-5"></div>
        @foreach($meetings_future as $meeting)
           @include('meetings.meeting_row', ['meeting' => $meeting])
        @endforeach
        
    @endif
    
    @if (count($meetings_past))
    
        <hr>
        <span class="label label-default"> Beigušās </span>           
               
        <div class='pull-right'>
               <select>
                    <option>2017</option>
                    <option>2016</option>   
               </select>
        </div>
        <div class="margin-top-5"></div>
        
        @foreach($meetings_past as $meeting)
           @include('meetings.meeting_row', ['meeting' => $meeting])
        @endforeach
        
    @endif    
    
  </ul>  
</div>
