/**
 * Portāla ziņu meklēšanas lapas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageArticles = function()
{ 
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Portāla ziņu meklēšanas bloka HTML elements
     * 
     * @type type
     */
    var page_elem = null;
    
    /**
     * Unikālais lapas bloka ID
     * 
     * @type String
     */
    var block_guid = "";

    /**
     * Iespējo ziņu pielādi uz lapas ritināšanu
     * 
     * @returns {undefined}
     */
    var enableScroll = function() {
        
        $("#feed_area_" + block_guid).jscroll({
            loadingHtml: '<img src="' + getBaseUrl() + 'mindwo/img/loading.gif" alt="Datu ielāde..." /> Datu ielāde...',
            padding: 20,
            nextSelector: 'ul.pager li > a[rel=next]',
            callback: function() {
                        $("#feed_area_" + block_guid + " .pager").hide();
            },
            contentSelector: ".article_row_area"
        });

        $("#feed_area_" + block_guid + " .pager").hide();
    };
    
    /**
     * Parāda vai paslēpj saiti "Notīrīt kritērijus" atkarībā ir vai nav norādīts vismaz viens kritērijs
     * 
     * @returns {undefined}
     */
    var clearLinkShowHide = function() {
        if (
                getElementVal(page_elem, 'input[name=criteria]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_from]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_to]', "") 
                )
        {
            page_elem.find(".dx-clear-link").show();
        }
        else
        {
            page_elem.find(".dx-clear-link").hide();
        }
    };

    /**
     * Notīra meklēšanas kritēriju lauku vērtības
     * 
     * @returns {undefined}
     */
    var clearFields = function() {
        form_elem.find('input[name=pick_date_from]').val('');
        form_elem.find('input[name=pick_date_to]').val('');
        form_elem.find('input[name=criteria]').val('');
    };    

    /**
     * Nodrošina saites "Notīrīt" funkcionalitāti - dzēš formas lauku vērtības
     *
     * @returns {undefined}
     */
    var handleLinkClear = function() {
        page_elem.find(".dx-clear-link").click(function() {
            clearFields();
            clearLinkShowHide();
        });
    };

    /**
     * Inicializē vērtību notīrīšanas saites parādīšanu/paslēpšanu, mainoties formas lauku vērtībām
     * 
     * @returns {undefined}
     */
    var handleValueChange = function() {
        form_elem.find("input").keyup(function() {
            clearLinkShowHide();
        });

        form_elem.find('input[name=pick_date_from]').datetimepicker({
            onChangeDateTime:function(dp,$input){
                clearLinkShowHide();
            }
        });
            
        form_elem.find('input[name=pick_date_to]').datetimepicker({
            onChangeDateTime:function(dp,$input){
                clearLinkShowHide();
            }
        });
    };
    
    /**
     * Nodrošina atrasto rakstu grupēšanu pa veidiem
     * 
     * @returns {undefined}
     */
    var handleFiltering = function() {
        
        if (page_elem.attr("dx_articles_count") == "0") {
            return;
        }
        
        $('.cbp-filter-item').click(function(e){
            e.preventDefault();

            var type = $(this).attr('dx_id');
            var current_type = $('#current_type').val();
            if (current_type == type)
            {
                return;
            }

            $('.cbp-filter-item').removeClass('cbp-filter-item-active');
            $(this).addClass('cbp-filter-item-active');

            $('#feed_area_' + block_guid).removeClass('type_frame_' + current_type).addClass('type_frame_' + type);

            $('#current_type').val(type);

            $("#feed_area_" + block_guid).jscroll({
                loadingHtml: '<img src="' + getBaseUrl() + 'mindwo/img/loading.gif" alt="Datu ielāde..." /> Datu ielāde...',
                padding: 20,
                nextSelector: 'ul.pager li > a[rel=next]',
                callback: function() {
                            $("#feed_area_" + block_guid + " .pager").hide();
                },
                contentSelector: ".article_row_area",
                force_load: true
            });
        });        
    };

    /**
     * Inicializē meklēšnas rīku parādīšanu pie lapas ielādes - parāda, ja bija meklēšanas pēc kāda no redzamajiem formas laukiem
     * 
     * @returns {undefined}
     */
    var initSearchTools = function()
    {
        if (
                getElementVal(page_elem, 'input[name=pick_date_from]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_to]', "") 
                )
        {
            SearchTools.showHideTools();
        }
    };
    
    /**
     * Uzstāda datuma ievades laukiem noklūsēto paskaidrojošo tekstu
     * 
     * @returns {undefined}
     */
    var initDatePickPlaceHolder = function() {
        $('#search_form_pick_date_to').attr('placeholder', 'Publicēts līdz');
        $('#search_form_pick_date_from').attr('placeholder', 'Publicēts no');  
    };
    
    /**
     * Inicializē portāla ziņu lapas JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initPage = function() {        
        
        page_elem = $(".dx-articles-page");
        
        form_elem = page_elem.find('.search-tools-form');
        
        block_guid = page_elem.attr("dx_block_guid");
        
        SearchTools.init(clearLinkShowHide, clearFields);               

        handleValueChange();
        handleLinkClear();

        clearLinkShowHide();
        initSearchTools();
        
        enableScroll();
        handleFiltering();
        
        initDatePickPlaceHolder();
    };

    return {
        init: function() {
            initPage();
        }
    };
}();

$(document).ready(function() {
    PageArticles.init();
});