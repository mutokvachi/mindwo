/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 04.11.16, 19:38
 */
(function($)
{
	$.fn.Sticky = function(opts)
	{
		var options = $.extend({}, $.Sticky.defaults, opts);
		return this.each(function()
		{
			new $.Sticky(this, options);
		});
	};
	
	$.Sticky = function(root, opts)
	{
		$.data(root, 'Sticky', this);
		var self = this;
		this.options = opts;
		this.root = $(root);
		this.placeholder = this.root.after('<div/>').next();
		
		this.root.width(this.root.parent().width());
		
		this.placeholder.css({
			position: this.root.css('position'),
			'float': this.root.css('float'),
			display: 'none'
		});
		
		this.init();
		this.update();
		
		$(window).resize(function()
		{
			self.init();
			self.update();
		});
		
		$(window).scroll(function()
		{
			self.init();
			self.update();
		});
	};
	
	$.Sticky.defaults = {
		side: 'top'
	};
	
	$.extend($.Sticky.prototype, {
		init: function()
		{
			this.placeholder.css({
				width: this.root.outerWidth(),
				height: this.root.outerHeight()
			});
			
			if(this.options.side == 'top')
			{
				if(this.root.hasClass('stuck'))
					this.top = this.placeholder.offset().top;
				
				else
					this.top = this.root.offset().top;
			}
			else
			{
				if(this.root.hasClass('stuck'))
					this.top = this.placeholder.offset().top - this.placeholder.height();
				
				else
					this.top = this.root.offset().top;
			}
			
			this.height = this.root.outerHeight();
		},
		
		stick: function()
		{
			this.root.addClass('stuck');
			this.placeholder.css('display', this.root.css('display'));
		},
		
		unstick: function()
		{
			this.root.removeClass('stuck');
			this.placeholder.css('display', 'none');
		},
		
		update: function()
		{
			var self = this;
			if(self.options.side == 'top')
			{
				if($(window).scrollTop() > self.top)
					self.stick();
				
				else
					self.unstick();
			}
			else
			{
				if($(window).scrollTop() + $(window).height() < self.top + self.height)
					self.stick();
				
				else
					self.unstick();
			}
		}
	});
})(jQuery);