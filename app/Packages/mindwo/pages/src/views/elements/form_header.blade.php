<div class='modal-header' style='background-color: #31708f;'>					
    <button type='button' class='close' data-dismiss='modal' title="Aizvērt"><i class='fa fa-times' style="color: white"></i></button>
    <h4 class='modal-title' style="color: white;">
        {{ $form_title }}
        @if ($badge)
            &nbsp;<span class='badge'>{{ $badge }}</span>
        @endif
    </h4>
</div>