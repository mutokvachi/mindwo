var sel_p = form_object.find("[dx_fld_name=task_perform_id]");
if (sel_p)
{
	var cur_val = 0;
	if (sel_p.val() > 0)
	{
		cur_val = sel_p.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{
		/* If system task then hide human related task fields */
		if (val == 1)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=employee_id]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=employee_id]").hide();
		}
	};
	
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
	};

	change_event(null);
	
	sel_p.on('change', change_event);
}