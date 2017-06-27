(function($)
{
	/**
	 * ColorField - a jQuery plugin that inits color field functionality (HTML5 picker)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.ColorField = function(opts)
	{
		var options = $.extend({}, $.fn.ColorField.defaults, opts);
		return this.each(function()
		{
			new $.ColorField(this, options);
		});
	};
	
	$.fn.ColorField.defaults = {};
	
	/**
	 * ColorField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.ColorField = function(root, opts)
	{
		$.data(root, 'ColorField', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
		                
		if(this.root.hasClass("is-init"))
		{
                    return; // field is allready initialized
		}
		
		this.root.find(".dx-color-del-btn").click(function() {                    
                    self.root.find("input[type=hidden]").val('');
                    self.root.find("input[type=color]").val('#fafafa');
                });
                
                this.root.find("input[type=color]").change(function() {
                    self.root.find("input[type=hidden]").val($(this).val());
                });
		
		this.root.addClass("is-init");
	};
	
})(jQuery);

$(document).ajaxComplete(function()
{
	$(".dx-color-field-container").ColorField();
});

$(document).ready(function()
{
	$(".dx-color-field-container").ColorField();
});