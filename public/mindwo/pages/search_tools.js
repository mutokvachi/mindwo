/**
 * Meklēšanas rīku pogas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var SearchTools = function()
{    
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Funkcija, kas parāda vai paslēp saiti "Notīrīt"
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearLinkShowHide = null;
    
    /**
     * Funkcija, kas notīra meklēšanas lauku vērtības.
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearFields = null;
    
    /**
     * Parāda vai paslēp meklēšanas rīkus
     * 
     * @returns {undefined}
     */
    var showHideTools = function() {

        var tools_btn = form_elem.find('.search-tools-btn');

        if (tools_btn.find('i').hasClass('fa-caret-down'))
        {
            tools_btn.find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
            tools_btn.removeClass('btn-primary').addClass('btn-default');
            form_elem.removeClass('search-tools-hiden').addClass('search-tools-shown');
        }
        else
        {
            tools_btn.find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
            tools_btn.removeClass('btn-default').addClass('btn-primary');

            form_elem.removeClass('search-tools-shown').addClass('search-tools-hiden');

            var phrase = form_elem.find('input[name=criteria]').val();
            
            clearFields();
            
            form_elem.find('input[name=criteria]').val(phrase);
            
            clearLinkShowHide();
        }
    };

    /**
     * Nodrošina meklēšanas pogas nospiešanas funkcionalitāti - veic meklēšanu
     * 
     * @returns {undefined}
     */
    var handleBtnSearch = function() {
        form_elem.find(".search-simple-btn").click(function(e) {
            
            show_page_splash();
            form_elem.submit();
        });
    };

    /**
     * Nodrošina meklēšanas rīku pogas nospiešanas funkcionalitāti - parāda vai paslēpj meklēšanas rīkus
     * 
     * @returns {undefined}
     */
    var handleBtnTools = function() {
        form_elem.find('.search-tools-btn').click(function(e) {
            showHideTools();
        });
    }; 
    
    /**
     * Inicializē meklēšanas rīku JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initTools = function(callback_clearLinkShowHide, callback_clearFields) {        

        form_elem = $('.search-tools-form');
        
        clearLinkShowHide = callback_clearLinkShowHide;
        clearFields = callback_clearFields;
        
        handleBtnSearch();
        handleBtnTools();
    };

    return {
        init: function(callback_clearLinkShowHide, callback_clearFields) {
            initTools(callback_clearLinkShowHide, callback_clearFields);
        },
        showHideTools: function() {
            showHideTools();
        }
    };
}();
