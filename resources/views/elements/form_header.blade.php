<div class='modal-header dx-form-header' style='background-color: #31708f;'>					
    <button type='button' class='close' data-dismiss='modal' title="{{ trans('form.btn_close') }}"><i class='fa fa-times' style="color: white"></i></button>
    <h4 class='modal-title' style="color: white;">
        {{ $form_title }}
        @if ($badge)
            &nbsp;<span class='badge'>{{ $badge }}</span>
        @endif
    </h4>
</div>