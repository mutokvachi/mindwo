var sel = form_object.find("[name=carryover_type_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		if (val == 2) // show
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=carryover_max]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=carryover_max]").hide();
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