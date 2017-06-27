<div class="portlet" id="writeq_{{ $block_guid }}" dx_block_id="{{ $id }}">
        <div class="portlet-title">
            <div class="caption font-grey-cascade uppercase">{{ $block_title }}</div>
            <div class="tools">
                <a class="collapse" href="javascript:;"> </a>                                       
            </div>
        </div>
    <div class="portlet-body">
        <div class="form-group">
            <textarea class='form-control' name='askQuestion' rows='4' maxlength='2000' placeholder="{{ trans('mindwo/pages::writeq.hint_write') }}" style="resize: vertical;"></textarea>                            
        </div> 
        <div class="row">
            <div class="col-lg-10" style="margin-top: -5px;">
                <div class="checkbox"><label> <input type="checkbox" class="i-checks"> <small>{{ trans('mindwo/pages::writeq.lbl_check') }}</small></label></div>
            </div>
            <div class="col-lg-2">
                <button class="btn btn-default pull-right" type="button">{{ trans('mindwo/pages::writeq.btn_send') }}</button>
            </div>
        </div>
    </div>                    
</div>

@include('mindwo/pages::blocks.writeq_email_form')