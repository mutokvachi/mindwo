var sel = form_object.find("[name=view_type_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		/* max_lenght */
		if (val == 9)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=custom_sql]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=custom_sql]").hide();
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
	
	sel.on('change', change_event);
}

