var sel = form_object.find("[name=task_type_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		/* If system task then hide human related task fields */
		
		/* activity_id */
		if (val == 8)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=activity_id]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=activity_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=activity_id] option[value='0']").prop("selected", "selected");
		}
		
		/* no_step_nr */
		if ((val != 6 && val != 4))
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=no_step_nr]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=no_step_nr]").hide();
		}
		
		/* field_id, field_value */
		if (val == 4 || val == 5)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_id]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_value]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_value]").hide();
		}
		
		/* field_operation_id */
		if (val == 5)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_operation_id]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=field_operation_id]").hide();
		}
		
		/* task_perform_id, employee_id, term_days, notes */
		if (val == 4 || val == 5 || val == 7 || val == 8)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=task_perform_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=employee_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=term_days]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=notes]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=due_field_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=resolution_field_id]").hide();
						
			form_object.find(".dx-form-field-line[dx_fld_name_form=task_perform_id] option[value='0']").prop("selected", "selected");
			form_object.find(".dx-form-field-line[dx_fld_name_form=employee_id] option[value='0']").prop("selected", "selected");
			form_object.find(".dx-form-field-line[dx_fld_name_form=due_field_id] option[value='0']").prop("selected", "selected");
			
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=task_perform_id]").show();
			
			if (form_object.find("[dx_fld_name=task_perform_id]").val() == 1)
			{
				form_object.find(".dx-form-field-line[dx_fld_name_form=employee_id]").show();
			}
			
			form_object.find(".dx-form-field-line[dx_fld_name_form=term_days]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=notes]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=due_field_id]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=resolution_field_id]").show();
		}
      
      	if (val == 6)
        {
          	form_object.find(".dx-form-field-line[dx_fld_name_form=term_days]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=due_field_id]").hide();
        }
      
        if (val == 3)
        {
           form_object.find("div[dx_attr=tab]").show();
        }
        else
        {
          form_object.find("div[dx_attr=tab]").hide();
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