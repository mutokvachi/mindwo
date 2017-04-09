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
				$("#search_title").html($('span', this).text());
				$("#search_dropd").find("span").children().replaceWith($('i', this).clone());
				$("#searchType").val($('span', this).text());
				current_type = $('span', this).text();
				$("#search_criteria").focus();
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
        if(dx_is_cssonly)
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

        if(!dx_is_cssonly)
		{
			placeSearchBox();
		}
        
        // Pievienojam izslīdošā darbinieku rezultātu bloka pārzīmēšanas izsaukumu uz lapas/loga izmēra izmaiņām
        PageMain.addResizeCallback(initSidebarStyle);
        
		if(!dx_is_cssonly)
		{
			PageMain.addResizeCallback(placeSearchBox);
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