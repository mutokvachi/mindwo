form_object.find(".dx-form-field-line[dx_fld_name_form=title]").show();

var sel = form_object.find("[name=type_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{

		//is_html_clean
		if (val == 10)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_clean_html]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_clean_html]").hide();
		}
		
		//is_crypto
		if (val == 1 || val == 4 || val == 5 || val == 18 || val == 12)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_crypted]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_crypted]").hide();
		}
		
		//max_lenght
		if (val == 0 || val == 2 || val== 3 || val == 5 || val == 6 || val == 7 || val == 8 || val== 9 || val == 10 || val == 12 || val == 14 || val == 15 || val == 22)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=max_lenght]").hide();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=max_lenght]").show();
		}
		
		//is_required
		if (val ==0 || val == 6)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_required]").hide();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_required]").show();
		}
		
		//rel_list_id, rel_display_field_id, rel_view_id, rel_display_formula_field
		if (val == 3 || val == 8 || val == 14)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_list_id]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_display_field_id]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_view_id]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_display_formula_field]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_fields_synchro]").show();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=is_right_check]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_list_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_display_field_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_view_id]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=rel_display_formula_field]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_fields_synchro]").hide();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=is_right_check]").hide();
		}
		
		//default_value
		if (val == 0 || val == 6 || val == 12 || val == 13)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=default_value]").hide();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=default_value]").show();
		}
		
		//formula
		if (val == 1 || val == 2 || val == 4 || val == 5 || val == 9 || val == 11)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=formula]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=formula]").hide();
		}
		
		//is_public_file, is_image_file, is_word_generation, is_multiple_files, is_text_extract
		if (val == 12)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_public_file]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_image_file]").show();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=is_word_generation]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_multiple_files]").show();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_text_extract]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_public_file]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_image_file]").hide();
                        form_object.find(".dx-form-field-line[dx_fld_name_form=is_word_generation]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_multiple_files]").hide();
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_text_extract]").hide();
		}
		
		//numerator_id
		if (val == 13)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=numerator_id]").show();
			
			form_object.find(".dx-form-field-line[dx_fld_name_form=reg_role_id]").show();
		}
		else
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=numerator_id]").hide();
			
			form_object.find(".dx-form-field-line[dx_fld_name_form=reg_role_id]").hide();
		}
		
		if (val ==9 || val == 13)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_manual_reg_nr]").show();
		}
		else {
			form_object.find(".dx-form-field-line[dx_fld_name_form=is_manual_reg_nr]").hide();
		}
                
                // items
                if (val ==22)
		{
			form_object.find(".dx-form-field-line[dx_fld_name_form=items]").show();
		}
		else {
			form_object.find(".dx-form-field-line[dx_fld_name_form=items]").hide();
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
