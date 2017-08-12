/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 21.07.17, 16:51
 */


var Module = {
	create: function(name, methods)
	{
		$.fn[name] = function(opts)
		{
			var options = $.extend({}, $.fn[name].defaults, opts);
			
			return this.each(function()
			{
				new $[name](this, options);
			});
		};
		
		$.fn[name].defaults = ($.isPlainObject(methods.defaults) ? methods.defaults : {});
		
		$[name] = function(root, opts)
		{
			$.data(root, name, this);
			this.root = $(root);
			this.options = opts;
			
			if($.isFunction(this.construct))
			{
				this.construct();
			}
		};
		
		$.extend($[name].prototype, methods || {});
	}
};