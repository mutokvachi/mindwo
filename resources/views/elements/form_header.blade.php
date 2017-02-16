<div class='modal-header dx-form-header'>					
    <button type='button' class='close dx-form-close-btn' data-dismiss='modal' title="{{ trans('form.btn_close') }}"><i class='fa fa-times' style="color: white"></i></button>
    <h4 class='modal-title' style="color: white;">
        @if (isset($form_icon) && $form_icon)
            {!! $form_icon !!}
        @endif
        {{ $form_title }}
        @if ($badge)
            &nbsp;<span class='badge'>{{ $badge }}</span>
        @endif
    </h4>
</div>