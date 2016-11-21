var sel = form_object.find("[name=is_several_days]");
if (sel)
{
	var cur_val = 0;
	if (sel.is(':checked'))
	{
		cur_val = 1;
	}
	
	var show_show_hide_rel_fields = function(val)
	{		
		if (val) // show
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=to_month_id]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=to_day_id]").show();
                        
                        form_object.find(".dx-form-field-line[dx_fld_name_form=from_month_id]").find('label').text(Lang.get('calendar.lbl_month_from'));
                        form_object.find(".dx-form-field-line[dx_fld_name_form=from_day_id]").find('label').text(Lang.get('calendar.lbl_day_from'));
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=to_month_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=to_day_id]").hide();
                        
                        form_object.find(".dx-form-field-line[dx_fld_name_form=from_month_id]").find('label').text(Lang.get('calendar.lbl_month'));
                        form_object.find(".dx-form-field-line[dx_fld_name_form=from_day_id]").find('label').text(Lang.get('calendar.lbl_day'));
		}		
	}
	
	show_show_hide_rel_fields(cur_val);
  
    sel.on('switchChange.bootstrapSwitch', function(event, state) {   
        show_show_hide_rel_fields(state);   
        if (!state) {
            // clear values
            form_object.find(".dx-form-field-line[dx_fld_name_form=to_month_id]").find('select')[0].selectedIndex = 0;
            form_object.find(".dx-form-field-line[dx_fld_name_form=to_day_id]").find('select')[0].selectedIndex = 0;
        }
    });
}