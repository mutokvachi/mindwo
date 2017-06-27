var arr_meta_flds = ["job_place_kind", "mobile_kind", "mobilly_kind", "subj_employee_id", "phone_nr"];

form_object.find(".dx-btn-save-form").click(function(e) {	
	if (!validateMeta()) {
		e.stopImmediatePropagation();
	}
});


form_object.find(".dx-form-field-line[dx_fld_name_form=request_type_id]").find(".dx-tree-btn-del").click(function() {
	hideAllFields();
});

form_object.find(".dx-form-field-line[dx_fld_name_form=request_type_id]").find(".dx-tree-btn-add").click(function() {
	$('#tree_form').on('hidden.bs.modal', function () {  
		show_ir_hide_meta_fields(form_object.find("input[name=request_type_id]").val());  	
	});
});

var addRedAsteric = function() {
	
	for (var i=0; i<arr_meta_flds.length; i++) {
		var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=" + arr_meta_flds[i] + "]");
		fld_line.find(".dx-fld-title").after('<span style="color: red"> *</span>');
	}
};

var validateMeta = function() {
	
	for (var i=0; i<arr_meta_flds.length; i++) {
		var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=" + arr_meta_flds[i] + "]");
		
		if (fld_line.is(":visible")) {
			
			var is_err = 0;
			if (arr_meta_flds[i] === "subj_employee_id") {
				if (!parseInt(fld_line.find("[name=" + arr_meta_flds[i] + "]").val())) {
					is_err = 1;
				}
			}
			else {
				if (!fld_line.find("[name=" + arr_meta_flds[i] + "]").val().trim()) {
					is_err = 1;
				}
			}
			
			if (is_err) {
				notify_err("Ir obligāti jānorāda lauka '" + fld_line.find(".dx-fld-title").text() + "' vērtība!");
				return false;
			}
		}
	}
	
	return true;

};

var hideAllFields = function() {
	for (var i=0; i<arr_meta_flds.length; i++) {
		fieldDisplay(arr_meta_flds[i], false);
	}
};

var show_ir_hide_meta_fields = function(val)
{  	
	if (val == 0) {
		hideAllFields();
      	hide_form_splash();
		return;    
	}
	
	var url = "/api/view/529/data/raw/id/" + val;

	toastr.clear();
	show_form_splash();
	
	$.ajax({ 
		type: 'GET',
		url: DX_CORE.site_url  + url,
		async:true,
		success : function(data) {
			$.each(JSON.parse(data.rows), function() {
				fieldDisplay("job_place_kind", this.is_work_place);
				fieldDisplay("mobile_kind", this.is_mobile);
				fieldDisplay("mobilly_kind", this.is_mobilly);
				fieldDisplay("subj_employee_id", this.is_empl);
				fieldDisplay("phone_nr", this.is_mobnr);
			});
							
			hide_form_splash();
		}
	});
};

var fieldDisplay = function(fld_name, is_visible) {
  	
	var fld_line = form_object.find(".dx-form-field-line[dx_fld_name_form=" + fld_name + "]");
	
	if (parseInt(is_visible)) {
		fld_line.show();
	}
	else {
		fld_line.hide();
		if (fld_name === "subj_employee_id") {
			clearEmpl(fld_line);
		}
		else {
			fld_line.find("[name=" + fld_name + "]").val('');
		}
	}
};

var clearEmpl = function(fld_line) {
	var fld_elem = fld_line.find(".dx-autocompleate-field");
	fld_elem.find('.dx-auto-input-select2').select2('data', {id:0, text:""});
	fld_elem.find("input.dx-auto-input-id").val(0);
	
	if (fld_elem.data("is-profile")) {
		fld_elem.find(".dx-rel-id-add-btn").hide();
	}
};

addRedAsteric();

show_ir_hide_meta_fields(form_object.find("input[name=request_type_id]").val());
