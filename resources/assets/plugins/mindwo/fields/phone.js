(function($)
{
	/**
	 * PhoneField - a jQuery plugin that inits phone field functionality (phone codes dropdown)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.PhoneField = function(opts)
	{
		var options = $.extend({}, $.fn.PhoneField.defaults, opts);
		return this.each(function()
		{
			new $.PhoneField(this, options);
		});
	};
	
	$.fn.PhoneField.defaults = {
	};
	
	/**
	 * PhoneField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.PhoneField = function(root, opts)
	{
		$.data(root, 'PhoneField', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
                
                if (this.root.data("is-init")) {
                    return; // field is allready initialized
                }
                
		this.input = $('.dx-phone-input', this.root);
		this.select = $('.dx-phone-select', this.root);
		this.hidden = $('.dx-phone-hidden', this.root);
                this.current_select = this.select.val();
                
                if (this.select.val() == 0) {
                    this.input.prop("readonly", true);
                }                
                
                /**
                 * Updates phone value which will be saved in database
                 * @returns {undefined}
                 */
                var setHiddenVal = function() {
                   
                    if (self.input.val().length > 0) {
                        self.hidden.val("(" + self.select.val() + ") " + self.input.val());
                    }
                    else {
                        self.hidden.val('');
                    }
                    
                };
            
                /**
                 * Re-order phone codes dropdown asccending
                 * @returns {undefined}
                 */
                var resortSelect = function() {
                    var options = self.select.find('option');
                    var arr = options.map(function(_, o) { return { t: $(o).text(), v: o.value }; }).get();
                    arr.sort(function(o1, o2) { return o1.t > o2.t ? 1 : o1.t < o2.t ? -1 : 0; });
                    options.each(function(i, o) {
                      o.value = arr[i].v;
                      $(o).text(arr[i].t);
                    });
                };
                
                /**
                 * Callback after new country insert
                 * @param {object} frm New country insertion form object
                 * @returns {undefined}
                 */
                var newCallback = function(frm) {
                    
                    var country_code = frm.find("input[name=code]").val();
                    var phone_code = frm.find("input[name=phone_code]").val();
                    
                    var title = country_code + " (" + phone_code + ")";
                    
                    self.select.append($('<option>', {
                        value: phone_code,
                        text: title
                    }));
                    
                    resortSelect();
                    
                    self.select.val(phone_code);
                    self.current_select = phone_code;
                    
                    frm.modal('hide');
                    self.input.prop("readonly", false);
                    setHiddenVal();
                    self.input.focus();
                };
                
		/**
                 * Handles dropdown change event
                 */
		this.select.change(function(e){
                    
                    if ($(this).val() == "new") {
                        var list_id = self.root.data("country-list-id");
                        view_list_item("form", 0, list_id, 0, 0, "", "", {after_save: newCallback});
                        $(this).val(self.current_select);
                        return;
                    }
                    
                    self.current_select = $(this).val();
                    
                    if ($(this).val() != 0) {
                        self.input.prop("readonly", false);
                        setHiddenVal();
                        self.input.focus();
                    }
                    else {
                        self.input.prop("readonly", true);
                        self.input.val('');
                        self.hidden.val('');
                    }
		});
                
                /**
                 * Handlses input box change event - performs validation
                 */
                this.input.change(function() {
                    var val = $(this).val().replace(/\D/g,'');
                    if (val != $(this).val()) {
                        notify_err(Lang.get('errors.phone_format_err'));
                        $(this).val(val);
                    }
                    setHiddenVal();
                });
                
                /**
                 * Allow to enter only numbers
                 */
                this.input.keydown(function(e) {
                    -1!==$.inArray(e.keyCode,[46,8,9,27,13,110])||/65|67|86|88/.test(e.keyCode)&&(!0===e.ctrlKey||!0===e.metaKey)||35<=e.keyCode&&40>=e.keyCode||(e.shiftKey||48>e.keyCode||57<e.keyCode)&&(96>e.keyCode||105<e.keyCode)&&e.preventDefault()
                });
                                
                this.root.data("is-init", 1);
	};
})(jQuery);

$(document).ajaxComplete(function() {
    $(".dx-phone-field").PhoneField();
});

$(document).ready(function() {
    $(".dx-phone-field").PhoneField();
});