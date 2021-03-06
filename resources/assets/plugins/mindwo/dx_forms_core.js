function load_binded_field(obj_parent, obj_child, binded_field_id, binded_rel_field_id)
{
    var formData = new FormData();
    formData.append("binded_field_id", binded_field_id);
    formData.append("binded_rel_field_id", binded_rel_field_id);
    formData.append("binded_rel_field_value", $("#" + obj_parent).val());
        
    $.ajax({ 
       type: 'POST',
       url: DX_CORE.site_url  + "load_binded_field",
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
                    $("#" + obj_child).empty();
	            $("#" + obj_child).append(myData['data']);
                    $("#" + obj_child).val(0);
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
       },
       beforeSend: function () {
           show_dx_progres(DX_CORE.trans_data_processing);
       },
       complete: function () {
           hide_dx_progres(); 
       }
   });
}

function new_list_item(list_id, rel_field_id, rel_field_value, form_htm_id, grid_htm_id, arr_callbacks)
{	
        if (list_id == 0)
	{
		notify_err(DX_CORE.trans_general_error + " The List ID not provided!");
		
		return;
	}

	if (rel_field_id > 0 && rel_field_value == 0)
	{
		rel_field_value = $( "#" + form_htm_id  +" input[name='item_id']").val();
		
		if (rel_field_value == 0)
		{
			notify_err(DX_CORE.trans_first_save_msg);
			return;
		}
	}

	view_list_item("form", 0, list_id, rel_field_id, rel_field_value, grid_htm_id, form_htm_id, arr_callbacks);
}

/**
 * Opens list item form - gets form's HTML in JSON and opens bootstrap modal form (in view or new entry entering mode - depending in item_id value)
 * After save form is reloaded in view mode.
 * 
 * @param {string} ajax_url             Relative URL for forms opening. Must be defined Laravel route.
 * @param {integer} item_id             List item ID. If 0, then form will be opened in new entry entering mode
 * @param {integer} list_id             List ID
 * @param {integer} rel_field_id        Used for sub-grids forms opening - field ID by which subgrid is joined (or 0 if no subgrid)
 * @param {integer} rel_field_value     Used for sub-grids forms opening - related item ID (or 0 if no subgrid)
 * @param {string} grid_htm_id          Grid HTML element ID, from which form is opened
 * @param {string} parent_form_htm_id   Parent form HTML element ID, if form is opened from an subgrid (which is placed in parent form)
 * @param {object} arr_callbacks        Callback functions object, for example: {before_show: callback1, after_close: callback2, before_save: callback3, after_save: callback4}. Before_save callback is used for pre-validation and can return True or False.
 * @returns {undefined}
 */
