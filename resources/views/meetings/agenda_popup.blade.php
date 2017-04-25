<div class='modal fade' aria-hidden='true' role='dialog' aria-hidden='true' data-backdrop='static' style="z-index: 999999;" id='dx-agenda-popup'>
    <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                @include('elements.form_header',['form_icon' => "<i class='fa fa-question-circle'></i>", 'form_title' => "Darba kārtības jautājums", 'badge' => 'Nav izskatīts'])
                   				
                    <div class='modal-body' style="overflow-y: auto; max-height: 500px; padding-left: 40px;">                                               
                    </div>
                    <div class='modal-footer'>                        
                        <button type='button' class='btn btn-white pull-right' data-dismiss='modal'><i class='fa fa-sign-out'></i>&nbsp;{{ trans('wf_info_task.btn_close') }}</button>                            
                    </div>
            </div>
    </div>
</div>