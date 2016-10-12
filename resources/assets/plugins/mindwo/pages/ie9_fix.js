/**
 * Galvenās lapas (kopīga visām lapām) JavaScript funkcionalitāte - IE9 problēmu risināšanai
 * 
 * @type _L4.Anonym$0|Function
 */
var IE9Fix = function()
{    
    /**
     * Notīra formas elementu placeholder vērtības
     * 
     * @param {type} form_elem
     * @returns {undefined}
     */
    var clearPlaceholders = function(form_elem) {
        form_elem.find('[placeholder]').each(function() {
            var input = $(this);
            if (input.val() == input.attr('placeholder')) {
              input.val('');
            }
        });  
    };
    
    /**
     * Novērš CSS problēmas ar IE9
     * 
     * @returns {undefined}
     */
    var fixDesign = function() {
        
        //$(".portlet").css('background-color', 'white');
        //$(".portlet").css('padding', '20px');
        
        $(".page-top").css('background-color','#2D5F8B');
    };
    
    var removePlaceholders = function() {
        $('[placeholder]').each(function() {
            $(this).attr('placeholder', '');
        }); 
    };
    
    var hide_tools = function() {
        $(".portfolio-content .search-article-tools-btn").hide();
        
        setTimeout(function() {
                $('#js-grid-juicy-projects').css('margin-top', '50px');
        }, 1000);
             
    };
    
    var countCSSRules = function() {
        var results = '',
            log = '';
    
        var total = 0;
        
        if (!document.styleSheets) {
            return;
        }
        
        var countSheet = function(sheet) {
            var count = 0;
            if (sheet && sheet.cssRules) {
                for (var j = 0, l = sheet.cssRules.length; j < l; j++) {
                    if( !sheet.cssRules[j].selectorText ) {
                        continue;
                    }
                    count += sheet.cssRules[j].selectorText.split(',').length;
                }
                
                if (count >= 4096) {
                    results += '\n********************************\nWARNING:\n There are ' + count + ' CSS rules in the stylesheet ' + sheet.href + ' - IE will ignore the last ' + (count - 4096) + ' rules!\n';
                }
            }
            
            return count;
        };
        
        for (var i = 0; i < document.styleSheets.length; i++) {
            total = total + countSheet(document.styleSheets[i]);
        }
        
        console.log('TOTAL CSS RULES: ' + total + ' TOTAL SHEETS: ' + document.styleSheets.length);
        if (results.length > 0) {
            console.log(results);
        }
    };

    
    /**
     * Uzstāda HTML formām, ka pirms sūtīšanas uz serveri jānotīra visu lauku placeholder vērtības
     * 
     * @returns {undefined}
     */
    var handleFormSubbmit = function() {
        $('form').submit(function() {
            clearPlaceholders($(this));
        });
    };
    
    /**
     * Inicializē galvenās lapas JavaScript funkcionalitāti IE9 kļūdu novēršanai
     * 
     * @returns {undefined}
     */
    var initFix = function() {
        
        //countCSSRules();
        
        if (!$('html').is('.ie9'))
        {
            return; // Nav IE9, neko nedaram
        }
        
        removePlaceholders();
        
        handleFormSubbmit();
        fixDesign();
        hide_tools();
        
        
    };

    return {
        init: function() {
            initFix();
        }
    };
}();

$(document).ready(function() {
    IE9Fix.init();
});