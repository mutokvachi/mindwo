/**
 * Reģistru katalogu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockFolders = function()
{ 
    /**
     * Parāda foldera apakšfolderus
     * 
     * @param {object} block Folderu komponentes HTML objekts
     * @returns {undefined}
     */
    var handleFolderClick = function(block) {
        block.find('.tile').click(function() {           
            if ($(this).attr("dx_is_register") == "1") {
                show_page_splash();
                window.location.href = DX_CORE.site_url + "skats_" + $(this).attr('dx_url');
            }
            else {
                block.find('.tiles').hide();
                block.find('.tiles[dx_id=' + $(this).attr('dx_id') + ']').show();
            }
        });
    };
    
    /**
     * Inicializē reģistru kataloga bloka JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initBlock = function() {        
        
        $(".dx-folders-portlet[dx_is_init='0']").each(function() {            
            
            $(this).find('.tiles').hide();
            $(this).find('.tiles[dx_id=' + $(this).attr('dx_menu_id') + ']').show();
            
            handleFolderClick($(this));
            
            $(this).attr('dx_is_init', 1); // uzstādam pazīmi, ka bloks inicializēts
        });    
    };    

    return {
        init: function() {
            initBlock();
        }
    };
}();

$(document).ready(function() {
    BlockFolders.init();
});