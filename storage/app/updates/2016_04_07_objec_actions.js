var btns_div = form_object.find(".dx_form_btns_left");

var is_gener = $("#generate_" + form_object.attr('id')).length;

if (btns_div && is_gener == 0)
{
	btns_div.append( "<div class='btn-group'><button  type='button' class='btn btn-white dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fa fa-cog'></i> Darbības <i class='fa fa-caret-down'></i></button><ul class='dropdown-menu'><li><a href='#' id='generate_" + form_object.attr('id') + "'>Ģenerēt reģistru</a></li><li><a href='#' id='copy_" + form_object.attr('id') + "'>Kopēt reģistru</a></li><li><a href='#' id='delete_" + form_object.attr('id') + "'>Dzēst reģistru</a></li><li><a href='#' id='audit_" + form_object.attr('id') + "'>Ģenerēt auditāciju</a></li></ul></div>" );
	
	$("#generate_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/register_generate';
		var item_title = 'Reģistra ģenerēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#delete_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/register_delete';
		var item_title = 'Reģistra dzēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#copy_" + form_object.attr('id')).click(function(){
		var item_id = form_object.find("input[name=id]").val();
		var item_url = '/structure/form/register_copy';
		var item_title = 'Reģistra kopēšana';
		
		get_popup_item_by_id(item_id, item_url, item_title);
		
	});
	
	$("#audit_" + form_object.attr('id')).click(function(){
		var item_url = '/structure/form/generate_audit';
		var item_title = 'Auditācijas ģenerēšana';
		
		get_popup_item_by_id(0, item_url, item_title);
		
	});
}