<div class='modal fade dx-popup-modal dx-publish-popup' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('calendar.scheduler.publish.popup_title'), 'badge' => '', 'form_icon' => '<i class="fa fa-globe"></i>'])

            <div class='modal-body'>
                <span class='dx-publish-intro1'>{{ trans('calendar.scheduler.publish.intro1') }}</span>
                <br><br>
                <span class='dx-publish-intro2'>{{ trans('calendar.scheduler.publish.intro2') }}</span>
                <br>
                <br>
                <b><span class='dx-publish-lbl-count'>{{ trans('calendar.scheduler.publish.lbl_count') }}</span>: </b><span class="dx-total-groups">0</span>
                <br>
                <br>
                <div class="dx-publish-progress text-center" style="display: none;">
                    <img src="{{Request::root()}}/assets/global/progress/loading.gif" alt="{{ trans('frame.please_wait') }}" title="{{ trans('frame.please_wait') }}" />
                    {{ trans('frame.data_processing') }}
                </div>
                <div class="text-center alert alert-info bg-green-jungle bg-font-green-jungle" role="alert" style="display: none;">                                    
                    <span class='dx-publish-ok'>{{ trans('calendar.scheduler.publish.msg_ok') }}</span>
                </div>
                <div class="text-center alert alert-error bg-red-sunglo bg-font-red-sunglo" role="alert" style="display: none;">                                    
                    <span class='dx-publish-err'>{{ trans('calendar.scheduler.publish.msg_err') }}</span>
                </div>
                <div class="dx-problem-lbl" style="margin-bottom: 10px; display: none;"><b>{{ trans('calendar.scheduler.lbl_problem_count') }}: </b><span class="dx-err-count">0</span></div>
                <div class="ext-cont" style='height: 250px;'>
                    <div id="dx-err-groups-box">
                        
                    </div>
                </div>
            </div>
            <div class='modal-footer'>                
                <button type='button' class='btn btn-primary dx-check-btn'>{{ trans('calendar.scheduler.btn_check') }}</button>                
                <button type='button' class='btn btn-default dx-check-publish-btn'>{{ trans('calendar.scheduler.publish.btn_publish') }}</button>                
                <button type='button' class='btn btn-white dx-cancel-btn' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>