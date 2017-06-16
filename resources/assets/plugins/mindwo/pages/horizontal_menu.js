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
	 * Make navigation behave correctly on mobiles.
	 */
	var handleTapEvent = function()
	{
		// select all dropdown toggles under the top level
		$('.dx-main-menu .dropdown-submenu > a.dropdown-toggle').each(function()
		{
			$(this).click(function(e)
			{
				if($(window).width() < 768)
				{
					e.stopPropagation();
					
					// select the ul element next to the toggle (submenu itself)
					var submenu = $(this).next();
					
					if(submenu.is(':visible'))
					{
						// hide submenu and all open sub-submenus of it
						submenu.add('.dropdown-menu', submenu).attr('style', '');
					}
					else
					{
						// hide already open submenus at the same level
						$(this).parent().siblings('.dropdown-submenu').find('.dropdown-menu:visible').attr('style', '');
						submenu.show();
					}
				}
			});
		});
		
		// close open submenus when closing a top-level menu
		$('.dx-main-menu > li > a.dropdown-toggle').click(function()
		{
			if($(window).width() < 768)
			{
				// if user is closing menu, then hide submenus of it
				if($(this).attr('aria-expanded') == 'true')
				{
					$(this).next().find('.dropdown-menu:visible').attr('style', '');
				}
				// if user opens another menu, hide submenus of an already open menu
				else
				{
					$(this).parent().siblings('.open').find('.dropdown-submenu .dropdown-menu:visible').attr('style', '');
				}
			}
		});
		
		$('.dx-main-menu').on('click', '.dropdown-submenu > a.dropdown-toggle', function(e)
		{
			if($(window).width() >= 768)
			{
				e.stopPropagation();
				$(this).trigger('mouseenter');
			}
		});
	};
	
	var hideSubmenus = function()
	{
		if($(window).width() > 768)
		{
			$('.dx-main-menu .dropdown-submenu .dropdown-menu:visible').attr('style', '');
		}
	};
	
	var menu_ul = $('#navbar > ul');
	var more_li = menu_ul.children('#more-items-wrap');
	var more_ul = more_li.children('#more-items');
	var more_li_width;
	var widths = [];
	
	/**
	 * Implementation of 'tabdrop' behavior for main navigation. When it is not enough place for all menu items,
	 * display 'More' menu item, and move there all items that don't fit width of a container, and vice versa.
	 */
	var emulateTabdrop = function()
	{
		// extra small screen - remove 'more' item for collapsed menu to work
		if($(window).width() <= 768)
		{
			if(more_ul.children().length)
			{
				more_ul.children().insertBefore(more_li).removeClass('dropdown-submenu');
			}
			
			more_li.hide();
			
			return;
		}
		
		// small, medium and large screens
		else
		{
			// calculate widths
			if(!more_li_width)
			{
				// make sure that 'More' item is visible
				more_li.show();
				more_li_width = more_li.outerWidth();
				
				// calculate widths of all menu items except 'More' item
				widths = [];
				menu_ul.children('li:not(:last-child)').each(function()
				{
					widths.push($(this).outerWidth());
				});
			}
		}
		
		// wdith of a container
		var width = menu_ul.parent().innerWidth() - more_li_width;
		var items = menu_ul.children('li:not(:last-child)');
		var count = items.length;
		
		// sum of widths of all menu items
		var items_width = 0;
		
		for(var i = 0; i < count; i++)
		{
			items_width += widths[i];
		}
		
		// not all items are visible - hide redundant items
		if(items_width > width)
		{
			more_li.show();
			
			// place redundant items under the 'More' item
			while(items_width > width)
			{
				var current = $(items[count - 1]);
				items_width -= widths[count - 1];
				count--;
				current = current.prependTo(more_ul);
				// if the moved item has child nodes, add class dropdown-submenu (mandatory)
				if(current.children('ul').length)
				{
					current.addClass('dropdown-submenu');
				}
			}
		}
		
		// all items are visible - get them out of the 'More' item and place back to the root level of the main menu
		else if(items_width < width)
		{
			while(true)
			{
				if(count == widths.length)
				{
					break;
				}
				
				if(items_width + widths[count] >= width)
				{
					break;
				}
				
				var current = more_ul.children().first();
				items_width += widths[count];
				count++;
				current.removeClass('dropdown-submenu').insertBefore(more_li);
			}
		}
		
		if(more_ul.children().length)
		{
			more_li.is(':visible') || more_li.show();
		}
		
		else
		{
			more_li.is(':hidden') || more_li.hide();
		}
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
		
		if((typeof dx_is_cssonly === 'undefined') || !dx_is_cssonly)
		{
			setContentMargin();
			PageMain.addResizeCallback(setContentMargin);
		}
		
		handleScroll();
		handleTapEvent();
		
		PageMain.addResizeCallback(positionDIVs);
		PageMain.addResizeCallback(hideSubmenus);
		
		if((typeof dx_is_cssonly !== 'undefined') && dx_is_cssonly)
		{
			emulateTabdrop();
			PageMain.addResizeCallback(emulateTabdrop);
		}
	};
	
	return {
		init: function()
		{
			initUI();
		},
                positionateDIVs: function() {
                        positionDIVs();
                }
	};
}();

$(document).ready(function()
{
    HMenuUI.init();
});