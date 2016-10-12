/**
 * Ziņas attēlošanas bloka JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockContentText = function()
{    
    /**
     * Inicializē ziņas attēlošanas bloka JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initBlock = function() {
        var emptyFunc = function() {};        
        EmployeesLinks.init($(".dx-article-item-page"), emptyFunc);
    };
   

    return {
        init: function() {
            initBlock();
        }
    };
}();

$(document).ready(function() {
    BlockContentText.init();
});