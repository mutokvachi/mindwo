var sel = form_object.find("[name=is_email_sending]");
if (sel)
{
	var cur_val = 0;
	if (sel.is(':checked'))
	{
		cur_val = 1;
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		if (val == 1) // show
		{
                        form_object.find(".dx-form-field-line[dx_fld_name_form=email_receivers]").show();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=role_id]").show();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=field_id]").show();
		}
		else // hide
		{
                        form_object.find(".dx-form-field-line[dx_fld_name_form=email_receivers]").hide();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=role_id]").hide();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=field_id]").hide();
		}		
	};
	
	show_show_hide_rel_fields(cur_val);
  
    sel.on('switchChange.bootstrapSwitch', function(event, state) {
        show_show_hide_rel_fields(state);
    });
}