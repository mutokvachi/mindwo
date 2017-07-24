var btns_div = form_object.find(".dx_form_btns_left");

var make_button = function() {

	if ($("#btn_group_" + form_object.attr('id')).length != 0) {
		return; // poga jau ir pievienota
	}
	
	btns_div.append( "<div class='btn-group' style='margin-left: 5px;' id='btn_group_" + form_object.attr('id') + "'><button  type='button' class='btn btn-white dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fa fa-cog'></i> " + Lang.get('db_dx_lists.menu_operations') + " <i class='fa fa-caret-down'></i></button><ul class='dropdown-menu' style='z-index: 5000;'><li><a href='javascript:;' id='copy_" + form_object.attr('id') + "'>" + Lang.get('db_dx_lists.menu_copy_view') + "</a></li><li><a href='javascript:;' id='gen_view_" + form_object.attr('id') + "'>" + Lang.get('db_dx_lists.menu_generate_view') + "</a></li><li><a href='javascript:;' id='delete_" + form_object.attr('id') + "'>" + Lang.get('db_dx_lists.menu_del_view') + "</a></li><li><a href='javascript:;' id='fld_delete_" + form_object.attr('id') + "'>" + Lang.get('db_dx_lists.menu_del_field') + "</a></li><li><a href='javascript:;' id='copy_wf" + form_object.attr('id') + "'>" + Lang.get('db_dx_lists.menu_copy_wf') + "</a></li></ul></div>" );
	
	$("#copy_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/view_copy';
		var item_title = Lang.get('db_dx_lists.menu_copy_view');
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
        
        $("#gen_view_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/view_generate';
		var item_title = Lang.get('db_dx_lists.menu_generate_view');
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#copy_wf" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/wf_copy';
		var item_title = Lang.get('db_dx_lists.menu_copy_wf');
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#delete_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/view_delete';
		var item_title = Lang.get('db_dx_lists.menu_del_view');
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#fld_delete_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/field_delete';
		var item_title = Lang.get('db_dx_lists.menu_del_field');
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
};

if (btns_div)
{
	make_button();
}