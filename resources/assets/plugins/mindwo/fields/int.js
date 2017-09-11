(function($)
{
	/**
	 * IntField - a jQuery plugin that inits numeric field functionality
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.IntField = function(opts)
	{
		var options = $.extend({}, $.fn.IntField.defaults, opts);
		return this.each(function()
		{
			new $.IntField(this, options);
		});
	};
	
	$.fn.IntField.defaults = {};
	
	/**
	 * IntField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.IntField = function(root, opts)
	{
		$.data(root, 'IntField', this);
		
		this.options = opts;
		this.root = $(root);
		
		if(this.root.hasClass("is-init"))
		{
			return; // field is allready initialized
		}
		/*
		this.root.keydown(function (e) {
                    // Allow: backspace, delete, tab, escape, enter and .
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                         // Allow: Ctrl+A, Command+A
                        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                         // Allow: home, end, left, right, down, up
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                             // let it happen, don't do anything
                             return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });
                */
               
                this.root.keydown(function (e) {
                    // Allow: backspace, delete, tab, escape, enter and .
                    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                         // Allow: Ctrl+A, Command+A
                        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                         // Allow: home, end, left, right, down, up
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                             // let it happen, don't do anything
                             return;
                    }
                    // Ensure that it is a number and stop the keypress
                    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                        e.preventDefault();
                    }
                });
		
		this.root.addClass("is-init");
	};
	
})(jQuery);

$(document).ajaxComplete(function(event, xhr, settings)
{
	$(".dx-int").IntField();
});

$(document).ready(function()
{
	$(".dx-int").IntField();
});