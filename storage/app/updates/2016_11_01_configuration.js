var sel = form_object.find("[name=field_type_id]");
if (sel)
{
	var cur_val = 0;
	
  	sel.find("option[value='0']").remove();
	sel.find("option[value='2']").remove();
	sel.find("option[value='3']").remove();
	sel.find("option[value='4']").remove();
	sel.find("option[value='6']").remove();
	sel.find("option[value='8']").remove();
	sel.find("option[value='10']").remove();
	sel.find("option[value='11']").remove();
	sel.find("option[value='13']").remove();
	sel.find("option[value='14']").remove();
	sel.find("option[value='16']").remove();
	sel.find("option[value='17']").remove();
  	
  	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
  
		
	var show_hide_rel_fields = function(val)
	{

		//val_varchar
		if (val == 1)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=val_varchar]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=val_varchar]").hide();
		}
		
		//val_script
		if (val == 15)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_script]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_script]").hide();
		}
		
		//val_integer
		if (val == 5)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_integer]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_integer]").hide();
		}
		
		//val_date
		if (val == 9)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_date]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_date]").hide();
		}
		
		//val_file_name
		if (val == 12)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_file_name]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_file_name]").hide();
		}
		
		//val_yesno
		if (val == 7)
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_yesno]").show();
		}
		else
		{
                    form_object.find(".dx-form-field-line[dx_fld_name_form=val_yesno]").hide();
		}
		
	}
	
	var change_event = function(e)
	{
		if (e)
		{
			show_hide_rel_fields($(this).val());
		}
		else
		{
			show_hide_rel_fields(cur_val);
		}
	}

	change_event(null);
	
	sel.on('change', change_event);
}
