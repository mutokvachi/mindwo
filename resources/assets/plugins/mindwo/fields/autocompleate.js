/**
 * JavaScript logic for autocompleate fields
 * 
 * @type _L4.Anonym$0|Function
 */
var AutocompleateField = function()
{        
    /**
     * Handles adding/edit button click event - opens related form
     * @param {object} fld_elem Field HTML element
     * @returns {undefined} 
     */
    var handleBtnAdd = function(fld_elem) {
        fld_elem.find(".dx-rel-id-add-btn").on("click", function() {            
            var cur_val = fld_elem.find(".dx-auto-input-id").val();
            
            if (fld_elem.data("is-profile")) {
                if (cur_val > 0) {
                    show_page_splash(1);
                    show_form_splash(1);
                    window.location = fld_elem.data("profile-url") + cur_val;            
                }
                else {
                    notify_err(Lang.get('fields.err_choose_val'));
                }
            }
            else {
                rel_new_form(fld_elem.attr("data-form-url"), fld_elem.attr("data-rel-list-id"), cur_val, fld_elem.attr("data-rel-field-id"), fld_elem.attr("id"), "AutocompleateField");                            
            }
        });
    };
    
    /**
     * Handles clear button click event - removes value from Select2 field
     * @param {object} fld_elem Field HTML element
     * @returns {undefined}
     */
    var handleBtnDel = function(fld_elem) {
        fld_elem.find(".dx-rel-id-del-btn").click(function() {
            fld_elem.find('.dx-auto-input-select2').select2('data', {id:0, text:""});
            fld_elem.find("input.dx-auto-input-id").val(0);
            
            if (fld_elem.data("is-profile")) {
                fld_elem.find(".dx-rel-id-add-btn").hide();
            }
            
        });
    };
    
    /**
     * Call back function after new item added - so it appear in field
     * @param {string} fld_htm_id Select2 field HTML element ID
     * @param {integer} val_id Saved related item ID
     * @param {string} val_title Saved related item title
     * @returns {undefined}
     */
    var update_callback = function(fld_htm_id, val_id, val_title) {        
        var sel = $("#" + fld_htm_id).find(".dx-auto-input-select2");
        if (sel && sel.length > 0) {
            // Form is in edit mode
            sel.select2('data', {id:val_id, text:val_title});
        }
        else {
            // Form is in view mode
            $("#" + fld_htm_id).find("input.dx-auto-input-txt").val(val_title);
        }
        $("#" + fld_htm_id).find("input.dx-auto-input-id").val(val_id);
    };
    
    /**
     * Converts given text in correct UTF-8 string format
     * @param {string} text Text to convert
     * @returns {document@call;createElement.innerText|span.innerText}
     */
    var convertText = function(text) {
        var span = document.createElement('span');
        span.innerHTML = text;
        return span.innerText;
    };
        
    /**
     * Initializes field Select2 functionality
     * 
     * @param {object} fld_elem Field HTML element
     * @returns {undefined}
     */
    var initSelect2 = function(fld_elem) {
        fld_elem.find('.dx-auto-input-select2').select2({
            placeholder: fld_elem.attr("data-trans-search"),
            minimumInputLength: fld_elem.attr("data-min-length"),
            ajax: {
                type: 'POST',
                url: DX_CORE.site_url  + 'fill_autocompleate',
                processData: false,
                contentType: false,
                dataType: 'json',
                quietMillis: 250,
                cache: true,
                data: function (term, page) {
                    return {
                        q: term, // search term
                        list_id: fld_elem.attr("data-rel-list-id"),
                        txt_field_id: fld_elem.attr("data-rel-field-id"),
                        rel_view_id: fld_elem.attr("data-rel-view_id"),
                        rel_display_formula_field: fld_elem.attr("data-rel-formula-field"),                        
                        field_id: fld_elem.attr("data-field-id"),
                    };
                },
                results: function (data, page) {                 
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to alter the remote JSON data

                    if (data.success == 1)
                    {
                        return { results: data.data};
                    }
                    else
                    {
                        if (data.error)
                        {
                            notify_err(data.error);
                        }
                        else
                        {
                            console.log("Some unknown autocompleate error!")    
                            notify_err(DX_CORE.trans_general_error);
                        }
                    }
                }       
            }
        }).on('change', function(event) {

            if (event.val)
            {
                fld_elem.find("input.dx-auto-input-id").val(event.val);
                if (fld_elem.data("is-profile")) {
                    fld_elem.find(".dx-rel-id-add-btn").show();
                }
            }
            else
            {
                fld_elem.find("input.dx-auto-input-id").val(0);
                if (fld_elem.data("is-profile")) {
                    fld_elem.find(".dx-rel-id-add-btn").hide();
                }
            }	
        });
        
        // set default value
        fld_elem.find('.dx-auto-input-select2').select2('data', { id: fld_elem.attr("data-item-value"), text: convertText(fld_elem.attr("data-item-text")) });
    };
    
    /**
     * Show or hide add button in case of employee profile logic
     * 
     * @param {object} fld_elem Field element
     * @returns {undefined}
     */
    var initProfileBtn = function(fld_elem) {        
        if (fld_elem.data("is-profile") && fld_elem.find("input.dx-auto-input-id").val() == "0") {            
            fld_elem.find(".dx-rel-id-add-btn").hide();
        }
    };
    
    /**
     * Inits autocompleate fields
     * 
     * @returns {undefined}
     */
    var initField = function() {
        $(".dx-autocompleate-field[data-is-init=0][data-is-manual-init=0]").each(function() {            
            handleBtnAdd($(this));
            handleBtnDel($(this));
            initSelect2($(this));
            initProfileBtn($(this));
            $(this).attr('data-is-init', 1);
        });       
    };

    return {
        init: function() {
            initField();
        },
        update_callback: function(fld_htm_id, val_id, val_title) {
            update_callback(fld_htm_id, val_id, val_title);
        },
        initSelect: function(fld_el) {
            fld_el.find('button.dx-rel-id-add-btn').hide();
            initSelect2(fld_el);
            
            if (fld_el.attr('data-is-init') != "1") {
                handleBtnDel(fld_el);
            }
            fld_el.attr('data-is-init', 1);
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {
    AutocompleateField.init();
});

$(document).ready(function() {
    AutocompleateField.init();
});