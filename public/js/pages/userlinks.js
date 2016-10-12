/**
 * SVS lietotāja profila funkcionalitāte - paroles maiņa
 * 
 * @type _L4.Anonym$0|Function
 */
var UserLinks = function()
{
    /**
     * Paroles nomaiņas formas elements
     * 
     * @type Object
     */
    var pass_form = null;
    
    /**
     * Izpilda paroles maiņas AJAX pieprasījumu - saglabā jauno paroli
     * 
     * @returns {undefined}
     */
    var changePassw = function() {
        
        var formData = new FormData();
        formData.append("pass_old", pass_form.find("input[name='pass_old']").val());
        formData.append("pass_new1", pass_form.find("input[name='pass_new1']").val());
        formData.append("pass_new2", pass_form.find("input[name='pass_new2']").val());

        var ajax_url = '/ajax/change_password';

        var request = new FormAjaxRequest (ajax_url, "", "", formData);            
        request.progress_info = "Saglabā datus... Lūdzu, uzgaidiet...";                       

        request.callback = function(data) {

            notify_info("Parole veiksmīgi nomainīta!");          

        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };    
    
    /**
     * Uzstāda formas laukiem validatoru
     * 
     * @returns {undefined}
     */
    var setValidator = function() {
        pass_form.validator({            
            errors: {
                auto: 'Nav norādīta vērtība!'
            },
            feedback: {
                success: 'glyphicon-ok',
                error: 'glyphicon-alert'
            }
        }); 
    };
    
    /**
     * Nodrošina paroles maiņas formas atvēršanu
     * 
     * @returns {undefined}
     */
    var handleLinkPassword = function() {
        $("a.dx-user-change-passw-link").click(function(){
            get_popup_item_by_id(0, 'ajax/form_password', 'Paroles maiņa');
        });
    };
    
    /**
     * Nodrošina paroles maiņas saglabāšanas izsaukumu - pogas "Mainīt paroli" nospiešana
     * 
     * @returns {undefined}
     */
    var handleBtnChange = function() {
        pass_form.find(".dx-user-change-passw-btn").click(function() {
            changePassw();
        });
    };
    
    /**
     * Uzstāda laukiem validatoru. Ja validatora komponente vēl netika iekļauta, tad dinamiski to iekļauj
     * 
     * @returns {undefined}
     */
    var handleValidator = function() {        
        if ( !$.isFunction($.fn.validator) ) {
            $.getScript( getBaseUrl() + "plugins/validator/validator.js", setValidator);
        }
        else{
            setValidator();
        }
    };
    
    /**
     * Inicializē lietotāja profila iespējas
     * 
     * @returns {undefined}
     */
    var initProfile = function()
    {
        handleLinkPassword();        
    };
    
    /**
     * Inicializē paroles nomaiņas formu - tā tiek ielādēta ar AJAX
     * 
     * @returns {undefined}
     */
    var initPasswForm = function() {
        
        if ($(".dx-user-change-passw-form[dx_block_init='0']").length == 0) {
            return; // jau ir inicializēts
        }
        
        pass_form = $(".dx-user-change-passw-form");
        
        handleBtnChange();
        handleValidator();
        
        pass_form.attr("dx_block_init", 1);
        
        setTimeout(function() {
            pass_form.find("input[name='pass_old']").focus();
        }, 500);
        
    };

    return {
        init: function() {
            initProfile();
        },
        initPasswForm: function() {
            initPasswForm();
        }
    };
}();

$(document).ready(function() {
    UserLinks.init();
});

$(document).ajaxComplete(function(event, xhr, settings) {
    UserLinks.initPasswForm();
});