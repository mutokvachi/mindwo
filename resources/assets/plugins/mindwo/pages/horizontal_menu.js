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
    var setActiveMenu = function() {      
        $(".navbar-fixed-top .navbar-default li").removeClass("active");

        var active_item = $('.navbar-fixed-top .navbar-default li a[href="' + window.location.href + '"]');
        
        if (active_item.length == 0) {
            return; // nav ielādēta lapa no menu
        }
        
        if (parseInt(active_item.attr("data-level")) == 0) {
          active_item.parent().addClass("active");
        }
        else {
            setActiveParentMenu(parseInt(active_item.attr("data-level")), active_item.parent());
        }
        
    };
    
    /**
     * Rekursīvi aktivizē menu elementa vecākos elementus līdz pašam pirmajam līmenim
     * @param {integer} level Līmenis
     * @param {object} elem Menu elements
     * @returns {undefined}
     */
    var setActiveParentMenu = function(level, elem) {
        
        if (level == 0) {
            elem.addClass("active");
            return;
        }
        else {
            setActiveParentMenu(level-1, elem.parent().parent());
        }  
    };
    
    /**
     * Repositionate page content and employees search pane
     * 
     * @returns {undefined}
     */
    var setContentMargin = function() {
        
        var menu_w = $(".navbar-fixed-top").outerWidth() - $("#dx-search-box-top-li").outerWidth()-40;
        
        $(".dx-main-menu").css("max-width", menu_w + "px");
        
        var height = $(".navbar-fixed-top").outerHeight() + "px";
        
        $(".dx-page-container").css("margin-top", height);
        
        $(".dx-employees-quick").css("top", height, "important");
        
    };
    
    var hideErrorImages = function() {
        $("img").error(function () { 
            $(this).hide();
            setContentMargin();
        });  
    };
    
    var handleScroll = function () {
        
        $(document).scroll(function() {
            var top = $(window).scrollTop();
            if (top > 50) {
                $(".dx-top-menu").removeClass("dx-nonfixed-top").addClass("navbar-fixed-top");
                $(".dx-page-container").css('margin-top', '50px');
                $(".dx-form-header").addClass("dx-nonfixed-header");
            } else {
                $(".dx-top-menu").removeClass("navbar-fixed-top").addClass("dx-nonfixed-top");
                $(".dx-page-container").css('margin-top', '0px');
                $(".dx-form-header").removeClass("dx-nonfixed-header");
            }
            console.log(top);
        });
    };
    
    
    /**
     * Inits horizontal menu page UI
     * 
     * @returns {undefined}
     */
    var initUI = function() {
       hideErrorImages(); 
       setActiveMenu();
       setContentMargin();
       handleScroll();
       PageMain.addResizeCallback(setContentMargin); 
    };

    return {
        init: function() {
            initUI();
        }
    };
}();

$(document).ready(function() {
    HMenuUI.init();
});