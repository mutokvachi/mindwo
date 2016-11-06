(function($)
{
	/**
	 * DateTimeField - a jQuery plugin that inits datetime functionality (calendar picker)
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.DateTimeField = function(opts)
	{
		var options = $.extend({}, $.fn.DateTimeField.defaults, opts);
		return this.each(function()
		{
			new $.DateTimeField(this, options);
		});
	};
	
	$.fn.DateTimeField.defaults = {
		locale: "en",
		format: "yyyy-mm-dd",
                is_time: false
	};
	
	/**
	 * DateTimeField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.DateTimeField = function(root, opts)
	{
		$.data(root, 'DateTimeField', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
                
                if (this.root.data("is-init")) {
                    return; // fields is allready initialized
                }
                
		this.input = $('.dx-datetime-field', this.root);
		this.calButton = $('.dx-datetime-cal-btn', this.root);
		
                if (this.root.data("format")) {
                    this.options.format = this.root.data("format");
                }
                
                if (this.root.data("locale")) {
                    this.options.locale = this.root.data("locale");
                }
                
                if (this.root.data("is-time")) {
                    this.options.is_time = this.root.data("is-time");
                }
                
		// Bind callbacks to buttons
		this.calButton.click(function()
		{
                    self.show_cal();
		});
                
                this.input.datetimepicker({
                    lang: this.options.locale,
                    format: this.options.format,
                    timepicker: this.options.is_time,
                    dayOfWeekStart: 1,
                    closeOnDateSelect: true
                });
                
                this.root.data("is-init", 1);
	};
	
	/**
	 * DateTimeField methods
	 */
	$.extend($.DateTimeField.prototype, {
		/**
		 * Show calendar
		 */
		show_cal: function()
		{
                    this.input.datetimepicker('show');
		}
	});
})(jQuery);

$(document).ajaxComplete(function(event, xhr, settings) {
    $(".dx-datetime").DateTimeField();
});

$(document).ready(function() {
    $(".dx-datetime").DateTimeField();
});