function view_list_item(ajax_url, item_id, list_id, rel_field_id, rel_field_value, grid_htm_id, parent_form_htm_id, arr_callbacks)
{		
	if (is_executing(grid_htm_id))
	{
            return;
	}
        
        show_page_splash();
        show_form_splash();
        
	start_executing(grid_htm_id);
        
        var formData = new FormData();
        formData.append("item_id", item_id);
        formData.append("list_id", list_id);
        formData.append("parent_item_id", rel_field_value);
        formData.append("parent_field_id", rel_field_id);
        formData.append("grid_htm_id", grid_htm_id);

        var request = new FormAjaxRequest (ajax_url, "", grid_htm_id, formData);

        request.progress_info = "";
        request.err_callback = function() {
            stop_executing(grid_htm_id);
        }
        
        request.callback = function(data) {
            
            if (data['success'] == 1)
            {                  
                if (typeof arr_callbacks != 'undefined') {
                    add_form_callbacks(data['frm_uniq_id'], arr_callbacks);
                }
                
                if (data['is_fullscreen'] == "1") {
                    $("#td_data").hide();
                    $("#td_form_data").html(data['html']);
                    
                    if (typeof HFormUI != 'undefined') {
                        HFormUI.init(grid_htm_id);
                    }
                }
                else {
                    $( "body" ).append(data['html']);
                }
            } 
            else
            {
               notify_err(data['error']);
               stop_executing(grid_htm_id);
            }
                    
            hide_form_splash(1);
            hide_page_splash(1);
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
}

/**
 * Opens list item form - gets form's HTML in JSON and opens bootstrap modal form (in view or new entry entering mode - depending in item_id value and form_is_edit_mode option)
 * 
 * @param {string} ajax_url             Relative URL for forms opening. Must be defined Laravel route.
 * @param {integer} item_id             List item ID. If 0, then form will be opened in new entry entering mode
 * @param {integer} list_id             List ID
 * @param {integer} rel_field_id        Used for sub-grids forms opening - field ID by which subgrid is joined (or 0 if no subgrid)
 * @param {integer} rel_field_value     Used for sub-grids forms opening - related item ID (or 0 if no subgrid)
 * @param {string} grid_htm_id          Grid HTML element ID, from which form is opened
 * @param {boolean} form_is_edit_mode   Indicates if form for existing item is opened in edit mode (1) or view mode (0)
 * @param {string} parent_form_htm_id   Parent form HTML element ID, if form is opened from an subgrid (which is placed in parent form)
 * @param {object} arr_callbacks        Callback functions object, for example: {before_show: callback1, after_close: callback2, before_save: callback3, after_save: callback4}. Before_save callback is used for pre-validation and can return True or False.
 * @returns {undefined}
 */
function open_form(ajax_url, item_id, list_id, rel_field_id, rel_field_value, grid_htm_id, form_is_edit_mode, parent_form_htm_id, arr_callbacks)
{            
        var formData = new FormData();
        formData.append("item_id", item_id);
        formData.append("list_id", list_id);
        formData.append("parent_item_id", rel_field_value);
        formData.append("parent_field_id", rel_field_id);
        formData.append("grid_htm_id", grid_htm_id);
        formData.append("form_is_edit_mode", form_is_edit_mode);
        formData.append("parent_form_htm_id", parent_form_htm_id);
        
        if (parent_form_htm_id.length > 0) {
            var parent_form = $("#" + parent_form_htm_id);
            var elem = parent_form.find("input[name=call_field_htm_id]");
            if (elem.length > 0) {
                formData.append("call_field_htm_id", elem.val());                
                formData.append("call_field_id", parent_form.find("input[name=call_field_id]").val());
                formData.append("call_field_type", parent_form.find("input[name=call_field_type]").val());
            }            
        }
        
        $.ajax({ 
            type: 'POST',
            url: DX_CORE.site_url  + ajax_url,
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
                        if (typeof arr_callbacks != 'undefined') {
                            add_form_callbacks(data['frm_uniq_id'], arr_callbacks);
                        }
                        $( "body" ).append(myData['html']);				
                    } 
                    else
                    {
                       notify_err(myData['error']);
                    }
                }
                catch (err)
                {
                   notify_err(err);
                }
                hide_form_splash();
                hide_page_splash();
            },
            beforeSend: function () {
                show_form_splash();
                show_page_splash();
            }
        });
}

// Šo funkciju izmanto, lai atvērtu formu no autocompleate lauka pogas vai dropdown lauka pogas nospiešanas (tātad, te nav GRIDa objekts)
function rel_new_form(ajax_url, list_id, item_id, call_field_id, call_field_htm_id, call_field_type)
{
        var formData = new FormData();
        formData.append("item_id", item_id);
        formData.append("list_id", list_id);
        formData.append("call_field_id", call_field_id);
        formData.append("call_field_htm_id", call_field_htm_id);
        formData.append("call_field_type", call_field_type);
        /*
        if (item_id > 0) {
            formData.append("form_is_edit_mode", 1);
        }
        */
        $.ajax({ 
            type: 'POST',
            url: DX_CORE.site_url  + ajax_url,
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
                         $( "body" ).append(myData['html']);				
                     } 
                     else
                     {
                        notify_err(myData['error']);
                     }
                 }
                 catch (err)
                 {
                    notify_err(err);
                 }
                 hide_form_splash();
            },
            beforeSend: function () {
                show_form_splash();
            }
        });
}

