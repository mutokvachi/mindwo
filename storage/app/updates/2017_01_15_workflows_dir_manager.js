var sel = form_object.find("[name=task_perform_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		var task = form_object.find("[name=task_type_id]"); 
      	var task_id = task.val();
      
      	/* field_id */
		if (val == 2 || val == 6 || val == 7 || val == 8 || task_id == 4 || task_id == 5)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_id]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_id]").hide();
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