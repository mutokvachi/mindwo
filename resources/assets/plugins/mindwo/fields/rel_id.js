/**
 * JavaScript logic for dropdown fields
 * 
 * @type _L4.Anonym$0|Function
 */
var RelIdField = function()
{        
    /**
     * Handles adding button click event - opens related form
     * @param {object} fld_elem Field HTML element
     * @returns {undefined}
     */
    var handleBtnAdd = function(fld_elem) {
        fld_elem.find(".dx-rel-id-add-btn").click(function() {
            rel_new_form(fld_elem.attr("data-form-url"), fld_elem.attr("data-rel-list-id"), 0, fld_elem.attr("data-rel-field-id"), fld_elem.attr("id"), "RelIdField");             
        });
    };
    
    /**
     * Handles edit button click event - opens related form
     * @param {object} fld_elem Field HTML element
     * @returns {undefined}
     */
    var handleBtnEdit = function(fld_elem) {
        fld_elem.find(".dx-rel-id-edit-btn").click(function() {
            var cur_val = 0;
            var sel = fld_elem.find("select");
                        
            if (sel.length > 0) {
                // field in edit mode
                cur_val = sel.val();
            }
            
            if (cur_val == 0) {
                notify_err(fld_elem.attr("data-trans-must-choose"));
                return;
            }
            
            rel_new_form(fld_elem.attr("data-form-url"), fld_elem.attr("data-rel-list-id"), cur_val, fld_elem.attr("data-rel-field-id"), fld_elem.attr("id"), "RelIdField");             
        });
    };
    
    /**
     * Handles view button click event - opens related form
     * @param {object} fld_elem Field HTML element
     * @returns {undefined}
     */
    var handleBtnView = function(fld_elem) {
        fld_elem.find(".dx-rel-id-view-btn").click(function() {
            var self = $(this);            
            var update_fld = function(frm) {
                var fld = frm.find(".dx-form-field-line[data-field-id=" + fld_elem.attr("data-rel-field-id") + "]");
                var inp = fld.find("input");
                
                if (inp.length > 0) {
                    self.closest(".dx-rel-id-field").find(".dx-rel-id-text").val(inp.val());
                }
            };
            
            open_form(fld_elem.attr("data-form-url"), $(this).attr("data-item-id"), fld_elem.attr("data-rel-list-id"), 0, 0, "", 0, "", {after_close: update_fld});             
        });
    };
    
    /**
     * Call back function after new item added - so it appear in dropdown
     * @param {string} fld_htm_id Dropdown field HTML element ID
     * @param {integer} val_id Saved related item ID
     * @param {string} val_title Saved related item title
     * @returns {undefined}
     */
    var update_callback = function(fld_htm_id, val_id, val_title) {        
        var sel = $("#" + fld_htm_id).find("select");
        if (sel && sel.length > 0) {
            var opt = sel.find("option[value=" + val_id + "]");
            if (opt.length > 0) {
                opt.html(val_title);
            }
            else {
                sel.append($('<option></option>').val(val_id).html(val_title).attr('selected', true));
            }
        }
        else {
            var input = $("#" + fld_htm_id).find("input.dx-rel-id-text");
            if (input && input.length > 0) {
                input.val(val_title);
            }
        }
    };
    
    /**
     * Fills binded dropdown fields (related to an parent dropdown) ToDo: test if this is needed...
     * @param {object} fld_elem Field HTML element
     * @returns {undefined}
     */
    var initBinded = function(fld_elem) {
        if (fld_elem.attr("data-binded-field-name").length > 0 && fld_elem.attr("data-item-value") > 0) {
            setTimeout(function() {
                if ($('#' + fld_elem.attr("data-frm-uniq-id") + '_' + fld_elem.attr("data-binded_field-name")).val()==0)
                {
                    load_binded_field(fld_elem.attr("data-frm-uniq-id") + '_' + fld_elem.attr("data-item-field"), fld_elem.attr("data-frm-uniq-id") + '_' + fld_elem.attr("data-binded-field-name"), fld_elem.attr("data-binded-field-id"), fld_elem.attr("data-binded-rel-field-id"));                    
                }
            }, 2000);
        }
    }
    
    /**
     * Inits dropdown fields
     * 
     * @returns {undefined}
     */
    var initField = function() {
        $(".dx-rel-id-field[data-is-init=0]").each(function() {            
            handleBtnAdd($(this));
            handleBtnEdit($(this));
            handleBtnView($(this));
            initBinded($(this));            
            $(this).attr('data-is-init', 1);
        });       
    };

    return {
        init: function() {
            initField();
        },
        update_callback: function(fld_htm_id, val_id, val_title) {
            update_callback(fld_htm_id, val_id, val_title);
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {
    RelIdField.init();
});

$(document).ready(function() {
    RelIdField.init();
});