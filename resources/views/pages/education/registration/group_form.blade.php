<div class="modal fade in" id="dx-edu-modal-group" tabindex="-1" role="dialog" aria-labelledby="dx-edu-modal-group-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div id='dx-edu-modal-group-content' class="modal-content">      
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ trans('form.btn_close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="dx-edu-modal-group-label">Grupas izvēle</h4>
            </div>
            <div class="modal-body">
                <div class="form-group required">
                    <label>Izvēlieties kursu</label>
                    <select class='form-control dx-edu-modal-group-select-course'> 
                        @foreach(\App\Models\Education\Subject::where('is_published', 1)->orderBy('title')->get() as $subject)    
                        <option value="{{ $subject->id }}">{{ $subject->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group required">
                    <label>Izvēlieties grupu</label>
                    <select class='form-control dx-edu-modal-group-select-group'>  
                        @foreach($availableOpenGroups as $group)    
                        <option value="{{ $group->id }}" data-subject-id="{{ $group->subject_id }}" style="display:none;">{{ $group->title }}</option>
                        @endforeach   
                    </select>
                </div>               
            </div>
            <div class="modal-footer">
                <button type="button" class="btn pull-left dx-edu-modal-group-decline" data-dismiss="modal">{{ trans('form.btn_close') }}</button>
                <button type="button" class="btn btn-primary dx-edu-modal-group-accept">{{ trans('form.btn_accept') }}</button>   
            </div>
        </div>
    </div>
</div>