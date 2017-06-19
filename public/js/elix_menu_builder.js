(function($)
{
	/**
	 * MenuBuilder - a jQuery plugin that renders menu drag & drop building UI
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.MenuBuilder = function(opts)
	{
		var options = $.extend({}, $.fn.MenuBuilder.defaults, opts);
		return this.each(function()
		{
			new $.MenuBuilder(this, options);
		});
	};
	
	$.fn.MenuBuilder.defaults = {
	};
	
	/**
	 * MenuBuilder constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.MenuBuilder = function(root, opts)
	{
            $.data(root, 'MenuBuilder', this);
            var self = this;
            this.options = opts;
            this.root = $(root);
            this.nodes = this.root.find('.dd').nestable('serialize');
            
            this.root.find(".dx-save-btn").click(function() {
                alert("Save to server: " + self.nodes);
            });
            
            var updateOutput = function (e) {
                var list = e.length ? e : $(e.target);
                if (window.JSON) {
                    self.nodes = window.JSON.stringify(list.nestable('serialize'));
                } else {
                    notify_err('JSON browser support required!');
                }
            };
            
            this.root.find('.dd').nestable({
                group: 1,
                maxDepth: 5,
                
            }).on('change', updateOutput);
	};
})(jQuery);
//# sourceMappingURL=elix_menu_builder.js.map
