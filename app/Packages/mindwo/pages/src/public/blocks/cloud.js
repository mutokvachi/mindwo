/**
 * Iezīmju mākoņa JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockCloud = function()
{    
    /**
     * Inicializē iezīmju mākoņu blokus. Ja ir jau uzzīmēts, tad dzēš un inicializē no jauna
     * 
     * @returns {undefined}
     */
    var drawCloud = function() {
        $("div [dx_attr='cloud']").each(function() {

            var tags = JSON.parse($(this).attr('dx_json'));

            var arr = $.map(tags, function(el) {
                return el;
            });

            $(this).jQCloud('destroy'); // clear old tags

            $(this).jQCloud(arr, {
                autoresize: true,
                afterCloudRender: targetLinks
            });
        });
    };
    
    /**
     * Uzstāda mākoņa saitēm, lai tās atver konkrētā TAB logā
     * 
     * @returns {undefined}
     */
    var targetLinks = function() {
        PageMain.initTargetLinks();
    }
    
    /**
     * Pārzīmē iezīmju mākoni, ja portlets sākotnēji tika ielādēs sakļauts, un tad nospiež izvēršanu
     * 
     * @returns {undefined}
     */
    var handleExpand = function(){
       
        $('a.dx-cloud-collapse').click(function(){
            if ($(this).hasClass('expand'))
            {
                setTimeout(function() {
                    drawCloud();
                }, 500);
            }
        });  
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializētos izdevumu blokus
     * 
     * @returns {undefined}
     */
    var initCloud = function()
    {        
        drawCloud();
        handleExpand();
        
        // Pievienojam mākoņu pārzīmēšanas izsaukumu uz lapas/loga izmēra izmaiņām
        PageMain.addResizeCallback(drawCloud);
    };

    return {
        init: function() {
            initCloud();
        }
    };
}();

$(document).ready(function() {
    BlockCloud.init();
});