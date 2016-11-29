var sel = form_object.find("[name=carryover_date_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		if (val == 3) // show
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=month_day_id]").show();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=month_id]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=month_day_id]").hide();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=month_id]").hide();
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