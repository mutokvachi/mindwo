<div class="well">        
    <form action='{{Request::root()}}/{{ $form_url }}' method='POST' class='search-tools-form search-tools-hiden'>
        
        <table border="0" width="100%" style="margin-bottom: -15px;" class="ie9-fix-placeholders">
            <tr>
                <td width="100%">{{ $criteria_title }}:</td>
            </tr>
        </table>
        
        <div class="input-group">
            <input type="text" class="form-control search-phrase-input" placeholder="{{ $criteria_title }}" name='criteria' value='{{ $criteria }}' id='dx-search-tools-txt'>
            <button class="btn btn-primary search-simple-btn search-simple-top" type="submit"><i class="fa fa-search"></i> Meklēt</button>
            <button class="btn btn-primary pull-right search-tools-btn" id="dx_search_tools_btn" type='button'>Meklēšanas rīki <i class="fa fa-caret-down"></i></button>
        </div>

        <div class='search-tools-container'>
            @include($fields_view)                  

            <div style='margin-top: 10px; margin-bottom: 20px;'>
                <button class="btn btn-primary search-simple-btn search-simple-bottom pull-right" type="submit"><i class="fa fa-search"></i> Meklēt</button>
                <a class="pull-left dx-clear-link" style="margin-right: 10px;" title='Notīrīt meklēšanas kritērijus'><i class="fa fa-eraser"></i> Notīrīt kritērijus</a>
            </div>
            <br />
        </div>


        {!! csrf_field() !!}
    </form>
</div>