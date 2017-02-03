/**
 * JavaScript logic for horizontal menu UI
 *
 * @type _L4.Anonym$0|Function
 */
var HMenuUI = function()
{
	/**
	 * Izceļ aktivizēto izvēlni (jo cache glabājas sākotnējais izcēlums - to noņemam)
	 *
	 * @returns {undefined}
	 */
	var setActiveMenu = function()
	{
		$(".dx-main-menu li").removeClass("active");
		
		var active_item = $('.dx-main-menu li a[href="' + window.location.href + '"]');
		
		if(active_item.length == 0)
		{
			return; // nav ielādēta lapa no menu
		}
		
		if(parseInt(active_item.attr("data-level")) == 0)
		{
			active_item.parent().addClass("active");
		}
		else
		{
			setActiveParentMenu(parseInt(active_item.attr("data-level")), active_item.parent());
		}
	};
	
	/**
	 * Rekursīvi aktivizē menu elementa vecākos elementus līdz pašam pirmajam līmenim
	 * @param {integer} level Līmenis
	 * @param {object} elem Menu elements
	 * @returns {undefined}
	 */
	var setActiveParentMenu = function(level, elem)
	{
		if(level == 0)
		{
			elem.addClass("active");
			return;
		}
		else
		{
			setActiveParentMenu(level - 1, elem.parent().parent());
		}
	};
	
	/**
	 * Repositionate page content and employees search pane
	 *
	 * @returns {undefined}
	 */
	var setContentMargin = function()
	{
		var menu_w = $(".dx-top-menu").outerWidth() - $("#dx-search-box-top-li").outerWidth() - 40;
		$(".dx-main-menu").css("max-width", menu_w + "px");
		
		var height = $(".dx-top-menu").offset().top + $(".dx-top-menu").outerHeight() + "px";
		$(".dx-employees-quick").css("top", height, "important");
		
	};
	
	/**
	 * Hide images not loaded
	 * @returns {undefined}
	 */
	var hideErrorImages = function()
	{
		$("img").error(function()
		{
			$(this).hide();
			setContentMargin();
		});
	};
	
	var positionDIVs = function()
	{
		var top = $(window).scrollTop();
		if(top > 50 && $(window).width() >= 768)
		{
			$(".dx-top-menu").removeClass("dx-nonfixed-top").addClass("navbar-fixed-top");
			$(".dx-page-container").css('margin-top', '50px');
						
			var height = $(".dx-top-menu").outerHeight() + "px";
			$(".dx-employees-quick").css("top", height, "important");
		}
		else
		{
			$(".dx-top-menu").removeClass("navbar-fixed-top").addClass("dx-nonfixed-top");
			$(".dx-page-container").css('margin-top', '0px');
						
			var height = ($(".dx-top-menu").offset().top + $(".dx-top-menu").outerHeight() - top) + "px";
			$(".dx-employees-quick").css("top", height, "important");
		}
	};
	
	/**
	 * Stick menu on scrolling
	 * @returns {undefined}
	 */
	var handleScroll = function()
	{
		$(document).scroll(function()
		{
			positionDIVs();
		});
	};
	
	/**
	 * Inits horizontal menu page UI
	 *
	 * @returns {undefined}
	 */
	var initUI = function()
	{
		//hideErrorImages();
		setActiveMenu();
		setContentMargin();
		handleScroll();
		PageMain.addResizeCallback(setContentMargin);
		PageMain.addResizeCallback(positionDIVs);
	};
	
	return {
		init: function()
		{
			initUI();
		}
	};
}();

$(document).ready(function()
{
    HMenuUI.init();
});
/**
 * JavaScript logic for horizontal menu forms UI
 * 
 * @type _L4.Anonym$0|Function
 */
var HFormUI = function()
{ 
    var handleFormClose = function(grid_id) {
        $(".dx-form-fullscreen-frame .dx-form-close-btn").click(function() {
            $("#td_form_data").html("");
            $("#td_data").show();
            stop_executing(grid_id);
        });
    };
    
    /**
     * Inits horizontal menu page UI
     * 
     * @returns {undefined}
     */
    var initUI = function(grid_id) {       
       handleFormClose(grid_id);
    };

    return {
        init: function(grid_id) {
            initUI(grid_id);
        }
    };
}();
//# sourceMappingURL=elix_mindwo_horizontal_menu.js.map
