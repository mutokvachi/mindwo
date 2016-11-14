var btns_div = form_object.find(".dx_form_btns_left");

var make_button = function() {

	if ($("#btn_group_" + form_object.attr('id')).length != 0) {
		return; // poga jau ir pievienota
	}
	
	btns_div.append( "<div class='btn-group' style='margin-left: 5px;' id='btn_group_" + form_object.attr('id') + "'><button  type='button' class='btn btn-white dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fa fa-cog'></i> Darbības <i class='fa fa-caret-down'></i></button><ul class='dropdown-menu'><li><a href='#' id='copy_" + form_object.attr('id') + "'>Kopēt skatu</a></li><li><a href='#' id='gen_view_" + form_object.attr('id') + "'>Ģenerēt skatu</a></li><li><a href='#' id='delete_" + form_object.attr('id') + "'>Dzēst skatu</a></li><li><a href='#' id='fld_delete_" + form_object.attr('id') + "'>Dzēst lauku</a></li><li><a href='#' id='copy_wf" + form_object.attr('id') + "'>Kopēt darbplūsmu</a></li></ul></div>" );
	
	$("#copy_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/view_copy';
		var item_title = 'Skata kopēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
        
        $("#gen_view_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/view_generate';
		var item_title = 'Skata  ģenerēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#copy_wf" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/wf_copy';
		var item_title = 'Darbplūsmas kopēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#delete_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/view_delete';
		var item_title = 'Skata dzēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#fld_delete_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/field_delete';
		var item_title = 'Lauka dzēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
};

if (btns_div)
{
	make_button();
}