var field_obj = form_object.find("[name=field_id]");
var field_val = field_obj.val();

if (field_obj.attr('type') != 'hidden')
{
  var view_id = form_object.find("[name=view_id]").val();
  var prev_form = get_previous_form_by_list(form_object.attr('id'), view_id);

  var list_id = $('#' + prev_form).find("[name=list_id]").val();

  var list_sel = form_object.find("[name=list_id]");

  if (list_sel.val()=="0") {
  	list_sel.val(list_id);
  }
  
  var field_val = field_obj.val();
  if (field_val == 0)
  {  
  	load_binded_field(list_sel.attr('id'), field_obj.attr('id'), list_sel.attr('dx_binded_field_id'), list_sel.attr('dx_binded_rel_field_id'));
  }
}