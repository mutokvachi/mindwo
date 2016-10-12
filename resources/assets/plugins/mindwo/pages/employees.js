/**
 * Darbinieku meklēšanas lapas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageEmployees = function()
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
                    content: page_elem.attr("trans_unit_hint")
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
                getElementVal(page_elem, 'input[name=phone]', "") ||
                getElementVal(page_elem, 'input[name=position]', "")
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
        form_elem.find('input[name=position]').val('');
        form_elem.find('input[name=phone]').val('');
        form_elem.find('input[name=department_id]').val(0);
        
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

            get_popup_item_by_id_ie9(company_id, 'ajax/departments', page_elem.find("select[name=source_id] option:selected").text() + ' - ' + page_elem.attr("trans_choosing_unit"), initTree);
        });
        
        // uzstāda deparamenta id uz 0, gadījumā ja manuāli rediģē departamenta teksta lauku
        page_elem.find("input[name=department]").change(function(e) {
           page_elem.find('input[name=department_id]').val(0); 
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
            page_elem.find("input[name=department_id]").val(0);
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
     * Handles new employee button click - opens form for new employee entering
     * 
     * @returns {undefined}
     */
    var handleNewEmployeeBtn = function() {
        page_elem.find(".dx-employee-new-add-btn").click(function() {
            new_list_item(page_elem.attr("dx_empl_list_id"), 0, 0, "", "");
        });
    }

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
                getElementVal(page_elem, 'input[name=phone]', "") ||
                getElementVal(page_elem, 'input[name=position]')
                )
        {
            SearchTools.showHideTools();
        }
    };
    
    /**
     * Inicializē darbinieku lapas JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initPage = function() {        
        
        page_elem = $(".dx-employees-page");
        form_elem = page_elem.find('.search-tools-form');
        
        SearchTools.init(clearLinkShowHide, clearFields);
                
        handleDepartmentChoose();
        handleDepartmentClear();

        EmployeesLinks.init(page_elem, clearFields);

        handleValueChange();
        handleLinkClear();
        handleSourceChange();

        clearLinkShowHide();
        initSearchTools();
        showHideWarning();
        handleNewEmployeeBtn();
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
                var node = data.instance.get_node(data.selected[i], true);
                
                $(".dx-employees-page input[name=department]").val(node.text());
                $(".dx-employees-page input[name=department_id]").val(node.attr("data-id"));
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
    PageEmployees.init();
});

$(document).ajaxComplete(function(event, xhr, settings) {
    PageEmployees.initTree();
});