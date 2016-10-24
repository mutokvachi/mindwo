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
     * Inits horizontal menu page UI
     * 
     * @returns {undefined}
     */
    var initUI = function() {
        
       setActiveMenu();        
        
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