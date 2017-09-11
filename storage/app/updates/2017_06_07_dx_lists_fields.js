var sel = form_object.find("[name=type_id]");
if (sel)
{
	var cur_val = 0;
	if (sel.val() > 0)
	{
            cur_val = sel.val();
	}
	        
        var link = $("<a>");
        link.addClass("pull-right");      
        link.hide();
        
        var flds_sel = $("<select>");
        flds_sel.addClass("form-control").addClass("dx-not-focus");
        flds_sel.hide();
        flds_sel.attr("dx_fld_name", "db_name");
        flds_sel.prop("required", true);
        flds_sel.attr("data-cbotext", "foo");
        
        
        form_object.find(".dx-form-field-line[dx_fld_name_form=db_name]").find("input").after(flds_sel);
        form_object.find(".dx-form-field-line[dx_fld_name_form=db_name]").find("label").after(link);
        
        link.on("click", function(e) {
            e.stopImmediatePropagation();
            var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=db_name]");
            if (flds_sel.is(":visible")) {                
                hideFieldsCbo(fld_line);
                fld_line.find("input").focus();
            }
            else {
                showFieldsCbo(fld_line);
                
            }
        });
        
        var hideFieldsCbo = function(fld_line) {
            fld_line.find("input").prop("name", "db_name").prop("required", true).show();
            flds_sel.removeAttr("name").prop("required", false).hide();
            fld_line.find(".form-control-feedback").css("margin-right", "0px");
            link.text(Lang.get('constructor.link_fld_choose'));
                        
            clearValidation(fld_line);
        };
        
        var showFieldsCbo = function(fld_line) {
            
            fld_line.find("input").removeAttr("name").prop("required", false).hide();
            flds_sel.prop("name", "db_name").prop("required", true).show();
            fld_line.find(".form-control-feedback").css("margin-right", "10px");
            link.text(Lang.get('constructor.link_fld_custom'));
            link.show();
            
            clearValidation(fld_line);
        };
        
	var fill_db_name = function(val)
	{
            var list_id = form_object.find("[name=list_id]").val();

            var url = "constructor/db_fields/" + list_id + "/" + val;

            toastr.clear();
            show_form_splash();

            $.ajax({ 
                    type: 'GET',
                    url: DX_CORE.site_url  + url,
                    async:true,
                    success : function(data) {
                        var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=db_name]");
                        
                        var first_val = "";
                        
                        flds_sel.empty();
                        
                        if (parseInt(data.count) == 0) {
                            hideFieldsCbo(fld_line);
                            link.hide();
                        }
                        else {
                            showFieldsCbo(fld_line);
                        }
                            
                        if (parseInt(data.count) > 0) {
                            
                            $.each(JSON.parse(data.rows), function() {
                                
                                var opt = $("<option>");
                                opt.val(this.field_name);
                                opt.text(this.field_name);
                                opt.attr("data-max-len", this.max_length);
                                
                                flds_sel.append(opt);
                                if (first_val.length == 0) {
                                    first_val = this.field_name;
                                }
                            });
                        }
                        
                        if (first_val.length > 0) {                            
                            fld_line.find("[name=db_name]").val(first_val);
                        }
                        
                        clearValidation(fld_line);
                        
                        hide_form_splash();
                    }
            });
	};
        
        // remove validation info if it was set
        var clearValidation = function(fld_line) {
            fld_line.removeClass("has-error").removeClass("has-success");
            fld_line.find(".form-control-feedback").removeClass("glyphicon-alert").removeClass("glyphicon-ok");
            fld_line.find(".help-block").html('');
        };
        
        var setValidationError = function(fld_line, err_txt) {
            clearValidation(fld_line);
            fld_line.addClass("has-error");
            fld_line.find(".form-control-feedback").addClass("glyphicon-alert");
            fld_line.find(".help-block").html(err_txt);
        };
        
        var validateField = function() {            
            var sel_opt = flds_sel.find('option:selected', this);
                        
            if (sel_opt.length) {
               return validateMaxLen( sel_opt.attr('data-max-len'));
            }
            
            return true;
        };
	
        var validateMaxLen = function(max_len){
            max_len = parseInt(max_len);
            if (max_len == 0) {
                return true; // no restriction on length
            }
            
            var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=max_lenght]");
                
            var enter_len = parseInt(form_object.find("[name=max_lenght]").val());
            
            if (isNaN(enter_len) || enter_len > max_len) {
                notify_err(Lang.get('constructor.valid_max_len', {max_len: max_len}));
                setValidationError(fld_line, Lang.get('constructor.valid_max_err', {max_len: max_len}));
                return false;
            }

            if (form_object.find("[name=is_crypted]").prop('checked')==true) {
                // crypted field max len formula: (db_len - 32)/4
                var max_crypt = Math.floor((max_len - 32 )/4);
                var ok_len = enter_len*4 + 32;

                if (max_crypt < 1) {                        
                    notify_err(Lang.get('constructor.valid_cant_crypt'));
                    notify_err(Lang.get('constructor.valid_min_len', {ok_len: ok_len}));
                    setValidationError(fld_line, Lang.get('constructor.valid_not_possible_crypt'));
                    return false;
                }

                if (max_crypt < enter_len) {
                    notify_err(Lang.get('constructor.valid_crypt_max_len', {max_crypt: max_crypt}));
                    notify_err(Lang.get('constructor.valid_min_len', {ok_len: ok_len}));
                    setValidationError(fld_line, Lang.get('constructor.valid_max_err', {max_len: max_crypt}));
                    return false;
                }
            }
            
            return true;
        };
        
	var change_event = function(e)
	{
            if (e)
            {
                    fill_db_name($(this).val());
            }
            else
            {
                    fill_db_name(cur_val);
            }
	};

	//change_event(null);
	
	sel.on('change', change_event);
        
        form_object.find(".dx-btn-save-form").click(function(e) {	
            if (!validateField()) {
                    e.stopImmediatePropagation();
            }
        });
}
