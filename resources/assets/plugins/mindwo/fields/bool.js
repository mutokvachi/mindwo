(function($)
{
	/**
	 * BoolField - a jQuery plugin that inits boolean field functionality (switch)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.BoolField = function(opts)
	{
		var options = $.extend({}, $.fn.BoolField.defaults, opts);
		return this.each(function()
		{
			new $.BoolField(this, options);
		});
	};
	
	$.fn.BoolField.defaults = {
	};
	
	/**
	 * BoolField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.BoolField = function(root, opts)
	{
		$.data(root, 'BoolField', this);
		
		this.options = opts;
		this.root = $(root);
                
                if (this.root.data("is-init")) {
                    return; // field is allready initialized
                }
		
                this.root.bootstrapSwitch();
                
                this.root.data("is-init", 1);
	};
	
	
})(jQuery);

$(document).ajaxComplete(function(event, xhr, settings) {
    $("input.dx-bool").BoolField();
});

$(document).ready(function() {
    $("input.dx-bool").BoolField();
});