function refresh_form_fields(edit_form_htm_id, form_htm_id, item_id, list_id, rel_field_id, rel_field_value, arr_callbacks)
{
    var formData = new FormData();
    formData.append("item_id", item_id);
    formData.append("list_id", list_id);
    formData.append("parent_field_id", rel_field_id);
    formData.append("parent_item_id", rel_field_value);
    
    var frm_uniq_id = $("#" + form_htm_id).find("div[dx_attr=form_fields]").attr("dx_form_id");
    formData.append("frm_uniq_id", frm_uniq_id);
    
    if (edit_form_htm_id == form_htm_id)
    {
        formData.append("form_is_edit_mode", 1);
    }
    
    $.ajax({ 
        type: 'POST',
        url: DX_CORE.site_url  + "refresh_form",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        async:true,
        success : function(data) {
            
            try
             {
                 var myData = data;
                 if (myData['success'] == 1)
                 {                    
                    var div_fields = $("#" + form_htm_id).find("div[dx_attr=form_fields]");
                    
                    div_fields.html(myData['html']);
                    div_fields.attr('dx_is_init', 0);
                    
                    jQuery.each(myData['tabs'], function(id, htm) {                        
                        $("#" + form_htm_id).find("div[dx_tab_id=" + id + "]").html(htm);                        
                    });
                    
                    var callback_result = true;
                    if (typeof arr_callbacks != 'undefined') {
                        if (typeof arr_callbacks.after_save != 'undefined') {
                            callback_result = arr_callbacks.after_save.call(this, $("#list_item_view_form_" + frm_uniq_id));                           
                        }
                    }
                    
                    if (callback_result) {
                        // Lets execute again custom javascript so they applay to refreshed fields
                        var script = $("body").find("div[dx_attr='" + form_htm_id + "']");
                        if (script.html())
                        {
                            var tmp = script.html();
                            tmp = tmp.replace('<script type="text/javascript">');
                            tmp = tmp.replace('</script>');
                            eval(tmp);
                        }

                        FormLogic.adjustDataTabs($("#item_edit_form_" + frm_uniq_id).find(".dx-cms-form-fields-section").first());                                       

                        var footer = $("#" + form_htm_id).find(".modal-footer");
                        footer.find(".dx-history-badge").html(myData['history_count']);

                        if (myData['history_count']) {
                            footer.find(".dx-history-badge").show();
                        }
                    }
                    
                    notify_info(DX_CORE.trans_data_saved);
                    stop_executing(edit_form_htm_id);

                    if (edit_form_htm_id != form_htm_id)
                    {
                        $('#' + edit_form_htm_id).parent().parent().parent().parent().parent().modal('hide');
                    }
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
             
             hide_form_splash(1);
             hide_page_splash(1);
        },
        beforeSend: function () {
            show_form_splash();
            show_page_splash();
        }
    });
}

function reload_edited_form(ajax_url, item_id, list_id, rel_field_id, rel_field_value, grid_htm_id, old_form_htm_id, arr_callbacks)
{
    var formData = new FormData();
    formData.append("item_id", item_id);
    formData.append("list_id", list_id);
    formData.append("parent_item_id", rel_field_value);
    formData.append("parent_field_id", rel_field_id);
    formData.append("grid_htm_id", grid_htm_id);
    formData.append("is_form_reloaded", 1);
    
    var request = new FormAjaxRequest (ajax_url, "", grid_htm_id, formData);
    
    request.progress_info = "";
    request.callback = function(data) {
        show_form_splash();
        
        var new_form_guid = data["frm_uniq_id"];
        var re = new RegExp(new_form_guid, 'g');
        var htm = data['html'].replace(re, old_form_htm_id);
        
        var height_content  = $("#list_item_view_form_" + old_form_htm_id).find(".modal-body").height();
            
        unregister_form("list_item_view_form_" + old_form_htm_id);
        
        if (typeof arr_callbacks != 'undefined') {
            add_form_callbacks(old_form_htm_id, arr_callbacks);
        }
        
        $("#list_item_view_form_" + old_form_htm_id).find(".modal-content").html(htm);

        if (get_last_form_id() != ("list_item_view_form_" + old_form_htm_id)) {
            var dom = $(htm);
            dom.find("script").each(function() {
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });

            dom.filter('script').each(function(){           
                $.globalEval(this.text || this.textContent || this.innerHTML || '');
            });
        }
        
        var callback_result = true;
        if (typeof arr_callbacks != 'undefined') {
            if (typeof arr_callbacks.after_save != 'undefined') {
                callback_result = arr_callbacks.after_save.call(this, $("#list_item_view_form_" + old_form_htm_id));                           
            }
        }
        
        if (callback_result) {  
            var tool_height = $("#top_toolbar_list_item_view_form_" + old_form_htm_id).height();

            $("#list_item_view_form_" + old_form_htm_id).find(".modal-body").height(height_content - tool_height-21);

            var height = $(window).height()*DX_CORE.form_height_ratio;
            var form_body = $("#list_item_view_form_" + old_form_htm_id).find(".modal-body");

            form_body.css('overflow-y', 'auto');    

            var scroll_height = form_body.find(".dx-form-row")[0].scrollHeight;
            var heading_height = $("#list_item_view_form_" + old_form_htm_id).find(".modal-header").outerHeight();
            var footer_height = $("#list_item_view_form_" + old_form_htm_id).find(".modal-header").outerHeight();

            if (scroll_height > (height_content - tool_height - 21) && scroll_height < (height - tool_height - heading_height - footer_height)) {
                form_body.height(scroll_height);
            }
            else {
                form_body.height(height_content);
            }

            form_body.css('max-height', height + 'px');

            FormLogic.adjustDataTabs($("#item_edit_form_" + old_form_htm_id).find(".dx-cms-form-fields-section").first());
        }        
                    
        notify_info(DX_CORE.trans_data_saved);
        hide_form_splash(1);
    };
    
    // izpildam AJAX pieprasījumu
    request.doRequest();
}

 /*
 * Ieraksta saglabāšana reģistrā. 
 * 
 * @param   post_form_htm_id        string      Formas FORM elementa HTML identifikators
 * @param   grid_htm_id             string      Reģistra tabulārā skata HTML objekta identifikators - nepieciešams lai atjauninātu saraksta datus
 * @param   list_id                 integer     Reģistra identifikators
 * @param   rel_field_id            integer     Saistītā ieraksta lauka identifikators (ja forma ir atvērta no apakšgrida)
 * @param   rel_field_value         integer     Saistītā ieraksta identifikators (ja forma ir atvērta no apakšgrida)
 * @param   parent_form_htm_id      string      Formas skatīšanās režīms - no kuras tika atvērta šī rediģēšanas režīma forma
 * @param   arr_callbacks           object      Callback funkcijas kā objekts, piemēram {before_show: callback1, after_close: callback2, before_save: callback3, after_save: callback4} 
 * @return  void
 */ 
function save_list_item(post_form_htm_id, grid_htm_id, list_id, rel_field_id, rel_field_value, parent_form_htm_id, arr_callbacks)
{   
    
    var formData = process_data_fields(post_form_htm_id);
    
    if (formData == null) {
        return;
    }
    
    show_form_splash(1);
    
    var request = new FormAjaxRequest ("save_form", post_form_htm_id, grid_htm_id, formData);
    
    request.progress_info = "";
    request.callback = function(data) {
        
        show_form_splash();
        if (grid_htm_id)
        {
            reload_grid(grid_htm_id);
        }
        
        if (data['call_field_htm_id']) {
            if (data['call_field_type'] == "RelIdField") {
                RelIdField.update_callback(data['call_field_htm_id'], data['id'], data['call_field_value']);
            }
            else if (data['call_field_type'] == "AutocompleateField") {
                AutocompleateField.update_callback(data['call_field_htm_id'], data['id'], data['call_field_value']);
            }
            else {
                notify_error("Unsuported field type '" + data['call_field_htm_id'] + "'");
            }
            
            hide_form_splash(1);
            hide_page_splash(1);
            $( "#" + post_form_htm_id).parent().parent().parent().parent().parent().modal('hide');
            notify_info(DX_CORE.trans_data_saved);
            return;
        }  
            
        if (parent_form_htm_id)
        {  
            refresh_form_fields(post_form_htm_id, parent_form_htm_id, data['id'], list_id, rel_field_id, rel_field_value, arr_callbacks);                            
        }
        else
        { 
            var form_html_uniq_id = $( "#" + post_form_htm_id).parent().parent().parent().parent().parent().attr("id").replace("list_item_view_form_","");
                        
            reload_edited_form('form', data['id'], list_id, rel_field_id, rel_field_value, grid_htm_id, form_html_uniq_id, arr_callbacks);
        }
    };
    
    // izpildam AJAX pieprasījumu
    request.doRequest();
}

/**
 * Sagatavo saglabāšanai visus datu ievades laukus
 * 
 * @param {string} post_form_htm_id HTML formas elementa id
 * @returns {Object} Masīvs ar saglabājamiem datu laukiem
 */
function process_data_fields(post_form_htm_id) {
    
    tinyMCE.triggerSave();
    
    var formData = new FormData();
    
    if (!process_Input_simple(post_form_htm_id, formData)) {
        hide_page_splash(1);
        return null;
    }
    
    if (!process_dropzone(post_form_htm_id, formData)) {
        hide_page_splash(1);
        return null;
    }
    
    process_TextArea_Select(post_form_htm_id, formData);

    process_Input_radio(post_form_htm_id, formData);
    process_Input_checkbox(post_form_htm_id, formData);
    
    return formData;
}

/**
 * Sagatavo saglabāšanai datu ievades laukus - checkbox un radio
 * 
 * @param {string} post_form_htm_id HTML formas elementa id
 * @param {Object} formData Masīvs ar saglabājamiem datiem
 * @returns {undefined}
 */
function process_Input_radio(post_form_htm_id, formData) {
    $('#' + post_form_htm_id).find(':radio:checked').each(function (key, obj) {
        formData.append(obj.name, obj.value);
    });
}

/**
 * Sagatavo saglabāšanai datu ievades laukus - checkbox
 * 
 * @param {string} post_form_htm_id HTML formas elementa id
 * @param {Object} formData Masīvs ar saglabājamiem datiem
 * @returns {undefined}
 */
function process_Input_checkbox(post_form_htm_id, formData) {
    $('#' + post_form_htm_id).find('input.dx-bool').each(function (key, obj) {
        if ($(this).prop('checked')==true){
            formData.append(obj.name, 1);
        }
        else {
            formData.append(obj.name, 0);
        }
    });
}

/**
 * Sagatavo saglabāšanai datu ievades laukus - textarea un select elementiem
 * 
 * @param {string} post_form_htm_id HTML formas elementa id
 * @param {Object} formData Masīvs ar saglabājamiem datiem
 * @returns {unresolved}
 */
function process_TextArea_Select(post_form_htm_id, formData) {
    $('#' + post_form_htm_id + ' select, #' + post_form_htm_id + ' textarea').each(function(key, obj) {
        formData.append(obj.name, obj.value);
    });
    return formData;
}

/**
 * Sagatavo saglabāšanai datu ievades laukus (izņemot textarea, select, radio un checkbox)
 * 
 * @param {string} post_form_htm_id HTML formas elementa id
 * @param {Object} formData Masīvs ar saglabājamiem datiem
 * @returns {Boolean}
 */
function process_Input_simple(post_form_htm_id, formData){
    var is_nonvalid_file = 0;
    $('#' + post_form_htm_id).find(':input:not(:checkbox, :radio)').each(function (key, obj) {
        
        if (is_nonvalid_file == 1) {
            return;
        }
        
        if (obj.type == "file")
        {            
            
            if (obj.files[0])
            {                
                
                if (!validateFile(obj.files[0])) {
                    is_nonvalid_file = 1;
                    
                    return;
                }
                
                if ($(obj).hasClass('dx-crypto-field-file')) {
                    if ($(obj).data('is-decrypted') == 0) {
                        var cryptoVal = $(obj).data('crypto-value');

                        formData.append(obj.name, cryptoVal, obj.files[0].name);
                    }
                } else {
                    formData.append(obj.name, obj.files[0]);
                }
            }
        }
        else
        {
             if (obj.name.length > 0)
             {
                formData.append(obj.name, obj.value);
            }
        }
    });
    
    if (is_nonvalid_file == 1) {
        return false;
    }
    else {
        return true;
    }
}

/**
 * Sagatavo saglabāšanai datnes, kas pievienotas ar velc&palaid komponenti
 * 
 * @param {string} post_form_htm_id HTML formas elementa id
 * @param {Object} formData Masīvs ar saglabājamiem datiem
 * @returns {Boolean}
 */
function process_dropzone(post_form_htm_id, formData) {
    var is_nonvalid_file = 0;
    $('#' + post_form_htm_id).find('.dropzone').each(function (key, obj) {
        
        if (is_nonvalid_file == 1) {
            return;
        }
        
        var drop = Dropzone.forElement(obj);
        
        for (var i = 0; i < drop.files.length; i++) 
        {
            if (!validateFile(drop.files[i])) {
                is_nonvalid_file = 1;
                return;
            }
            formData.append(drop.options.paramName + "[]", drop.files[i]);
        }
    }); 
    
    if (is_nonvalid_file == 1) {
        return false;
    }
    else {
        return true
    }
}
/**
 * Validē datnes izmēru, vai tas ir mazāks par maksimāli pieļaujamo
 * 
 * @param {Object} file Datnes objekts (input elements ar tipu file)
 * @returns {Boolean}
 */
function validateFile(file) {
    
    if (typeof file.name == "undefined") {
        return false;
    }
    
    var filesize = ((file.size/1024)/1024).toFixed(2);
      
    var err = DX_CORE.trans_file_error;
    var limit = 0;
    if (parseFloat(filesize) > parseFloat(DX_CORE.max_upload_size))
    {        
        limit = parseFloat(DX_CORE.max_upload_size);
    }
    else if (parseFloat(filesize) > parseFloat(DX_CORE.post_max_size)){
        limit = parseFloat(DX_CORE.post_max_size);
    }
    
    if (limit > 0) {
        err = err.replace("%n", file.name).replace("%s", filesize).replace("%u", limit);
        notify_err(err);
        return false;
    }
    
    return true;
}

 /*
 * Dzēš vienu ierakstu no reģistra - izsauc no formas
 * 
 * @param   string      form_htm_id Formas HTML objekta identifikators
 * @param   string      grid_htm_id Reģistra tabulārā skata HTML objekta identifikators - nepieciešams lai atjauninātu saraksta datus
 * @return  void
 */ 
function delete_list_item(form_htm_id, grid_htm_id)
{
    if (!confirm(DX_CORE.trans_confirm_delete))
    {
        return;
    }

    var item_id = $("#" + form_htm_id + " input[name='item_id']").val();

    if (item_id === 0)
    {
        // ieraksts vēl nav bijis saglabāts, tāpēc vienkārši aizveram formu
        $("#" + form_htm_id).modal('hide');
        return;
    }

    var edit_form_id = $("#" + form_htm_id + " input[name='edit_form_id']").val();

    var formData = new FormData();
    formData.append("item_id", item_id);
    formData.append("edit_form_id", edit_form_id);
    
    var request = new FormAjaxRequest ("delete_item", form_htm_id, grid_htm_id, formData);
    
    request.callback = function() {
        
        if (grid_htm_id)
        {
            reload_grid(grid_htm_id);
        }
        
        // Aizveram formu
        $("#" + form_htm_id).modal('hide');
        
        notify_info(DX_CORE.trans_data_deleted);
    };
    
    // izpildam AJAX pieprasījumu
    request.doRequest();
}

/**
 * Dzēš vienu vai vairākus gridā norādītos ierakstus
 * 
 * @param integer list_id Reģistra ID
 * @param integer grid_htm_id Grid elementa HTML ID
 * @param string items Dzēšamo ierakstu ID atdalīti ar |
 * @param integer is_count1 Pazīme, vai dzēšamo ierkstu skaits ir 1 (1 - jā, 0 - nē, ir vairāki ieraksti)
 * @returns void
 */
function delete_multiple_items(list_id, grid_htm_id, items, is_count1)
{
    var formData = new FormData();
    formData.append("list_id", list_id);
    formData.append("items", items);
    
    var request = new FormAjaxRequest ("delete_grid_items", '', grid_htm_id, formData);
    
    request.callback = function() {
        
        reload_grid(grid_htm_id);
        
        var msg = DX_CORE.trans_data_deleted;
        if (!is_count1)
        {
            msg = DX_CORE.trans_data_deleted_all;
        }
        
        notify_info(msg);
    };
    
    // izpildam AJAX pieprasījumu
    request.doRequest();
}

 /*
 * Ģenerē Word datni. 
 * 
 * @param   integer     item_id     Ieraksta identifikators
 * @param   integer     list_id     Reģistra identifikators
 * @param   string      grid_htm_id Reģistra tabulārā skata HTML objekta identifikators - nepieciešams lai atjauninātu saraksta datus
 * @param   string      form_htm_id Formas HTML objekta identifikators
 * @return  void
 */ 
function generate_word(item_id, list_id, grid_htm_id, form_htm_id)
{
    var formData = new FormData();
    formData.append("item_id", item_id);
    formData.append("list_id", list_id);
    formData.append("filter_data", get_word_filter_data(item_id));    

    var request = new FormAjaxRequest ("generate_word", form_htm_id, grid_htm_id, formData);
    
    request.progress_info = DX_CORE.trans_word_generating;
    request.callback = function(data) {
    
        reload_grid(grid_htm_id);

        $("#" + form_htm_id + " [dx_file_field_id=" + data['field_id'] + "]").html(data['html']);                          

        notify_info(DX_CORE.trans_word_generated);
    };
    
    // izpildam AJAX pieprasījumu
    request.doRequest();    
}

/**
 * Nodrošina satura redaktora datņu pārvladību
 * 
 * @param {type} field_name
 * @param {type} url
 * @param {type} type
 * @param {type} win
 * @returns {undefined}
 */
function FileManager(field_name, url, type, win) { 
    
    // from http://andylangton.co.uk/blog/development/get-viewport-size-width-and-height-javascript
    var w = window,
    d = document,
    e = d.documentElement,
    g = d.getElementsByTagName('body')[0],
    x = w.innerWidth || e.clientWidth || g.clientWidth,
    y = w.innerHeight|| e.clientHeight|| g.clientHeight;
    
    var cmsURL = DX_CORE.site_url+'filemanager/show?&field_name='+field_name+'&langCode='+tinymce.settings.language;
    
    if(type == 'image') {           
        cmsURL = cmsURL + "&type=images";
    }

    tinyMCE.activeEditor.windowManager.open({
        file : cmsURL,
        title : Lang.get('fields.file_browser_title'),
        width : x * 0.8,
        height : y * 0.8,
        resizable : "yes",
        close_previous : "no"
    });         

}

function init_textarea(htm_id)
{
	
        var config = {
	    mode : "exact",
	    height : 208,
	    language : Lang.getLocale(),
	    plugins: [
	        "advlist autolink lists link image charmap print preview anchor colorpicker hr",
	        "searchreplace visualblocks code fullscreen",
	        "insertdatetime media table contextmenu paste textcolor",
                "custom_buttons"
	    ],
	    toolbar: "insertfile undo redo | styleselect | bold italic | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | sizeselect fontsizeselect",
	    file_browser_callback: FileManager,
            fullscreen_new_window : true,
	    theme_advanced_font_sizes: "10px,12px,13px,14px,15px,16px,18px,20px,22px,24px,26px,28px,30px,32px,34px,36px,38px,40px",
	    font_size_style_values: "10px,12px,13px,14px,15px,16px,18px,20px,22px,24px,26px,28px,30px,32px,34px,36px,38px,40px"
        };
      
        // custom_buttons pluginā ir iespējams katrai formai likt savus paštaisītos pluginus
        // Plugina kodu ieraksta datu bāzes tabulā "dx_forms_js" pie atbilstošās formas
        // Zemāk redzamā rindiņa pievieno tukšu pluginu, kas nepieciešams, lai nerastos kļūda ja formai tomēr nav paštaisītu pluginu
        tinyMCE.PluginManager.add('custom_buttons', function(editor, url) {})
        
        if (DX_CORE.valid_elements.length > 0)
        {
            config['valid_elements'] = DX_CORE.valid_elements;
        }
        
        if (DX_CORE.valid_styles.length > 0)
        {
        config['valid_styles'] = {};    
        config['valid_styles']['*'] = DX_CORE.valid_styles;
        }
        
        tinyMCE.init(config);
}

function init_soft_code(htm_id)
{
	
	var editor = CodeMirror.fromTextArea(document.getElementById(htm_id), {
                 lineNumbers: true,
                 matchBrackets: true,
                 styleActiveLine: true,
                 mode: "javascript",
                 autofocus: true
             });
             
	editor.on('change',function(cMirror){
	  // get value right from instance
	   $("#" + htm_id).val(cMirror.getValue());
	});
	
	
	setTimeout(function() {
	  editor.refresh();
	}, 1000); // or 10, 100
}

$(document).ready(function($) {
    //init tinymcm control
    init_textarea();    
});

$(document).on('focusin', function(e) {
    if ($(e.target).closest(".mce-window").length) {
        e.stopImmediatePropagation();
    }
});