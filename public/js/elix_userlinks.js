/**
 * Lapas augšējās joslas meklēšanas lauka funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var SearchTop = function()
{
    /**
     * Aktuālais meklēšanas tips
     * 
     * @type String
     */
    var current_type = "";

    /**
     * Paziņojuma teksts, ja nekas nav atrasts pēc norādītā kritērija
     * 
     * @type String
     */
    var txt_nothing_found = "Nav atrasts neviens atbilstošs ieraksts.";
    
    /**
     * Noklusētais paziņojuma teksts, kamēr vēl nav veikta darbinieku meklēšana
     * 
     * @type String
     */
    var txt_default_info = "Lūdzu, ievadiet vismaz trīs burtus, un tiks veikta dinamiskā darbinieku meklēšana.";
    
    /**
     * Employee label title
     * 
     * @type String
     */
    var txt_employee = "Darbinieki";
    
    /**
     * Searching in progress label
     * 
     * @type String
     */
    var txt_searching = "Meklē....";
    
    /**
     * Norāda, vai ir parādīts izslīdošais darbinieku meklēšanas rezultātu bloks
     * 
     * @type Number
     */
    var is_sidebar_shown = 0;
    
    var is_search_on_top = 1;

    var statusDisplay = $("#quick-search-status");
    
    /**
     * Veic AJAX pieprasījumu ar darbinieku meklēšanu
     * 
     * @returns {undefined}
     */
    var doAjaxSearch = function() {
        
        var criteria = $("#search_criteria").val().trim();
        
        if (criteria == "")
        {
            $(".dx-employees-quick-results").html('');
            statusDisplay.html(txt_default_info);
            return;
        }
        
        if (criteria.length < 3) {
            $(".dx-employees-quick-results").html('');
            statusDisplay.html(txt_default_info);
            return;
        }
        
        /*
        var formData = new FormData();
        formData.append("criteria", criteria);
        */
        var formData = "criteria=" + criteria;
        var ajax_url = 'ajax/employees';
        
        $(".dx-employees-quick-results").html('');
        statusDisplay.html(txt_searching);

        var request = new FormAjaxRequestIE9 (ajax_url, "", "", formData);            
        request.progress_info = "";

        request.callback = function(data) {

            if (data['html'].indexOf('employee-list') !== -1) {
                $(".dx-employees-quick-results").html(data['html']);

                EmployeesLinks.init($("#dx-top-search-div").find(".employee-list"), clearCriteria);
                statusDisplay.html('');
            }
            else {
                // nothing found
                statusDisplay.html(txt_nothing_found);
            }
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };    
    
    /**
     * Notīra darbinieku meklēšanas frāzes lauku
     * 
     * @returns {undefined}
     */
    var clearCriteria = function() {
        $("#search_criteria").val('');
    }
    
    /**
     * Nodrošina meklēšanas tipa izvēles apstrādi - uzstāda meklēšanas tipa tekstu un ieliek kursora fokusu meklēšanas laukā
     * 
     * @returns {undefined}
     */
    var handleTypeChoose = function() {
        $("#top_search a").each(function() {
            $(this).click(function(e) {
                $("#search_title").html($(this).text());
                $("#searchType").val($(this).text());
                current_type = $(this).text();
                $("#search_criteria").focus();                
            });
        });
    };
	
	var handleTypeChooseCSSOnly = function() {
		$("#top_search a").each(function() {
			$(this).click(function(e) {
			    var span = $('span', this);
				$("#search_title").html(span.text());
				$("#search_dropd").find("span").children().replaceWith($('i', this).clone());
				$("#searchType").val(span.text());
				current_type = span.text();
				$("#search_criteria").attr('placeholder', span.data('placeholder')).focus();
			});
		});
	};

    /**
     * Nodrošina meklēšanas izpildi uz pogas nospiešanu
     * 
     * @returns {undefined}
     */
    var handleBtnSearch = function() {
        $("#search_btn").click(function() {
            show_page_splash();
        });
    };
    
    var hideSideBar = function() {
        $('body').toggleClass('page-quick-sidebar-open');
        is_sidebar_shown = 0;
        $(".dx-employees-quick-results").html('');  
        statusDisplay.html(txt_default_info);

    };
    
    /**
     * Parāda izslīdošo darbinieku meklēšanas rezultātu bloku, ja ir fokuss uz meklēšanas lauku.
     * Bloku rāda tikai tad, ja meklēšanas tips ir Darbinieki
     * 
     * @returns {undefined}
     */
    var handleQuickSidebarToggler = function() {
        // quick sidebar toggler
        $('#search_criteria').on('keyup', function(e) {
            if (current_type == txt_employee)
            {
                if (is_sidebar_shown == 0 && is_search_on_top == 1)
                {                    
                    $('body').toggleClass('page-quick-sidebar-open');
                    is_sidebar_shown = 1;
                    
                    if ($("#search_criteria").val().trim() != "")
                    {
                        doAjaxSearch();
                    }
                }
            }
            else
            {
                if (is_sidebar_shown == 1)
                {
                    hideSideBar();
                }
            }
        });
    };

    /**
     * Paslēp darbinieku meklēšanas rezultātu bloku, ja nospiests krustiņš
     * 
     * @returns {undefined}
     */
    var handleSidebarHide = function() {
        $("a.close-quick-sidebar").click(function() {
            if (is_sidebar_shown == 1)
            {
                $('body').toggleClass('page-quick-sidebar-open');
                is_sidebar_shown = 0;
            }
        });
    };
    
    /**
     * Nodrošina darbinieku ātro meklēšanu - AJAX pieprasījuma izpilde, ja ievada kādu simbolu meklēšanas laukā
     * 
     * @returns {undefined}
     */
    var handleQuickSearch = function() {
        $("#search_criteria").keyup(function() {
            if (is_sidebar_shown)
            {
                doAjaxSearch();
            }
        });        
    };
    
    /**
     * Nodrošina darbinieku ātro meklēšanu - AJAX pieprasījuma izpilde, ja meklēšanas laukā iekopē kādu tekstu
     * 
     * @returns {undefined}
     */
    var handleCopyPasteSearch = function() {
        $('#search_criteria').bind('paste', function () {            
            if (current_type == txt_employee) {
                setTimeout(function () {
                    doAjaxSearch();
                }, 100);
            }
        });
    };
    
    /**
     * Uzstāda izslīdošā darbinieku meklēšanas rezultāta bloka platumu atbilstoši loga platumam
     * 
     * @returns {undefined}
     */
    var initSidebarStyle = function() {                
        
        var width = 760; //$( document ).width() - $("#top_search").offset().left;
        
        if ($("#employee_quick_css").length == 0)
        {
            $("head").append('<style type="text/css" id="employee_quick_css"></style>');
        }
        var newStyleElement = $("#employee_quick_css");
        newStyleElement.html('.page-quick-sidebar-wrapper{width:' + width + 'px; right: -' + width + 'px;}');
        
        if ( $.isFunction($.fn.slimScroll) ) {
            if ($(".dx-employees-quick .slimScrollDiv").length)
            {
                $(".dx-employees-quick-results").slimScroll({destroy: true});
            }

            $('.dx-employees-quick-results').slimScroll({
                height: $(".dx-employees-quick").height() + 'px',
                position: 'left',
                color: '#f1f4f7'
            });
        }
    };
    
    var placeSearchBox = function() {
        if ($( document ).width() > 990) {
            // show on top
            if (is_search_on_top){
                return;
            }
            
            var htm = $("#dx-search-box-in-page").html();
            $("#dx-search-box-in-page").html("");
            
            $("#dx-search-box-top-li").html(htm);
            is_search_on_top = 1;
            initHandlers();
        }
        else
        {            
            // show in page content
            if (!is_search_on_top){
                return;
            }
            
            var htm = $("#dx-top-search-form").parent().html();
            $("#dx-top-search-form").remove();
            
            $("#dx-search-box-in-page").html(htm);
            is_search_on_top = 0;
            initHandlers();
            
            if (is_sidebar_shown == 1)
            {
                hideSideBar();
            }
        }
        
    };
    
    /**
     * Initialize button handlers and styles
     * 
     * @returns {undefined}
     */
    var initHandlers = function() {
        if((typeof dx_is_cssonly !== 'undefined') && dx_is_cssonly)
        {
			handleTypeChooseCSSOnly();
        }
        else
		{
			handleTypeChoose();
		}
        handleBtnSearch();
        
        initSidebarStyle();
        handleQuickSidebarToggler();
        handleSidebarHide();
        handleQuickSearch();
        handleCopyPasteSearch();
    };
    
    /**
     * Initialize translations for messages and labels
     * 
     * @returns {undefined}
     */
    var initTranslations = function() {
        var search_obj = $("#dx-top-search-div");
        txt_nothing_found = search_obj.attr('trans_nothing_found');
        txt_default_info = search_obj.attr('trans_default_info');
        txt_employee = search_obj.attr('trans_employees');
        txt_searching = search_obj.attr('trans_searching');
    };
    
    /**
     * Inicializē slīdrādes bloku
     * 
     * @returns {undefined}
     */
    var initSearch = function()
    {
        initTranslations();
        
        initHandlers();
        
        var search_obj = $("#dx-top-search-div");
        
        $("#searchType").val(search_obj.attr('trans_default'));
        current_type = search_obj.attr('trans_default');

        if((typeof dx_is_cssonly === 'undefined') || !dx_is_cssonly)
        {
            placeSearchBox();
            PageMain.addResizeCallback(placeSearchBox);
        }
        
        // Pievienojam izslīdošā darbinieku rezultātu bloka pārzīmēšanas izsaukumu uz lapas/loga izmēra izmaiņām
        PageMain.addResizeCallback(initSidebarStyle);
        
        if (typeof HMenuUI !== "undefined" && typeof HMenuUI.positionateDIVs === "function") { 
            HMenuUI.positionateDIVs();
        }
        
    };

    return {
        init: function() {
            initSearch();
        }
    };
}();

$(document).ready(function() {
    $('[autofocus]:not(:focus)').eq(0).focus();
    SearchTop.init();
});
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
        request.progress_info = DX_CORE.trans_data_processing;                       

        request.callback = function(data) {

            notify_info(DX_CORE.trans_data_saved);          

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
            get_popup_item_by_id(0, 'ajax/form_password', DX_CORE.trans_passw_form_title);
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
//# sourceMappingURL=elix_userlinks.js.map
