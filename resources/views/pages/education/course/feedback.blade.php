<div class="dx-edu-course-tab-container">
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-5 col-xs-12">
            <div class="form-group">
                <label>Jūsu vārds</label>
                <input class="form-control dx-edu-course-feedback-author" type="text" maxlength="500" value=""/> 
            </div>
            <div class="form-group">
                <label>Jūsu e-pasts</label>
                <input class="form-control dx-edu-course-feedback-email" type="text" maxlength="500" value=""/> 
            </div>
            <div class="form-group">
                <label>Jūsu komentārs</label>
                <textarea class="form-control dx-edu-course-feedback-text" rows="5" cols="1"></textarea>
            </div>
            <div style='margin-top:10px;'>
                <button class="btn btn-md dx-edu-course-feedback-btn-save">Pievienot atsauksmi</button>
            </div>
        </div>
        <div class="visible-xs col-xs-12" style='border-bottom: 1px solid #ddd; margin-bottom:10px; margin-top:10px;'>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
            <h4>Atsauksmes</h4>
           @if($subject->feedbacks()->where('is_published', 1)->count() > 0)
                @foreach($subject->feedbacks()->where('is_published', 1)->get() as $feedback)
                <div style="border-bottom: 1px solid #ddd; padding-bottom:10px; margin-bottom:10px;">
                    <span style="font-weight:bold; font-size:14px;">{{ $feedback->author }}</span>
                    <small>{{ $feedback->created_time->format(config('dx.txt_date_format')) }}</small>
                    <div>{{ $feedback->text }}</div>
                </div>
                @endforeach        
            @else
                Šobrīd vēl nav iesniegtas nevienas atsauksmes par mācību pasākumu
            @endif 
        </div>
    </div>
</div>