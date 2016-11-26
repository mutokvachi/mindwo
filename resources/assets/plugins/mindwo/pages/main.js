/**
 * Galvenās lapas (kopīga visām lapām) JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageMain = function()
{

    /**
     * Lapas objekts
     * 
     * @type Object
     */
    var page_elem = null;

    /**
     * Lietotāja aktuālo uzdevumu skaits (ja ieslēgta darpblūsmu funkcionalitāte)
     * Nepieciešams, lai rādītu popup lodziņu ar aktuālajiem uzdevumiem
     * 
     * @type Number
     */
    var user_tasks_count = 0;

    /**
     * Pašreizējās ielādētās lapas Laravel route nosaukums
     * Nepieciešams, lai popup lodziņu ar aktuālajiem uzdevumiem nerādītu SVS tabulāro sarakstu skatos
     * 
     * @type String
     */
    var current_route = "";

    /**
     * Funkciju masīvs, kas būs jāizsauc, mainoties lapas izmēriem
     * Masīvā nepieciešamības gadījumā ievieto funckijas, kas maina izmērus lapā ievietotajiem elementiem
     * 
     * @type Array
     */
    var resize_functions_arr = [];
    
    var tab_win = null;

    /**
     * Relogin modal dialog obj
     * @type object
     */
    var reLoginModal = null;
    
    /**
     * Papildina datņu lejuplādes saites ar ikonām
     * 
     * @returns {undefined}
     */
    var setFilesLinksIcons = function() {
        
        setFilesLinksFa('jpg', 'fa fa-file-image-o');
        setFilesLinksFa('png', 'fa fa-file-image-o');
        setFilesLinksFa('gif', 'fa fa-file-image-o');
        setFilesLinksFa('txt', 'fa fa-file-text-o');
        setFilesLinksFa('pdf', 'fa fa-file-pdf-o');
        setFilesLinksFa('doc', 'fa fa-file-word-o');
        setFilesLinksFa('docx', 'fa fa-file-word-o');
        setFilesLinksFa('ppt', 'fa fa-file-powerpoint-o');
        setFilesLinksFa('pptx', 'fa fa-file-powerpoint-o');
        setFilesLinksFa('xls', 'fa fa-file-excel-o');
        setFilesLinksFa('xlsx', 'fa fa-file-excel-o');
        setFilesLinksFa('mp4', 'fa fa-file-video-o');
        setFilesLinksFa('mp3', 'fa fa-file-audio-o');
        
    };
    
    /**
     * Papildina datņu lejuplādes saites ar ikonām atbilstoši datnes paplašinājumam
     * 
     * @param {string} file_ext     Datnes paplašinājums
     * @param {string} icon_class   Ikonas klases nosaukums
     * @returns {undefined}
     */
    var setFilesLinksFa = function(file_ext, icon_class) {        
        $("a[href$='." + file_ext + "']").not(".dx-no-link-pic").each(function() {
            if ($(this).find('i').length == 0)
            {
                $(this).html("<i class='" + icon_class + "'></i> " +  $(this).html());  
            }
        });
    };
    
    /**
     * Izpilda visas funkcijas no masīva, ja lapai mainās izmērs
     * 
     * @returns {undefined}
     */
    var resizePage = function() {
        for (i = 0; i < resize_functions_arr.length; i++) {            
            resize_functions_arr[i]();
        }
    };
    
    /**
     * Pārzīmē lapas izskatu ņemot vērā cookie uzstādīto vērtību
     * 
     * @returns {undefined}
     */
    var resizePageFromCookie = function() {
        redrawPageLayout($.cookie("layout"));
    };
    
    /**
     * Pārzīmē lapas elementus uz lapas izskatu "Pa visu ekrānu"
     * 
     * @returns {undefined}
     */
    var resetLayoutToWide = function() {
        $("body").removeClass("page-boxed");

        $('.page-header > .page-header-inner').removeClass("container");

        if ($('.page-container').parent(".container").size() === 1) {
            $('.page-container').insertAfter('body > .clearfix');
        }

        if ($('.page-footer > .container').size() === 1) {
            $('.page-footer').html($('.page-footer > .container').html());
        } else if ($('.page-footer').parent(".container").size() === 1) {
            $('.page-footer').insertAfter('.page-container');
            $('.scroll-to-top').insertAfter('.page-footer');
        }

        $('body > .container').remove();
    };

    /**
     * Pārzīmē lapas elementus uz lapas izskatu "Fiksēts rāmis pa vidu"
     * 
     * @returns {undefined}
     */
    var resetLayoutToBoxed = function() {
        $("body").addClass("page-boxed");

        // set header
        $('.page-header > .page-header-inner').addClass("container");
        $('body > .clearfix').after('<div class="container"></div>');

        // set content
        $('.page-container').appendTo('body > .container');

        $('.page-footer').html('<div class="container">' + $('.page-footer').html() + '</div>');        
    };
    
    /**
     * Maina lapas izskatu - fiksēts rāmis lapas vidū, sānos joslas
     * 
     * @returns {undefined}
     */
    var makePageBoxed = function() {

        if ($("body").hasClass("page-boxed"))
        {
            return;
        }

        resetLayoutToBoxed();

        $("#btnScreen").attr("dx_attr", "boxed");
        $("#btnScreen").attr('title', DX_CORE.trans_page_fullscreen);
        $("#btnScreen").html('<i class="fa fa-arrows-alt"></i> ' + DX_CORE.trans_page_fullscreen + ' ');

        $.cookie("layout", "boxed");
    };

    /**
     * Maina lapas izskatu - visas lapas platumā
     * 
     * @returns {undefined}
     */
    var makePageWide = function() {
        resetLayoutToWide();

        $("#btnScreen").attr("dx_attr", "wide");
        $("#btnScreen").attr('title', DX_CORE.trans_page_boxed);
        $("#btnScreen").html('<i class="fa fa-arrows-h"></i> ' + DX_CORE.trans_page_boxed + ' ');

        $.cookie("layout", "wide");
    };
    
    /**
     * Pārzīmē lapu uz norādīto izskata variantu
     * 
     * @param {string} to_layout Izskata variants, wide - pa visu lapu, boxed - rāmis
     * @returns {undefined}
     */
    var redrawPageLayout = function(to_layout) {

        if (to_layout == "wide")
        {
            makePageWide();
        }
        else
        {
            makePageBoxed();
        }
        
        if ( $.isFunction($.fn.App) ) {
            App.runResizeHandlers();
        }
        
        if ( $.isFunction($.fn.Layout) ) {
            Layout.fixContentHeight(); // fix content height            
            Layout.initFixedSidebar(); // reinitialize fixed sidebar
        }
        
        resizePage();
    };

    /**
     * Uzstāda lapas atribūtā interneta pārlūka datus.
     * Tas nepieciešams, lai CSS varētu speciāli piemērot stilus atkarībā no pārlūka versijas
     * 
     * @returns {undefined}
     */
    var initUserAgentAttr = function() {
        var doc = document.documentElement;
        doc.setAttribute('data-useragent', navigator.userAgent);
    };

    /**
     * Uzstāda csrf-token vērtību visiem AJAX pieprasījumiem
     * Tas nepieciešams, lai darbotos Laravel drošības funkcija, kas validē POST pieprasījumus
     * 
     * @returns {undefined}
     */
    var initAjaxCSRF = function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    };

    /**
     * Uzstāda bootstrap modālo logu maksimālā augstuma nosacījumu
     * DX_CORE.form_height_ratio parametrs definējas js/dx_core.js
     * 
     * @returns {undefined}
     */
    var initFormHeight = function() {
        
        $.fn.modal.defaults.maxHeight = function() {
            var menu_h = $(window).height(); //$(".page-sidebar-menu").height();
            menu_h = menu_h - 246;
            
            return menu_h;// * DX_CORE.form_height_ratio;
        };
        
    };

    /**
     * Uzstāda globālos parametrus, lai funkcionētu SVS
     * 
     * @returns {undefined}
     */
    var initCoreParams = function() {
        DX_CORE.site_url = page_elem.attr("dx_root_url");
        App.setAssetsPath(DX_CORE.site_url + "assets/");
        
        DX_CORE.site_public_url = page_elem.attr("dx_public_root_url");
        DX_CORE.progress_gif_url = DX_CORE.site_url + "assets/global/progress/loading.gif";
        DX_CORE.valid_elements = page_elem.attr("dx_valid_html_elements");
        DX_CORE.valid_styles = page_elem.attr("dx_valid_html_styles");
        DX_CORE.max_upload_size = page_elem.attr("dx_max_file_size").replace("M", "");
        DX_CORE.post_max_size = page_elem.attr("dx_post_max_size");
        
        DX_CORE.trans_data_processing = page_elem.attr("trans_data_processing");
        DX_CORE.trans_please_wait = page_elem.attr("trans_please_wait");
        DX_CORE.trans_sys_error = page_elem.attr("trans_sys_error");
        DX_CORE.trans_session_end = page_elem.attr("trans_session_end");
        DX_CORE.trans_general_error = page_elem.attr("trans_general_error");
        DX_CORE.trans_first_save_msg = page_elem.attr("trans_first_save_msg");
        DX_CORE.trans_data_saved = page_elem.attr("trans_data_saved");
        DX_CORE.trans_data_deleted = page_elem.attr("trans_data_deleted");
        DX_CORE.trans_data_deleted_all = page_elem.attr("trans_data_deleted_all");
        DX_CORE.trans_word_generating = page_elem.attr("trans_word_generating");
        DX_CORE.trans_word_generated = page_elem.attr("trans_word_generated");
        DX_CORE.trans_excel_downloaded = page_elem.attr("trans_excel_downloaded");
        DX_CORE.trans_file_downloaded = page_elem.attr("trans_file_downloaded");
        DX_CORE.trans_file_error = page_elem.attr("trans_file_error");
        DX_CORE.trans_confirm_delete = page_elem.attr("trans_confirm_delete");
        DX_CORE.trans_page_fullscreen = page_elem.attr("trans_page_fullscreen");
        DX_CORE.trans_page_boxed = page_elem.attr("trans_page_boxed");
        DX_CORE.trans_passw_form_title = page_elem.attr("trans_passw_form_title");
        
        //tree
        DX_CORE.trans_tree_close = page_elem.attr("trans_tree_close");
        DX_CORE.trans_tree_chosen = page_elem.attr("trans_tree_chosen");
        DX_CORE.trans_tree_cancel = page_elem.attr("trans_tree_cancel");
        DX_CORE.trans_tree_choose = page_elem.attr("trans_tree_choose");
        
        user_tasks_count = page_elem.attr("dx_user_tasks_count");
        current_route = page_elem.attr("dx_current_route");
    };

    /**
     * Uzstāda notifikāciju izslīdošā lodziņa parametrus (novietojumu, attēlošanas veidu u.c.)
     * 
     * @returns {undefined}
     */
    var initNotifications = function() {
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
    };

    /**
     * Parāda popup lodziņu ar lietotāja aktuālajiem uzdevumiem, ja tādi ir
     * 
     * @returns {undefined}
     */
    var initUserTasksPopup = function() {

        if (user_tasks_count > 0 && current_route != "view" && current_route != "home") {
            
            setTimeout(function() {
                $.gritter.add({
                    title: 'Jums ir jāizpilda <font color="#F1C40F">' + user_tasks_count + '</font>' + ((user_tasks_count > 1) ? ' uzdevumi' : ' uzdevums'),
                    text: 'Dodieties uz <a href="' + DX_CORE.site_url + 'skats_aktualie_uzdevumi" class="text-warning">uzdevumu sadaļu</a> un izpildiet uzdevumus.',
                    time: 7000
                });
            }, 3000);
        }
    }; 
    
    /**
     * Uzstāda palīdzības popup formās uz datu laukiem, kuriem norādīti paskaidrojumi
     * 
     * @returns {undefined}
     */
    var initHelpPopups = function() {
        
        $('.dx-form-help-popup').tooltipster({
            theme: 'tooltipster-light',
            animation: 'grow',
            maxWidth: 300
        });
    };

    /**
     * Uzstāda popup funkcionalitāti visiem elementiem, kuriem definēts atribūts title
     * 
     * @returns {undefined}
     */
    var initSpecialTooltips = function() {
        $('[title]').tooltipster({
            theme: 'tooltipster-light',
            animation: 'grow'
        });
    }
    
    /**
     * Pārzīmē lapu uz lapas ielādi, ņemot vērā cookie uzstādīto lapas izskata veidu
     * Ja kreisā puses izvēlne ir sakļaut, tad jānogaida 500 milisekundes, lai lapa pabeidz korekti uzzīmēties
     * 
     * @returns {undefined}
     */
    var initPageSize = function(){
        if ($.cookie("sidebar_closed") == "1")
        {
            setTimeout(function() {
                resizePageFromCookie();
            }, 500);
        }
        else
        {
            resizePageFromCookie();
        }
    };
    
    /**
     * Nodrošina portletu izvēršanas/sakļaušanas funkcionalitāti uz attiecīgās ikonas nospiešanu
     * Saglabā cookie portleta stāvokli
     * 
     * @returns {undefined}
     */
    var handlePortletsHideShow = function(){
        $('div.tools a').click(function(){
            var portlet = $(this).parent().parent().parent();
            var portlet_id = portlet.attr('dx_block_id');

            if ($(this).hasClass('expand'))
            {
                $.cookie(portlet_id, "show");
                portlet.removeClass('dx-portlet-collapsed');
            }
            else
            {
                $.cookie(portlet_id, "hide");
                portlet.addClass('dx-portlet-collapsed');
            }
        });
    };
    
    /**
     * Ielādējot lapu, uzstāda portletu sākotnējo stāvokli (izvēsts vai sakļauts) atkarība no cookie saglabātā iepriekšējā stāvokļa
     * 
     * @returns {undefined}
     */
    var initPortletsShowHide = function() {
        $('div.tools a').each(function() {

            var portlet = $(this).parent().parent().parent();

            var portlet_id = portlet.attr('dx_block_id');

            if (portlet_id)
            {
                if ($.cookie(portlet_id)=="hide")
                {
                    portlet.find('.portlet-body').hide();
                    $(this).addClass('expand').removeClass('collapse');
                    portlet.addClass('dx-portlet-collapsed');
                }
            }
        });  
    };
   
    /**
     * Pievieno bootstrap modālajiem popup pārvietošanas funkcionalitāti
     * 
     * @returns {undefined}
     */
    var makeModalsDraggable = function() {
        /*
        $(".modal").draggable({
            handle: ".modal-header"
        });
        */
    };

    /**
     * IE9 pārlūka gadījumā atrisina problēmu ar teksta lauku placeholder funkcionalitāti
     * 
     * @returns {undefined}
     */
    var solveIE9Placeholders = function() {
        $('input, textarea').each(function() {
            $(this).placeholder();
        });
    };
    
    /**
     * Noņem progresa logu visai lapai, kad lapa ir ielādēta
     * 
     * @returns {undefined}
     */
    var handleSplashRemoval = function() {
        $(window).bind("load", function() {
            $('.splash').css('display', 'none');
            $('body').css('overflow', 'auto');
        });
    };
    
    /**
     * Nodrošina lapas pārzīmēšanu, ja nospiež kreisās puses izvēlnes sakļaušanas/izvēršanas pogu
     * 
     * @returns {undefined}
     */
    var handleBtnScreen = function(){
        $("#btnScreen").click(function(e) {

            var to_layout = "boxed";
            if ($(this).attr("dx_attr") != "wide")
            {
                to_layout = "wide";
            }
            redrawPageLayout(to_layout);
        });  
    };
    
    /**
     * Nodrošina lapas pārzīmēšanu, ja tiek mainīts pārlūka loga izmērs
     * 
     * @returns {undefined}
     */
    var handleWindowResize = function() {
        $(window).resize(function() {
            setTimeout(function() {
                resizePageFromCookie();
            }, 500);
        });  
    };
    
    /**
     * Handles window resize events for horizontal menu UI
     * 
     * @returns {undefined}
     */
    var handleWindowResizeHorUI = function() {
        $(window).resize(function() {
            setTimeout(function() {
                resizePage();
            }, 500);
        });  
    };
    
    /**
     * Uzstāda ziņu saitēm, lai tās atver jaunā pārlūka TAB un foksuē TABu.
     * Funkcionalitāte tiek uzstādīta arī mākoņa saitēm.
     * 
     * @returns {undefined}
     */
    var handleTargetedLinkClick = function() {
        $("a[target=_dx_portal], .jqcloud a").each(function() {            
            
            if ($(this).attr('is_target_set') == 1) {
                return;
            }
            
            $(this).click(function(e) {
                e.preventDefault();

                if (isTabInstance()) {
                    tab_win.close();
                }

                tab_win = window.open($(this).attr("href"),"_dx_portal");            
            });
            
            $(this).attr('is_target_set', 1);
        });        
    };
    
    /**
     * Pārbauda, vai TABs jau ir bijis atvērts
     * 
     * @returns {Boolean}
     */
    var isTabInstance = function() {
        if (tab_win == null) {
            return false;
        }
        
        if (tab_win.closed) {
            return false;            
        }
        
        return true;
    };
    
    /**
     * Izceļ aktivizēto izvēlni (jo cache glabājas sākotnējais izcēlums - to noņemam)
     * 
     * @returns {undefined}
     */
    var setActiveMenu = function() {      
        $(".page-sidebar-menu .nav-item").removeClass("active").removeClass("open");
        $(".page-sidebar-menu .nav-item span.selected").remove();

        var active_item = $('.page-sidebar-menu .nav-item a[href="' + window.location.href + '"]');
        
        if (active_item.length == 0) {
            return; // nav ielādēta lapa no menu
        }
        
        active_item.parent().addClass("active").addClass("open");
        
        if (active_item.attr("data-level") == 0) {
          active_item.append('<span class="selected"></span>');
        }
        else {
            setActiveParentMenu(parseInt(active_item.attr("data-level")), active_item.parent());
        }
        
    };
    
    /**
     * Rekursīvi aktivizē menu elementa vecākos elementus līdz pašam pirmajam līmenim
     * @param {integer} level Līmenis
     * @param {object} elem Menu elements
     * @returns {undefined}
     */
    var setActiveParentMenu = function(level, elem) {
        
        if (level == 0) {
            elem.find("a").first().append('<span class="selected"></span>');
            return;
        }
        else {
            elem.parent().parent().addClass("active").addClass("open"); // te mēs analizējam <li> elementus
            setActiveParentMenu(level-1, elem.parent().parent());
        }  
    };
    
    /**
     * Fix slider/menu issue (metronic theme hack)
     * 
     * @returns {undefined}
     */
    var reset_margin = function() {
        $('#td_data').css('margin-left', 0);
        //$('#td_data').css('background', 'rgba(224,234,255,0.95)');
        var page_width = $('.page-bar').width();
        var min_h = $('#slide-page-holder').height();
        var page_header_h = $('.page-header').height();

        $('#slides-container div.row').each(function() {
            var h = $(this).height();
            if (h > min_h) {
                min_h = h;
            }
        });

        // -20 because of .page-bar padding
        $('#slide-page-holder').width(page_width - 20);
        $('#td_data').css('min-height', min_h + page_header_h);
    };
    
    /**
     * Handles AJAX response status - display errors if needed
     * @param {object} xhr AJAX response object
     * @returns {undefined}
     */
    var showAjaxError = function(xhr) {
        
        // 401 (session ended) is handled in the file resources/assets/plugins/mindwo/pages/re_login.js
        if (xhr.status == 200) {
            return;
        }
        
        // session ended - relogin required
        if (xhr.status == 401) {
            hide_page_splash(1);
            hide_form_splash(1);
            reLoginModal.modal("show");
            return;
        }
        
        notify_err(getAjaxErrorText(xhr));
        
        hide_page_splash(1);
        hide_form_splash(1);
    };
    
    /**
     * Gets error message from AJAX error response
     * 
     * @param {type} xhr
     * @returns {string} Error message
     */
    var getAjaxErrorText = function(xhr) {
        var err_txt = "";
        var json = xhr.responseJSON;
        
        // Validation errors handling
        if ( xhr.status === 422 ) 
        {            
            var errorsHtml= '<ul>';
            $.each( json, function( key, value ) {
                errorsHtml += '<li>' + value[0] + '</li>'; 
            });
            errorsHtml += '</ul>';
            err_txt = errorsHtml;
        }
        else {
            
            if (typeof json == "undefined") {
                try {
                    json = JSON.parse(xhr.responseText);
                }
                catch (e) {}
            }
            
            if ( typeof json != "undefined" && typeof json.success != "undefined" && json.success == 0 && typeof json.error != "undefined" )
            {
                err_txt = json.error;                
            }
        }
        
        if (!err_txt) {
            // unknown error
            err_txt = DX_CORE.trans_general_error;
        }
        
        return err_txt;
    };
    
    /**
     * Inicializē galvenās lapas JavaScript funkcionalitāti.
     * Izpildās, kamēr vēl nav visa lapa līdz galam ielādēta.
     * 
     * @returns {undefined}
     */
    var initPage = function() {

        page_elem = $("body.dx-main-page");

        initUserAgentAttr();
        initAjaxCSRF();
        initFormHeight();
        initCoreParams();
        initNotifications();

        handleSplashRemoval();
    };

    /**
     * Inicializē funkcionalitāti, kad lapa ir jau līdz galam ielādēta
     * 
     * @returns {undefined}
     */
    var initPageLoaded = function() {
        
        reLoginModal = reLogin.auth_popup;
        reLoginModal.on('shown.bs.modal', function () {
            reLoginModal.find("input[name='user_name']").val("").focus();
            reLoginModal.find("input[name='password']").val("");
        });
        
        initUserTasksPopup();     
        
        initPortletsShowHide();
        handlePortletsHideShow();
        
        handleTargetedLinkClick();
        
        setFilesLinksIcons();
        
        if ($("body").hasClass("dx-horizontal-menu-ui")) {            
            handleWindowResizeHorUI();
            resizePage();
        }
        else {
            handleBtnScreen();      
            handleWindowResize();
            initPageSize();
            setActiveMenu();
        }                
            
        if (dx_is_slider == 1) {
            reset_margin();        
            addResizeCallback(reset_margin);
        }
    };

    /**
     * Pievieno masīvam funkciju, kas būs jāizsauc lapas izmēru mainīšanas gadījumā
     * 
     * @param {function} callback   Izsaucamā funkciaj
     * @returns {undefined}
     */
    var addResizeCallback = function(callback) {
        resize_functions_arr.push(callback);
    };
    
    function showConfirm(){
        
    };

    return {
        init: function() {
            initPage();
        },
        initPageLoaded: function() {
            initPageLoaded();
        },
        addResizeCallback: function(callback) {
            addResizeCallback(callback);
        },
        modalsDraggable: function() {
            makeModalsDraggable();
        },
        initHelpPopups: function() {
            initHelpPopups();
            initSpecialTooltips();
        },
        initFilesIcons: function() {
            setFilesLinksIcons();
        },
        initTargetLinks: function() {
            handleTargetedLinkClick();
        },
        initAjaxCSRF: function() {
            initAjaxCSRF();
        },
        resizePage: function() {
            resizePage();
        },
        errorHandler: function(xhr) {
            showAjaxError(xhr);
        },
        getAjaxErrTxt: function(xhr) {
            return getAjaxErrorText(xhr);
        }
    };
}();


PageMain.init();

$(document).ready(function() {
    PageMain.initPageLoaded();
    PageMain.initHelpPopups();
    $(this).scrollTop(0,0);
});

$(document).ajaxComplete(function(event, xhr, settings) {      
    PageMain.errorHandler(xhr);
    PageMain.modalsDraggable();
    PageMain.initHelpPopups();
    PageMain.initFilesIcons();
});
