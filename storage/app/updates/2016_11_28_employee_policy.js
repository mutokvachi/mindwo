var sel = form_object.find("[name=is_hiring_date]");
if (sel)
{
	var cur_val = 0;
	if (sel.is(':checked'))
	{
		cur_val = 1;
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		if (val == 0) // show
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=eff_date]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=eff_date]").hide();
		}		
	};
	
	show_show_hide_rel_fields(cur_val);
  
    sel.on('switchChange.bootstrapSwitch', function(event, state) {   
        show_show_hide_rel_fields(state);
    });
}