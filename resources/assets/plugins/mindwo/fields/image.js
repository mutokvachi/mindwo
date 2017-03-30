(function($)
{
	/**
	 * ImageField - a jQuery plugin that inits image field functionality
	 *
	 * @param root
	 * @returns {*}
	 * @constructor
	 */
	$.fn.ImageField = function(opts)
	{
		var options = $.extend({}, $.fn.ImageField.defaults, opts);
		return this.each(function()
		{
			new $.ImageField(this, options);
		});
	};
	
	$.fn.ImageField.defaults = {};
	
	/**
	 * ImageField constructor
	 *
	 * @param root
	 * @constructor
	 */
	$.ImageField = function(root, opts)
	{
		$.data(root, 'ImageField', this);
				
		this.root = $(root);		
		if(this.root.hasClass("is-init"))
		{
			return; // field is allready initialized
		}
                
		this.options = opts;
                
		var btn_rotate = this.root.find(".dx-rotate-btn");                
                var input_rotate = this.root.find(".dx-rotate-angle-input");
                var input_file = this.root.find(".dx-img-file-input");
                
                var rotateElement = function (img, side) {

                    var deg = img.data('rotate') || 0;
                    var angle = 0;

                    if (side == 'left') {
                        angle = parseInt(deg) - 90;
                    } else if (side == 'right') {
                        angle = parseInt(deg) + 90;
                    }

                    var rotate = 'rotate(' + angle + 'deg)';

                    img.css({
                        'transition': 'all 400ms',
                        '-webkit-transform': rotate,
                        '-moz-transform': rotate,
                        '-o-transform': rotate,
                        '-ms-transform': rotate,
                        'transform': rotate
                    });

                    img.data('rotate', angle);
                    input_rotate.val(angle);
                };
                
                btn_rotate.click(function() {
                    var img = $(this).closest(".dx-image-fld").find(".thumbnail img")
                    rotateElement(img, 'right');                    
                });
                
                input_file.on("change", function() {
                    
                    var img = $(this).closest(".dx-image-fld").find(".thumbnail img")
                    img.data('rotate', 0);
                    input_rotate.val(0);
                });
		
		this.root.addClass("is-init");
	};
	
})(jQuery);

$(document).ajaxComplete(function()
{
	$(".dx-image-fld").ImageField();
});

$(document).ready(function()
{
	$(".dx-image-fld").ImageField();
});