var sel = form_object.find("[name=list_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
		cur_val = sel.val();
	}
	
	var show_show_hide_rel_fields = function(val)
	{
		
		var sel_empl = form_object.find("[name=filter_field_id]");
                var old_val = sel_empl.val();
		sel_empl.empty();
      	
                if (val == 0) {
                        return;    
                }
                        var url = "/api/view/7/data/raw/list_id/" + val;

                toastr.clear();
                        show_form_splash();
                        $.ajax({ 
                    type: 'GET',
                    url: DX_CORE.site_url  + url,
                    async:true,
                    success : function(data) {              	
                                        sel_empl.append("<option value='0'></option>");	
                                    var cnt = 0;
                                        var first_id = 0;
                                        $.each(JSON.parse(data.rows), function() {
                                            if (this.type_id == 2 || this.type_id ==9) {
                                                    sel_empl.append("<option value='" + this.id + "'>" + this.title_list + "</option>");
                                                    if (first_id == 0) {
                                                            first_id = this.id;
                                                    }
                                                    cnt++;
                                            }
                                        });
                                        
                                        sel_empl.val(old_val);
                                        if (cnt == 0) {
                                                notify_err("Can't load list fields with data type 'Date' or 'Date/Time'!");
                                        }


                                        hide_form_splash();
                    }
		});
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