/**
 * Loads grid's HTML into given DIV's content
 * This function is triggered by left side menu item click
 * 
 * @param   string  grid_data_htm_id   DIV's html ID, where grid's HTML will be loaded
 * @param   int     list_id            Grid's coresponding list (register) ID - from the table dx_lists
 * @param   int     view_id            Grid's list view ID. If 0, then default view will be loaded
 */
function load_grid(grid_data_htm_id, list_id, view_id)
{
    var formData = new FormData();
    formData.append("list_id", list_id);
    formData.append("view_id", view_id);
    formData.append("grid_data_htm_id", grid_data_htm_id);
    formData.append("page_nr", 1);
    formData.append("page_row_count", 0);
    
    post_grid_ajax(formData, grid_data_htm_id, "", 0);	
}

/**
 * Reloads allready loaded grid's content
 * This function is triggered by grids filtering, paginator or reload button
 * 
 * @param   string     grid_id            Grid's DIV id
 */
function reload_grid_by_id(grid_id)
{
    var filter_data = get_filter_data(grid_id);
    var grid_data_htm_id = $("#" + grid_id).data("grid_data_htm_id");
    
    var formData = new FormData();
    formData.append("list_id", $("#" + grid_id).data("list_id"));
    formData.append("view_id", $("#" + grid_id).data("view_id"));
    formData.append("grid_data_htm_id", grid_data_htm_id);
    formData.append("page_nr", $("#" + grid_id).data("grid_page_nr"));
    formData.append("page_row_count", $("#" + grid_id).data("grid_rows_in_page"));
    formData.append("grid_id", grid_id);
    formData.append("filter_data", filter_data);
    formData.append("sorting_field", $("#" + grid_id).data("sorting_field"));
    formData.append("sorting_direction", $("#" + grid_id).data("sorting_direction"));
    
    var view_container = $("#" + grid_id).closest(".dx-block-container-view");
    
    var report_date_from = view_container.find("input[name=dx_filter_date_from]");
    if (report_date_from.length > 0) {
        formData.append("dx_filter_date_from", report_date_from.val());
    }
    
    var report_date_to = view_container.find("input[name=dx_filter_date_to]");
    if (report_date_to.length > 0) {
        formData.append("dx_filter_date_to", report_date_to.val());
    }
    
    if (report_date_from.length > 0 || report_date_to.length > 0) {
        formData.append("dx_filter_field_id", view_container.data("filter-field-id"));
    }
    
    post_grid_ajax(formData, grid_data_htm_id, "", 0);    
}

/**
 * Reloads allready loaded grid's content
 * This function is triggered by grids filtering or paginator or reload button
 * We have 2 kind of grids - loaded in the main page or loaded in form's tab section
 * 
 * @param   string     grid_id            Grid's DIV id
 */
function reload_grid(grid_id)
{        
    if ($("#" + grid_id).data("tab_id"))
    {
            reload_tab_grid(grid_id);
            return;
    }

    reload_grid_by_id(grid_id);
}

function load_tab_grid(tab_id, list_id, view_id, rel_field_id, rel_field_value, form_htm_id, page_nr, page_row_count, is_scroll)
{    
    var formData = new FormData();
    formData.append("list_id", list_id);
    formData.append("tab_id", tab_id);
    formData.append("view_id", view_id);
    formData.append("rel_field_id", rel_field_id);
    formData.append("rel_field_value", rel_field_value);
    formData.append("form_htm_id", form_htm_id);
    formData.append("page_nr", page_nr);
    formData.append("page_row_count", page_row_count);
    
    post_grid_ajax(formData, tab_id, form_htm_id, is_scroll);      
}

function post_grid_ajax(formData, grid_data_htm_id, form_htm_id, is_scroll)
{    
    $.ajax({ 
       type: 'POST',
       url: DX_CORE.site_url  + "grid",
       data: formData,
       processData: false,
       contentType: false,
       dataType: "json",
       success : function(data) {
            try
            {
                var myData = data;
                if (myData['success'] == 1)
                { 
                    $("#" + grid_data_htm_id).html(myData['html']);
                                        
                    if (is_scroll == 1)
                    {
                        var d = $("#" + form_htm_id).find(".modal-body");

                        d.scrollTop(d.prop("scrollHeight"));
                    }
                    
                    setTimeout(function(){ 
                        PageMain.resizePage();                     
                    }, 100);
                    
                    // Commented out because it prevents main navigation from working properly on mobiles
                    /*
                    setTimeout(function(){ 
                        $('.dropdown-toggle').dropdown();                        
                    }, 1000);
                    */
                } 
                else
                {
                    notify_err(myData['error']);
                }
            }
            catch (err)
            {                
                notify_err(escapeHtml(err));                
            }
            hide_page_splash();
            hide_form_splash();
       },
       beforeSend: function () {
            show_form_splash();
            show_page_splash();
       },
       complete: function () {
           hide_form_splash();
           hide_page_splash(); 
       }
   }); 
}

function reload_tab_grid(grid_id)
{
	var filter_data = get_filter_data(grid_id);
	
	var rel_field_id = $("#" + grid_id).data("rel_field_id");
	var rel_field_value = $("#" + grid_id).data("rel_field_value");
	var form_htm_id = $("#" + grid_id).data("form_htm_id");
	
	var grid_post_id = grid_id;
	
	if (rel_field_id > 0 && rel_field_value == 0)
	{
		rel_field_value = $( "#" + form_htm_id  +" input[name='item_id']").val();
		
		if (rel_field_value == 0)
		{
			notify_err(Lang.get('errors.first_save_for_related'));
			return;
		}

		grid_post_id = ""; // we need completely reload the grid and do not use SQL saved in session
	}
        
        var formData = new FormData();
        formData.append("list_id", $("#" + grid_id).data("list_id"));
        formData.append("tab_id", $("#" + grid_id).data("tab_id"));
        formData.append("view_id", $("#" + grid_id).data("view_id"));
        formData.append("rel_field_id", rel_field_id);
        formData.append("rel_field_value", rel_field_value);
        formData.append("form_htm_id", form_htm_id);
        formData.append("page_nr", $("#" + grid_id).data("grid_page_nr"));
        formData.append("page_row_count", $("#" + grid_id).data("grid_rows_in_page"));
        formData.append("sorting_field", $("#" + grid_id).data("sorting_field"));
        formData.append("sorting_direction", $("#" + grid_id).data("sorting_direction"));
        formData.append("grid_id", grid_post_id);
        formData.append("filter_data", filter_data);
        
        post_grid_ajax(formData, $("#" + grid_id).data("tab_id"), form_htm_id, 1);
        
}

function get_filter_data(grid_id)
{
	var arr_filter = new Array();

	$("#filter_" + grid_id + " input").each(function(i, obj) {
		if (obj.value != "")
		{
			var el = new Array();
			el.push(obj.getAttribute('sql_name'));
			el.push(obj.value);
			el.push(obj.getAttribute('field_id'));
                        
			arr_filter.push(el);		
		}
	});
	
	return JSON.stringify(arr_filter);
}
