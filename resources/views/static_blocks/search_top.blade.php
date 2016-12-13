<div id="dx-top-search-div"
    trans_nothing_found = "{{ trans("search_top.nothing_found") }}"
    trans_default_info = "{{ trans("search_top.default_info") }}"
    trans_employees = "{{ trans("search_top.employees") }}"
    trans_searching = "{{ trans("search_top.searching") }}"
    trans_default = "{{ Config::get('dx.default_search') }}"
    >
<form action='{{Request::root()}}/search' method='POST' id="dx-top-search-form">
    <input type="hidden" id="searchType" name="searchType"/>
    
    <input type="hidden" name="cabinet" value="" />
    <input type="hidden" name="office_address" value="" />
    <input type="hidden" name="manager_id" value="0" />
    <input type="hidden" name="subst_empl_id" value="0" />
    <input type="hidden" name="source_id" value="0" />
    <input type="hidden" name="department" value="" />
    <input type="hidden" name="position" value="" />
    <input type="hidden" name="is_from_link" value="1" />
    
    <div class="input-group" id="top_search">
        
        <div class="input-group-btn">
            <button id="search_dropd" type="button" class="btn btn-default dropdown-toggle green-soft" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><b id="search_title">{{ Config::get('dx.default_search') }}</b> <span class="caret"></span></button>
          <ul class="dropdown-menu">
            <li class="searchTypeItem"><a href="#">{{ trans("search_top.documents") }}</a></li>
            <li class="searchTypeItem"><a href="#">{{ trans("search_top.employees") }}</a></li>            
            <li class="searchTypeItem"><a href="#">{{ trans("search_top.news") }}</a></li>
          </ul>
        </div>
        
        <input type="text" class="form-control" placeholder="{{ trans("search_top.search_placeholder") }}" id="search_criteria" name="criteria" autofocus>
        
        <div class="input-group-btn">
          <button class="btn btn-default blue-soft" type="submit" id="search_btn" style='border-color: #93a1bb;'><i class="fa fa-search"></i></button>
        </div>
    </div>
    {!! csrf_field() !!}
</form>

<!-- Darbinieku meklēšanas rezultātu izslīdošais bloks -->
<div class="page-quick-sidebar-wrapper dx-employees-quick" data-close-on-body-click="false">
    <div class="page-quick-sidebar">
        <div style="padding:10px;">
            <div id="quick-search-status" class="pull-left">{{ trans("search_top.search_hint") }}</div>
            <a href='#' class='btn blue-soft pull-right close-quick-sidebar'>
                <i class='fa fa-lg fa-close'></i>
            </a>
            <div class="clearfix"></div>
        </div>

        <div class='dx-employees-quick-results'></div>
    </div>
</div>
</div>