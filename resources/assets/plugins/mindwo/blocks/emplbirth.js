/**
 * Darbinieku dzimšanas dienu bloka JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockEmplbirth = function()
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
     * Parāda vai paslēpj brīdinājumu, ka jānorāda uzņēmums, lai izvēlētos sturktūrvienību
     * 
     * @param {Object} page_elem Lapas elements
     * @returns {undefined}
     */
    var showHideWarning = function(){
        var btn_choose = page_elem.find(".dx-tree-value-choose-btn");
        
        if (getElementVal(page_elem, 'select[name=source_id]', 0) > 0){
            // noņemam brīdinājumu, ja bija uzstādīts            
            if (btn_choose.hasClass('tooltipstered')) {
                btn_choose.tooltipster('disable');
            }            
        }
        else{             
            if (!btn_choose.hasClass('tooltipstered')) {
                // uzstādam brīdinājumu pirmo reizi
                btn_choose.tooltipster({
                    theme: 'tooltipster-light',
                    animation: 'grow',
                    content: 'Lai izvēlētos struktūrvienību, vispirms ir jānorāda uzņēmums!'
                });
            }
            else
            {
                // iespjojam jau uzstādītu brīdinājumu
                btn_choose.tooltipster('enable');
            }
        }
    };

    /**
     * Parāda vai paslēpj saiti "Notīrīt kritērijus" atkarība ir vai nav norādīts vismaz viens kritērijis
     * 
     * @returns {undefined}
     */
    var clearLinkShowHide = function() {
        if (
                getElementVal(page_elem, 'select[name=source_id]', 0) > 0 ||
                getElementVal(page_elem, 'input[name=department]', "") ||
                getElementVal(page_elem, 'input[name=criteria]', "") ||
                getElementVal(page_elem, 'input[name=date_from]', "")
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
        form_elem.find('select[name=source_id]').val(0);
        form_elem.find('input[name=department]').val('');
        form_elem.find('#defaultrange input').val('');
        form_elem.find('input[name=date_from]').val('');
        form_elem.find('input[name=date_to]').val('');
        
        // uzstāda brdīdinājumu, ka nav norādīts uzņēmums (nevar izvēlēties struktūrvienību)
        showHideWarning();
    };

    /**
     * Nodrošina struktūrvienību izvēlnes loga popup atvēršanu
     * 
     * @returns {undefined}
     */
    var handleDepartmentChoose = function() {
        page_elem.find(".dx-tree-value-choose-btn").click(function(e) {

            var company_id = page_elem.find("select[name=source_id]").val();

            if (company_id == 0)
            {
                page_elem.find("select[name=source_id]").focus();
                page_elem.find("select[name=source_id]").simulate('mousedown');
                return;
            }

            get_popup_item_by_id_ie9(company_id, 'ajax/departments', page_elem.find("select[name=source_id] option:selected").text() + ' - struktūrvienības izvēle', initTree);
        });
    };

    /**
     * Nodrošina struktūrvienības lauka notīrīšanu
     * 
     * @returns {undefined}
     */
    var handleDepartmentClear = function() {
        page_elem.find(".dx-tree-value-clear-btn").click(function(e) {
            page_elem.find("input[name=department]").val('');
            clearLinkShowHide(page_elem);
        });
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
     * Nodrošina brīdinajuma uzstādīšanu/paslēpšanu, atkarībā vai norādīts uzņēmums
     * 
     * @returns {undefined}
     */
    var handleSourceChange = function(){
        form_elem.find("select[name=source_id]").change(function() {
            showHideWarning();
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
                getElementVal(page_elem, 'input[name=department]', "") ||
                getElementVal(page_elem, 'input[name=date_from]', "")
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
            el_date_from: "date_from",
            el_date_to: "date_to",
            page_selector: ".dx-employees-birth-block",
            form_selector: ".search-tools-form",
            arr_ranges: {
                    'Šodien': [moment(), moment()],
                    'Vakar': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Rīt': [moment().add('days', 1), moment().add('days', 1)],
                    'Nākamo 7 dienu laikā': [moment(), moment().subtract('days', -6)],
                    'Šis mēnesis': [moment().startOf('month'), moment().endOf('month')],
                    'Iepriekšējais mēnesis': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                }
        };
        
        DateRange.init(arr_date_param, clearLinkShowHide);
    }
    
    /**
     * Inicializē darbinieku lapas JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initPage = function() {        
        
        page_elem = $(".dx-employees-birth-block");
        form_elem = page_elem.find('.search-tools-form');
        
        SearchTools.init(clearLinkShowHide, clearFields);
                
        handleDepartmentChoose();
        handleDepartmentClear();

        handleValueChange();
        handleLinkClear();
        handleSourceChange();
        
        EmployeesLinks.setFormUrl('search');
        EmployeesLinks.init($(".dx-employees-birth-page"), clearFields);
        
        initDateRange();

        clearLinkShowHide();
        initSearchTools();
        showHideWarning();       
    };

    /**
     * Inicializē struktūrvienību koku
     * 
     * @returns {undefined}
     */
    var initTree = function() {        
        $(".dx-department-tree-container").on('select_node.jstree', function (e, data) {
            var i, j;
            for(i = 0, j = data.selected.length; i < j; i++) {
                $(".dx-employees-birth-block input[name=department]").val(data.instance.get_node(data.selected[i], true).text());
                $( '#popup_window' ).modal('hide');
                break;
            }
            
        }).jstree({
            "core" : {"multiple" : false },
            
        });
    };

    return {
        init: function() {
            initPage();
        },
        initTree: function() {
            initTree();
        }
    };
}();

$(document).ready(function() {
    BlockEmplbirth.init();
});

$(document).ajaxComplete(function(event, xhr, settings) {
    BlockEmplbirth.initTree();
});