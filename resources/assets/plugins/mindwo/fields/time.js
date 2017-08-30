
(function($)
{
	/**
	 * TimeField - a jQuery plugin that inits time field functionality
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.TimeField = function(opts)
	{
		var options = $.extend({}, $.fn.TimeField.defaults, opts);
		return this.each(function()
		{
			new $.TimeField(this, options);
		});
	};
	
	$.fn.TimeField.defaults = {};
	
	/**
	 * TimeField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.TimeField = function(root, opts)
	{
		$.data(root, 'TimeField', this);
		
		this.options = opts;
		this.root = $(root);
		
		if(this.root.hasClass("is-init"))
		{
			return; // field is allready initialized
		}
		                
                this.root.timepicker({
                    template: 'dropdown',
                    autoclose: true,
                    minuteStep: 10,
                    defaultTime: '',
                    showSeconds: false,
                    showMeridian: false,
                    showInputs: true
                });                
		
		this.root.addClass("is-init");
	};
	
})(jQuery);

$(document).ajaxComplete(function()
{
	$(".dx-time").TimeField();
});

$(document).ready(function()
{
	$(".dx-time").TimeField();
});