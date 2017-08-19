<div class='modal fade dx-popup-modal dx-publish-popup' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;">
    <div class='modal-dialog modal-lg'>
        <div class='modal-content'>
            @include('elements.form_header',['form_title' => trans('calendar.scheduler.publish_popup_title'), 'badge' => '', 'form_icon' => '<i class="fa fa-globe"></i>'])

            <div class='modal-body' style="overflow-y: auto; max-height: 500px;">
                Publicējot grupas, tiks nosūtītas e-pastu notifikācijas grupu pasniedzējiem, dalībniekiem un atbalsta personālam un informācija būs pieejama visiem iesaistītajiem lietotājiem MPS portālā.
                <br><br>
                Pirms grupu publicēšanas, sistēma veic publicējamo datu korektības pārbaudi.
                <br>
                <br>
                <b>Publicējamo grupu skaits: </b><span class="dx-total-groups">0</span>
                <br>
                <br>
                <div class="dx-publish-progress text-center" style="display: none;">
                    <img src="{{Request::root()}}/assets/global/progress/loading.gif" alt="{{ trans('frame.please_wait') }}" title="{{ trans('frame.please_wait') }}" />
                    {{ trans('frame.data_processing') }}
                </div>
                <div class="text-center alert alert-info bg-green-jungle bg-font-green-jungle" role="alert" style="display: none;">                                    
                    Visas grupas ir veiksmīgi pārbaudītas un publicētas! Informācija par grupām ir nodota un pieejama pasniedzējiem, dalībniekiem un atbalsta personālam.
                </div>
                <div class="text-center alert alert-error bg-red-sunglo bg-font-red-sunglo" role="alert" style="display: none;">                                    
                    Neviena grupa netika publicēta, jo ir konstatēta datu neatbilstība! Lūdzu, veiciet nepieciešamās datu korekcijas.
                </div>
                <div class="dx-problem-lbl" style="margin-bottom: 10px; display: none;"><b>Problemātisko grupu skaits: </b><span class="dx-err-count">0</span></div>
                <div class="ext-cont">
                    <div id="dx-err-groups-box">
                        
                    </div>
                </div>
            </div>
            <div class='modal-footer'>                
                <button type='button' class='btn btn-primary dx-check-publish-btn'>{{ trans('calendar.scheduler.btn_check_publish') }}</button>                
                <button type='button' class='btn btn-white dx-cancel-btn' data-dismiss='modal'>{{ trans('form.btn_cancel') }}</button>                            
            </div>
        </div>
    </div>
</div>