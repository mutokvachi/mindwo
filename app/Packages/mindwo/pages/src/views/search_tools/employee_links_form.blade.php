<form action='{{Request::root()}}/search' method='POST'>
    <input type="hidden" name="source_id" value="0" />
    <input type="hidden" name="department" value="" />
    <input type="hidden" name="cabinet" value="" />
    <input type="hidden" name="office_address" value="" />
    <input type="hidden" name="searchType" value="Darbinieki" />
    <input type="hidden" name="manager_id" value="0" />
    <input type="hidden" name="subst_empl_id" value="0" />
    <input type="hidden" name="position" value="" />
    {!! csrf_field() !!}
</form>