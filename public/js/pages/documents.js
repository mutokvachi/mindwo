/**
 * Lotus Notes dokumentu meklēšanas lapas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageDocuments = function()
{
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;

    /**
     * Lotus Notes dokumentu meklēšanas bloka HTML elements
     * 
     * @type type
     */
    var page_elem = null;

    /**
     * Parāda vai paslēpj saiti "Notīrīt kritērijus" atkarībā ir vai nav norādīts vismaz viens kritērijs
     * 
     * @returns {undefined}
     */
    var clearLinkShowHide = function() {
        if (
                getElementVal(page_elem, 'select[name=source_id]', 0) > 0 ||
                getElementVal(page_elem, 'select[name=kind_id]', 0) > 0 ||
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

        form_elem.find('select[name=source_id]').val(0);
        form_elem.find('select[name=kind_id]').val(0);
        
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

        form_elem.find("select").change(function() {
            clearLinkShowHide();
        });
    };

    /**
     * Nodrošina dokumentu formas atvēršanu
     * 
     * @returns {undefined}
     */
    var handleDocLinks = function() {
        $("a.dx-lotus-btn").click(function() {
            view_list_item("form", $(this).attr('item_id'), $(this).attr('list_id'), 0, 0, "", "");            
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
                getElementVal(page_elem, 'select[name=source_id]', 0) > 0 ||
                getElementVal(page_elem, 'select[name=kind_id]', 0) > 0 ||
                getElementVal(page_elem, 'input[name=pick_date_from]', "") ||
                getElementVal(page_elem, 'input[name=pick_date_to]', "")
                )
        {
            SearchTools.showHideTools();
        }
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
            page_selector: ".dx-documents-page",
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
     * Inicializē dokumentu JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initPage = function() {

        page_elem = $(".dx-documents-page");

        form_elem = page_elem.find('.search-tools-form');

        SearchTools.init(clearLinkShowHide, clearFields);

        handleValueChange();
        handleLinkClear();

        clearLinkShowHide();
        initSearchTools();

        initDateRange();
        
        handleDocLinks();
    };

    return {
        init: function() {
            initPage();
        }
    };
}();

$(document).ready(function() {
    PageDocuments.init();
});