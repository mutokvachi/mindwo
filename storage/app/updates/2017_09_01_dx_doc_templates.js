var sel = form_object.find("[name=kind_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{	
        var tab = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_dx_doc_templates.tab_template') + "')" ).closest("li");
        
        if (tab.hasClass("active")) {
            var tab_main = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_dx_doc_templates.tab_main') + "')" );
            tab_main.tab('show');
        }

        if (val == 1) // Word
		{
            form_object.find(".dx-form-field-line[dx_fld_name_form=file_name]").show();
            tab.hide();
		}
		else // PDF
		{
            form_object.find(".dx-form-field-line[dx_fld_name_form=file_name]").hide();
            tab.show();
		}
		
		
	}
	
	var change_event = function(e)
	{
		if (e)
		{
			show_show_hide_rel_fields($(this).val());
		}
		else
		{
			show_show_hide_rel_fields(cur_val);
		}
	}

	change_event(null);
	
	sel.on('change', change_event);
}

form_object.find(".dx-btn-save-form").click(function(e) {	
	if (!validateData()) {
		e.stopImmediatePropagation();
	}
});

var validateData = function() {
    var kind = parseInt(form_object.find("[name=kind_id]").val());
    
    if (kind === 1 && form_object.find(".dx-form-field-line[dx_fld_name_form=file_name]").find("div.fileinput-exists").length == 0) {        
        notify_err(Lang.get('db_dx_doc_templates.err_no_file'));
        var tab_main = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_dx_doc_templates.tab_main') + "')" );
        tab_main.tab('show');

        return false;
    }

    tinyMCE.triggerSave();
    
    if (kind === 2 && form_object.find("[name=html_template]").val().trim() === "") {        
        notify_err(Lang.get('db_dx_doc_templates.err_no_html'));
        var tab_main = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_dx_doc_templates.tab_template') + "')" );
        tab_main.tab('show');

        return false;    
    }

    return true;
};

var addRedAsteric = function(fld) {	
    var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=" + fld + "]");
    fld_line.find(".dx-fld-title").after('<span style="color: red"> *</span>');	
};

addRedAsteric("file_name");
addRedAsteric("html_template");