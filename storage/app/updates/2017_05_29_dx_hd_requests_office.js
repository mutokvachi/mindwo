var sel = form_object.find("[name=item_id]");
if (sel)
{
	if (sel.val() === "0") {
		form_object.find(".dx-form-field-line[dx_fld_name_form=reporter_empl_id]").closest(".dx-form-field-line").removeClass("col-lg-4").addClass("col-lg-6");
		form_object.find(".dx-form-field-line[dx_fld_name_form=request_time]").closest(".dx-form-field-line").removeClass("col-lg-4").addClass("col-lg-6");
	}
	else {
		form_object.find(".dx-form-field-line[dx_fld_name_form=reporter_empl_id]").closest(".dx-form-field-line").removeClass("col-lg-4").addClass("col-lg-6");
		form_object.find(".dx-form-field-line[dx_fld_name_form=id]").closest(".dx-form-field-line").removeClass("col-lg-4").addClass("col-lg-2");
	}
}