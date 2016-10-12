/**
 * Darbinieku izmaiņu bloka JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockEmplchanges = function()
{ 
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Darbinieku meklēšanas bloka HTML elements
     * 
     * @type type
     */
    var page_elem = null;
        
    /**
     * Atgriež elementa vērtību. Ja elements nav atrasts, tad noklusēto vērtību
     * 
     * @param {Object} parent_elem Vecākais elements, kurā tiks veikta meklēšana
     * @param {string} selector    Meklējamais elements
     * @param {mixed} default_val  Noklusētā vērtība
     * @returns {mixed}
     */
    var getElementVal = function(parent_elem, selector, default_val) {
        var elem = parent_elem.find(selector);

        if (!elem.length)
        {
            return default_val;
        }

        return elem.val();
    };

    /**
     * Parāda vai paslēpj saiti "Notīrīt kritērijus" atkarība ir vai nav norādīts vismaz viens kritērijis
     * 
     * @returns {undefined}
     */
    var clearLinkShowHide = function() {
        if (
                $('input[name="ch_new"]:checked').length > 0 ||
                $('input[name="ch_change"]:checked').length > 0 ||
                $('input[name="ch_leave"]:checked').length > 0 ||
                getElementVal(page_elem, 'input[name=criteria]', "") ||
                getElementVal(page_elem, 'input[name=date_from]', "") ||
                getElementVal(page_elem, 'select[name=source_id]', 0) > 0
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
        form_elem.find('input[name=criteria]').val('');
        form_elem.find('#defaultrange input').val('');
        form_elem.find('input[name=date_from]').val('');
        form_elem.find('input[name=date_to]').val('');
        form_elem.find('select[name=source_id]').val(0);
        $('.empl-checks').prop('checked', false);
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
        
        $(".empl-checks").change(function() {
            clearLinkShowHide();            
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
            el_date_from: "date_from",
            el_date_to: "date_to",
            page_selector: ".dx-empl-changes-search",
            form_selector: ".search-tools-form",
            arr_ranges: {
                    'Šodien': [moment(), moment()],
                    'Vakar': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Pirms 7 dienām': [moment().subtract('days', 6), moment()],
                    'Nākamo 7 dienu laikā': [moment(), moment().subtract('days', -6)],
                    'Šis mēnesis': [moment().startOf('month'), moment().endOf('month')],
                    'Iepriekšējais mēnesis': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                }
        };
        
        DateRange.init(arr_date_param, clearLinkShowHide);
    }
    
    /**
     * Inicializē darbinieku izmaiņu bloka JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initBlock = function() {        
        
        page_elem = $(".dx-empl-changes-search");
        form_elem = page_elem.find('.search-tools-form');
        
        

        handleValueChange();
        handleLinkClear();
        
        initDateRange();

        clearLinkShowHide();     
    };    

    return {
        init: function() {
            initBlock();
        }
    };
}();

$(document).ready(function() {
    BlockEmplchanges.init();
});