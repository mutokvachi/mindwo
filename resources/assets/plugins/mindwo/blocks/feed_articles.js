/**
 * Ziņu plūsmas bloks ar datu pielādēšanu reaģējot uz lapas ritināšanu
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockFeedArticles = function()
{
    /**
     * Ziņu lapošanas HTML
     * Saglabājam pie pirmā ziņu bloka ielādes, gadījumam, ja nāksies atslēgt pielādēšanu un pēc tam to atkal ieslēgt
     * 
     * @type String|@call;$@call;html
     */
    var pager_htm = "";
    
    /**
     * Iespējo ziņu pielādi uz lapas ritināšanu
     * 
     * @returns {undefined}
     */
    var enableScroll = function() {
        if ($(".dx-article-feed-content .jscroll-inner").length) {
            // jau ir inicializēts
            if ($(".jscroll-inner").hasClass("dx-disable-scroll")) {
                $(".jscroll-inner").removeClass("dx-disable-scroll");
                $(".dx-article-feed-content .pager").hide();
            }
            return;
        }

        pager_htm = $(".dx-article-feed-content .pager").html();

        $(".dx-article-feed-content").jscroll({
            loadingHtml: '<img src="' + getBaseUrl() + 'img/loading.gif" alt="Datu ielāde..." /> Datu ielāde...',
            padding: 20,
            nextSelector: 'ul.pager li > a[rel=next]',
            callback: function() {
                $(".dx-article-feed-content .pager").hide();
            },
            contentSelector: ".article_row_area"
        });

        $(".dx-article-feed-content .pager").hide();
    };
    
    /**
     * Atspējo ziņu pielādi uz lapas ritināšanu
     * 
     * @returns {undefined}
     */
    var disableScroll = function() {
        if (!$(".dx-article-feed-content .jscroll-inner").length) {
            // nav bijis inicializēts
            return;
        }

        if ($(".jscroll-inner").hasClass("dx-disable-scroll")) {
            // jau atspējots
            return;
        }

        $(".jscroll-inner").addClass("dx-disable-scroll");                   
        
        // dzēšam ar AJAX pielādētās ziņas
        $(".dx-article-feed-content .jscroll-inner .jscroll-added").remove();            
        
        // atliekam atpakaļ sākotnējo lapošanas stāvokli
        $(".dx-article-feed-content .pager").html(pager_htm);
        
        // parādam lapotāju
        $(".dx-article-feed-content .pager").show();
    };
    
    /**
     * Apstrādā lapas ritināšanu - pielādē ziņas
     * 
     * @returns {undefined}
     */
    var handleScroll = function() {

        var doc_width = $(document).width();

        if (doc_width >= 1200)
        {
            enableScroll();
        }
        else
        {
            disableScroll();
        }
    };

    /**
     * Inicializē ziņu plūsmas bloku
     * 
     * @returns {undefined}
     */
    var initFeed = function()
    {
        handleScroll();

        // Pievienojam plūsmas pielādēšanas funkcionalitātes iespējošanu/atspējošanu uz lapas/loga izmēra izmaiņām
        PageMain.addResizeCallback(handleScroll);
    };

    return {
        init: function() {
            initFeed();
        }
    };
}();

$(document).ready(function() {
    BlockFeedArticles.init();
});