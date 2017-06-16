(function($)
{
	$.fn.ThemeSelect = function(opts)
	{
		var options = $.extend({}, opts);
		return this.each(function()
		{
			new $.ThemeSelect(this, opts);
		});
	};
	
	$.ThemeSelect = function(root, opts)
	{
		$.data(root, 'ThemeSelect', this);
		var self = this;
		this.root = $(root);
		this.options = opts;
		
		this.root.next().find('.dx-theme-link').click(function()
		{
			self.changeTheme($(this).data('themeId'));
		});
	};
	
	$.extend($.ThemeSelect.prototype, {
		changeTheme: function(themeId)
		{
			show_page_splash(1);
			
			$.ajax({
				type: 'post',
				url: '/theme/select/' + themeId,
				dataType: 'json',
				success: function(data)
				{
					if(data.success == 1)
					{
						window.location.reload(true);
					}
					hide_page_splash(1);
				}
			});
		}
	});
})(jQuery);

$(document).ready(function()
{
	if($("body").hasClass("dx-horizontal-menu-ui"))
	{
		$('.dx-user-change-design-link').ThemeSelect();
	}
});