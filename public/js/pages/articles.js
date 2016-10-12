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
     * Pašlai ielādētais ziņu tips
     * 
     * @type Number
     */
    var current_loaded_type = 0;
    
    /**
     * Masīvs ar tipam atbilstoši ielādēto pēdejo lapu numuriem
     * 
     * @type Array
     */
    var type_page_arr = [];

    /**
     * Uzstāda pazīmi, vai tiek ielādēti dati - lai izvairītos no n reizes saklikšķināšanas
     * 
     * @type Number
     */
    var is_loading = 0;
    /**
     * Iespējo ziņu pielādi uz lapas ritināšanu
     * 
     * @returns {undefined}
     */
    var enableScroll = function() {
        
        $("#feed_area_" + block_guid).jscroll({
            loadingHtml: '<img src="' + getBaseUrl() + 'img/loading.gif" alt="Datu ielāde..." /> Datu ielāde...',
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
        form_elem.find('#defaultrange input').val('');
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
    };
    
    /**
     * Parāda vai paslēpj ziņu ierakstus atkarībā no filtrā izvēlētā tipa
     * 
     * @param {integer} type Ziņas tipa ID, ja 0, tad jārāda visas ziņas
     * @returns {undefined}
     */
    var showHideContent = function(type) {
        if (type == 0) {
            $("[class^='panel article_item_row type_']").show();
        }
        else {
            $("[class^='panel article_item_row type_']").hide();            
            $(".type_" + type).show();
        }
    };
    
    /**
     * Izgūst parametra vērtību no URL
     * 
     * @param {string} url POST saites URL ar parametriem
     * @param {string} param_name Parametra nosaukums
     * @returns {string} Parametra vērtība
     */
    var getUrlParamVal = function(url, param_name) {
        var parts_arr = url.split('&' + param_name + '=');
        
        var val_arr = parts_arr[1].split('&');
        
        return val_arr[0];
    };
    
    /**
     * Atgriež tipam atbilstoši ielādēto lapas numuru
     * 
     * @param {integer} type Tipa ID
     * @returns {integer} Lapas nr
     */
    var getTypePageNr = function(type) {
        if (type_page_arr[type] == null) {
            return 0;
        }
        
        return type_page_arr[type];
    }
    
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
            if (is_loading == 1) {
                return false;
            }
            
            e.preventDefault();

            var type = $(this).attr('dx_id');
            
            if (current_loaded_type == type)
            {
                return;
            }
            
            if (type == 0 || current_loaded_type == 0) {
                // visu jādzēš un jālādē no jauna - lai nelādētu dubultā
                $(".article_row_area").html('');
                type_page_arr=[];
            }
            
            $('.cbp-filter-item').removeClass('cbp-filter-item-active');
            $(this).addClass('cbp-filter-item-active');
            
            showHideContent(type);
            
            current_loaded_type = type;
            
            var cnt = parseInt($(this).attr('dx_count'));
            
            if ($(".type_" + type).length >= cnt) {                
                return; // viss ielādēts                
            }
            
            is_loading = 1;
            
            if ($('.pager a[rel=next]').length == 0) {

                var htm = "<ul class='pager'><li><a href='" + window.location.href + 
                        "?type=" + type + 
                        "&page=" + getTypePageNr(type) + 
                        "&criteria=" + page_elem.attr('dx_search_criteria') +
                        "&searchType=" + page_elem.attr('dx_search_searchType') +
                        "&pick_date_from=" + page_elem.attr('dx_search_pick_date_from') +
                        "&pick_date_to=" + page_elem.attr('dx_search_pick_date_to') +        
                        "' rel='next'></a></li></ul>";
                
                $( "#feed_area_" + block_guid ).append(htm);
                
            }
            else {
                var pager_url = $('.pager a[rel=next]').attr('href');
                
                var url_type = getUrlParamVal(pager_url, 'type');
                var url_page = getUrlParamVal(pager_url, 'page');                
                type_page_arr[url_type] = url_page;
                
                if (url_type != type) {                    
                    
                    pager_url = pager_url.replace("&type=" + url_type, "&type=" + type);
                    pager_url = pager_url.replace("&page=" + url_page, "&page=0");
                    $('.pager a[rel=next]').attr('href', pager_url);
                    
                }
            }
            
            $("#feed_area_" + block_guid).jscroll({
                loadingHtml: '<img src="' + getBaseUrl() + 'img/loading.gif" alt="Datu ielāde..." /> Datu ielāde...',
                padding: 20,
                nextSelector: 'ul.pager li > a[rel=next]',
                callback: function() {
                            $("#feed_area_" + block_guid + " .pager").hide();
                            showHideContent(type);
                            is_loading = 0;
                },
                contentSelector: ".article_row_area",
                force_load: true
            });
        });        
    };

    /**
     * Inicializē datuma intervāla uzstādīšanas komponenti
     * 
     * @returns {undefined}
     */
    var initDateRange = function() {
        var arr_date_param = {
            range_id: "defaultrange",
            el_date_from: "pick_date_from",
            el_date_to: "pick_date_to",
            page_selector: ".dx-articles-page",
            form_selector: ".search-tools-form",
            arr_ranges: {
                    'Šodien': [moment(), moment()],
                    'Vakar': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Pirms 7 dienām': [moment().subtract('days', 6), moment()],                    
                    'Šis mēnesis': [moment().startOf('month'), moment().endOf('month')],
                    'Iepriekšējais mēnesis': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                }
        };
        
        DateRange.init(arr_date_param, clearLinkShowHide);
    }
    
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
     * Paslēpj filtrus pēc tipa, ja ir tikai 1 tips
     * 
     * @returns {undefined}
     */
    var initTypeFilters = function() {
        
        if ($('#js-filters-juicy-projects .cbp-filter-item').length < 3) {
            $('#js-filters-juicy-projects').hide();
        }
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
        current_loaded_type = page_elem.attr('dx_current_type');
        
        var mode = page_elem.attr("dx_mode");
        
        SearchTools.init(clearLinkShowHide, clearFields);               

        handleValueChange();
        handleLinkClear();

        clearLinkShowHide();
        
        if (mode == "search") {
            initSearchTools();
            initDateRange();
        }
        
        enableScroll();
        handleFiltering();
        initTypeFilters();
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