/**
 * Galvenās lapas (kopīga visām lapām) JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageMain = function()
{
    var is_grid_resize_callback_added = 0;

    var last_view_loaded_id = 0;
    
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

    /**
     * Resize function callbacks IDs array
     * 
     * @type Array
     */
    var callbacks_ids = [];

    /**
     * Indicates if now code is executed inside function timerResizePage()
     */
    var is_in_timer = false;
    
    /**
     * New tab object - for links opened from menu
     */
    var tab_win = null;

    /**
     * Relogin modal dialog obj
     * @type object
     */
    var reLoginModal = null;

    /**
     * Stores window height to be used in timer each 500 miliseconds to check if resize was done
     * 
     * @type integer
     */
    var win_h = 0;

    /**
     * Stores window width to be used in timer each 500 miliseconds to check if resize was done
     * 
     * @type integer
     */
    var win_w = 0;
    
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
     * Checks if window size was changes - if yes then executes call backs
     */
    var timerResizePage = function() {
        
        if (is_in_timer) {
            return;
        }
        
        var cur_win_h = $(window).height();
        var cur_win_w = $(window).width();

        if (win_h != cur_win_h || win_w != cur_win_w) {
            console.log("Timer resize page start: cur h=" + cur_win_h + " old h=" + win_h + " cur w=" + cur_win_w + " old w=" + win_w);
            
            executeResizeCallbacks();
            
            win_h = cur_win_h;
            win_w = cur_win_w;
            console.log("Timer resize page end");
        }
        
        is_in_timer = false;
    };

    /**
     * Executes resize callbacks from array
     */
    var executeResizeCallbacks = function() {
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
        
        executeResizeCallbacks();
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
        DX_CORE.post_max_size = page_elem.attr("dx_post_max_size").replace("M", "");
        
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

        DX_CORE.no_grid_height_resize = (page_elem.data('no-grid-height-resize')) ? 1 : 0;
    };

    /**
     * Uzstāda notifikāciju izslīdošā lodziņa parametrus (novietojumu, attēlošanas veidu u.c.)
     * 
     * @returns {undefined}
     */
    var initNotifications = function() {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "positionClass": "toast-top-left",
            "preventDuplicates": true,
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
     * Uzstāda palīdzības popup formās uz datu laukiem, kuriem norādīti paskaidrojumi
     * 
     * @returns {undefined}
     */
    var initHelpPopups = function() {
        
        $('.dx-form-help-popup:not(.tooltipstered)').tooltipster({
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
        $('[title]:not(.tooltipstered)').filter(function(i){
            return $(this).attr('title') != "" && !$(this).hasClass('dx-dont-tooltipster');
        }).tooltipster({
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
            //$('body').css('overflow', 'auto');
			$('body').css('overflow', 'visible');
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
    * Delay events with the same id, good for window resize events, keystroke, etc
    * 
    * @param {Function} func : callback function to be run when done
    * @param {Integer} wait : integer in milliseconds
    * @param {String} id : unique event id
    */
    var delayedEvent = (function () {
        var timers = {};

        return function (func, wait, id) {
            wait = wait || 200;
            id = id || 'anonymous';
            if (timers[id]) {
                clearTimeout(timers[id]);
                console.log('Delayed timer cleared for: ' + id);
            }
            console.log('Delayed timer set for: ' + id);
            timers[id] = setTimeout(func, wait);
        };
    })();
        
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
        var screenH = $( window ).height();
        var dataH;

        $('#slides-container div.row').each(function() {
            var h = $(this).height();
            if (h > min_h) {
                min_h = h;
            }
        });

        dataH = screenH > (min_h + page_header_h) ? screenH : (min_h + page_header_h);

        // -20 because of .page-bar padding
        $('#slide-page-holder').width(page_width - 20);
        $('#td_data').css('min-height', dataH);
        
        // Fix logo - remove toggler icons
        $('div.page-logo').css('width', '150px');
        $('div.page-logo .sidebar-toggler').css('display', 'none');
        $('div.page-header-inner .menu-toggler').css('display', 'none');
    };
    
    /**
     * Handles AJAX errors response status
     * 
     * @param {object} xhr AJAX response object
     * @param {string} err AJAX response error text
     * @returns {undefined}
     */
    var showAjaxError = function(xhr, err, settings) {
        console.log("AJAX err: " + err + " URL: " + settings.url);
        
        if (xhr.status == 401) {
            // session ended
            if (reLogin.auth_popup.is(":visible")) {
                // relogin already opened
                return false;
            }
            
            // relogin required
            reLogin.ajax_obj = settings;
            reLogin.openForm();
            return;
        }
        
        toastr.error(getAjaxErrorText(xhr, err));
        
        hide_page_splash(1);
        hide_form_splash(1);
    };
    
    /**
     * Gets error message from AJAX error response
     * 
     * @param {object} xhr AJAX response object
     * @param {string} err AJAX response error text
     * @returns {string} Error message
     */
    var getAjaxErrorText = function(xhr, err) {
        var err_txt = "";
        var json = xhr.responseJSON;
        
        // Validation errors handling
        if ( xhr.status === 422  && typeof json != "undefined") 
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
            if (err) {
                err_txt = err;
            }
            else {
                // unknown error
                console.log('Unknown AJAX error. XHR info: status = ' + xhr.status  + '; txt = ' + xhr.responseText);
                err_txt = DX_CORE.trans_general_error;
            }
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
        
        //initUserTasksPopup(); // Temporary remove tasks notifications - should be implemented posibility to setup which pages must use this notify
        
        initPortletsShowHide();
        handlePortletsHideShow();
        
        handleTargetedLinkClick();
        
        setFilesLinksIcons();
        
        handleMenuSplash();
        
        if (!$("body").hasClass("dx-horizontal-menu-ui")) { 
            handleBtnScreen();
            initPageSize();
            setActiveMenu();
        }                
            
        if (typeof dx_is_slider !== "undefined" && dx_is_slider === 1) {
            reset_margin();        
            addResizeCallback(reset_margin);
        }

        setInterval(function(){ 
            timerResizePage();
        }, 500);
    };

    /**
     * Pievieno masīvam funkciju, kas būs jāizsauc lapas izmēru mainīšanas gadījumā
     * 
     * @param {function} callback   Izsaucamā funkciaj
     * @returns {undefined}
     */
    var addResizeCallback = function(callback, id) {
        if (id) {
            for(i=0; i<callbacks_ids.length; i++) {
                if (callbacks_ids[i] == id) {
                    // allready added
                    return;
                }
            }
            callbacks_ids.push(id);
        }
        resize_functions_arr.push(callback);
    };
    
    /**
     * Show splash screen on menu link click
     */
    var handleMenuSplash = function() {
        
        var showSplash = function() {
            var stick = $(".dx-stick-footer");
            if (stick.is(":visible") && stick.hasClass("dx-page-in-edit-mode")) {                
                PageMain.showConfirm(null, null, Lang.get('errors.attention'), Lang.get('errors.form_in_editing'), Lang.get('errors.btn_ok'), null, null, true);
                return false;
            }            
            return true;
        };
        
        window.onpopstate = function(e){
            if(e.state && e.state.list_id && e.state.view_id){
                last_view_loaded_id = e.state.view_id;
                load_grid("td_data", e.state.list_id, e.state.view_id);
                return;
            }
            
            if (e.state && e.state.page_url) {
                if (!showSplash()) {
                    e.preventDefault();
                    return false;
                }
                
                window.location.href = e.state.page_url;
            }
            
            if (last_view_loaded_id) {
                show_page_splash(1);
                window.location.reload();
            }
        };

        var menu_click = function(link, e) {
            if (!showSplash()) {
                e.preventDefault();
                return false;
            }
            
            var list_id = link.attr("data-list-id");
            var view_id = link.attr("data-view-id");
            
            if (list_id && view_id && typeof load_grid !== "undefined") {
                e.preventDefault();
                link.closest("li[data-level=0]").removeClass("open").find("a[aria-expanded=true]").attr("aria-expanded", "false");
                window.history.pushState({"list_id": list_id, "view_id": view_id}, "", "/skats_" + view_id);
                
                if(is_grid_resize_callback_added == 0) {                    
                    PageMain.addResizeCallback(BlockViews.initHeight, 'initHeight');
                    is_grid_resize_callback_added = 1;
                }
                last_view_loaded_id = view_id;
                load_grid("td_data", list_id, view_id);
                
                return false;
            }
            
            $('.splash').css('display', 'block');
            $('body').css('overflow', 'hidden');
           
        };
        
        if ($("body").hasClass("dx-horizontal-menu-ui")) {
            // horizontal menu ui
            $(".dx-main-menu a").not(".dx-main-menu a.dropdown-toggle").click(function(e) {                
                return menu_click($(this), e);
            });
        }
        else {
            // vertical menu ui
            $("ul.page-sidebar-menu a").not("ul.page-sidebar-menu a.nav-toggle").click(function(e) {
                return menu_click($(this), e);
            });
        }
    };
    
    /**
     * Opens modal confirmation dialog. If modal's title, body or buttons text is not specified then function useses default texts.
     * @param {function} callback Callback function executed after accept
     * @param {object} callbackParameters Callback functions parameters
     * @param {string} title Modal title
     * @param {string} bodyText Modal body text
     * @param {string} acceptText Modal accept button text
     * @param {string} declineText Modal decline button text
     * @param {function} declineCallback Callback function executed after declined
     * @returns {undefined}
     */
    var showConfirm = function(callback, callbackParameters, title, bodyText, acceptText, declineText, declineCallback, is_accept_only){
        if(!title){
            title = Lang.get('form.modal_confirm_title');
        }
        
        if(!bodyText){
            bodyText = Lang.get('form.modal_confirm_body');
        }
        
        if(!acceptText){
            acceptText = Lang.get('form.btn_accept');
        }
        
        if(!declineText){
            declineText = Lang.get('form.btn_cancel');
        }
        
        
        var modal = $('#mindwo-modal');
        
        modal.find('#mindwo-modal-label').html(title);
        modal.find('#mindwo-modal-body').html(bodyText);
        
        var decline_btn = modal.find('#mindwo-modal-decline');
        decline_btn.html(declineText);
        decline_btn.off('click');   
        
        if(declineCallback != undefined){
            decline_btn.click(declineCallback);
        }
        
        if (is_accept_only) {
            decline_btn.hide();
        }
        
        var accept_btn  = modal.find('#mindwo-modal-accept');
        accept_btn.html(acceptText);
       
        accept_btn.off('click');
        
        if (callback != undefined && callback != null) {
            accept_btn.click(function(){
                var res =  callback(callbackParameters);

                if(res || typeof(res) == 'undefined'){
                    modal.modal('hide');
                }
            });
        }
        else {
            accept_btn.click(function(){
                modal.modal('hide');
            });
        }
        
        // hide opened menu item if any
        var navMainOpen = $("#navbar li.open");
        navMainOpen.find("a[aria-expanded=true]").attr("aria-expanded", false);
        navMainOpen.removeClass("open");
     
        // shoe modal popup
        modal.modal('show');        
    };

    return {
        init: function() {
            initPage();
        },
        initPageLoaded: function() {
            initPageLoaded();
        },
        addResizeCallback: function(callback, id) {
            addResizeCallback(callback, id);
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
            // do nothing
        },
        executeResizeCallbacks: function() {
            executeResizeCallbacks();
        },
        errorHandler: function(xhr, err, settings) {
            showAjaxError(xhr, err, settings);
        },
        getAjaxErrTxt: function(xhr) {
            return getAjaxErrorText(xhr);
        },
        showConfirm:function(callback, callbackParameters, title, bodyText, acceptText, declineText, declineCallback, is_accept_only){
            showConfirm(callback, callbackParameters, title, bodyText, acceptText, declineText, declineCallback, is_accept_only);
        }
    };
}();

Function.prototype.getName = function(){
  // Find zero or more non-paren chars after the function start
  return /function ([^(]*)/.exec( this+"" )[1];
};

PageMain.init();

$(document).ready(function() {
    PageMain.initPageLoaded();
    PageMain.initHelpPopups();
    $(this).scrollTop(0,0);    
});

$(document).ajaxError(function(event, xhr, settings, err) {
    PageMain.errorHandler(xhr, err, settings);
});

$(document).ajaxComplete(function(event, xhr, settings) {
    PageMain.modalsDraggable();
    PageMain.initHelpPopups();
    PageMain.initFilesIcons();
});

// To solve FireFox history back issue: https://stackoverflow.com/questions/158319/is-there-a-cross-browser-onload-event-when-clicking-the-back-button
window.onunload = function(){};