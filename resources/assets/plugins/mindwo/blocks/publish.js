/**
 * Izdevumu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockPublish = function()
{    
    /**
     * Norāda, cik ieraksti jāizlaiž pieprasot ielādēt nākamo izdevumu porciju, spiežot pogu "Ielādēt vairāk"
     * 
     * @type Number
     */
    var skip = 0;   
    
    /**
     * Norāda, pēc kāda gada tika veikta meklēšana
     * Nepieciešams, lai padotu AJAX pieprasījumam nākamo izdevumu pielādēšanai
     * 
     * @type Number
     */
    var filt_year = 0;
    
    /**
     * Norāda, pēc kāda mēneša tika veikta meklēšana
     * Nepieciešams, lai padotu AJAX pieprasījumam nākamo izdevumu pielādēšanai
     * 
     * @type Number
     */
    var filt_month = 0;
    
    /**
     * Pazīme, vai tika jau pievienota pārzīmēšanas apstrāde
     * 
     * @returns {undefined}
     */
    var is_resize_handler_added = 0;
    
    /**
     * Atgriež masīvu ar cubeportfolio komponentes objekta iestatījumiem
     * 
     * @returns {_L6.getCubeOptions.Anonym$0}
     */
    var getCubeOptions = function() {
      return {
                filters: '#js-filters-juicy-projects',
                layoutMode: 'grid',
                loadMoreAction: 'click',
                defaultFilter: '*',
                animationType: 'quicksand',
                gapHorizontal: 35,
                gapVertical: 30,
                gridAdjustment: 'responsive',
                mediaQueries: [{
                    width: 1500,
                    cols: 5
                }, {
                    width: 1100,
                    cols: 4
                }, {
                    width: 800,
                    cols: 3
                }, {
                    width: 480,
                    cols: 2
                }, {
                    width: 320,
                    cols: 1
                }],
                caption: 'overlayBottomReveal',
                displayType: 'sequentially',
                displayTypeSpeed: 80
            };  
    };
    
    /**
     * Uzzīmē galeriju objektu ar cubeportfolio komponenti
     * 
     * @returns {undefined}
     */
    var drawCubeGalery = function() {
        if ($("#js-grid-juicy-projects").hasClass("dx_init_ok"))
        {                
            $("#js-grid-juicy-projects").removeClass("dx_init_ok");
            jQuery("#js-grid-juicy-projects").off('initComplete.cbp');
            $("#js-grid-juicy-projects").cubeportfolio('destroy');
        }                    

        jQuery("#js-grid-juicy-projects").on('initComplete.cbp', function() {
            $("#js-grid-juicy-projects").addClass("dx_init_ok");
            
            if (is_resize_handler_added ==0) {
                is_resize_handler_added = 1;
                PageMain.addResizeCallback(drawCubeGalery);
            }
        });
        
        $("#js-grid-juicy-projects").cubeportfolio('init', getCubeOptions());  
    };
    
    /**
     * Izpilda AJAX pieprasījumu un pieladē papildus galerijas
     * 
     * @returns {undefined}
     */
    var addGaleryItems = function()
    {            
        /*
        var formData = new FormData();
        formData.append("param", "OBJ=PUBLISH");
        formData.append("skip", skip);
        formData.append("nr", filt_month);
        formData.append("year", filt_year);
        */
        var formData = "param=" + encodeURIComponent("OBJ=PUBLISH") + "&skip=" + skip + "&nr=" + filt_month + "&year=" + filt_year;
        var ajax_url = 'block_ajax';

        $("#load_more_link").text("IELĀDĒ...");

        var request = new FormAjaxRequestIE9 (ajax_url, "", "", formData);            
        request.progress_info = "Ielādē datus. Lūdzu, uzgaidiet...";                       

        request.callback = function(data) {

            skip = data.data.skip;
            var scroll_down = function(){
                var d = $("body");

                d.scrollTop(d.prop("scrollHeight"));                    
            };

            $("#js-grid-juicy-projects").cubeportfolio('appendItems', data['html'], scroll_down);
            
            if (data.data.is_last)
            {
                $("#load_more_link").hide();                    
            }
            else
            {
                $("#load_more_link").text("VAIRĀK");
            }

        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    /**
     * Meklēšanas pogas nospiešana - uzstāda progresa paziņojumu lapai
     * Progresa paziņojuma funkcija no dx_core.js
     * 
     * @returns {undefined}
     */
    var handleBtnSearch = function()
    {
        $("#search_galeries_btn").click(function() {
            show_page_splash();
        });
    };
    
    /**
     * Ielādēt vairāk pogas nospiešana - izpilda AJAX pieprasījumu un ielādē papildus izdevumus
     * 
     * @returns {undefined}
     */
    var handleLoadMoreLink = function()
    {
        $("#load_more_link").click(function(e){
           e.preventDefault();
           addGaleryItems();
        });
    };
    
    /**
     * Uzstāda izdevumu bloka parametrus
     * 
     * @param {object} block Izdevumu bloka elements
     * @returns {undefined}
     */
    var setParameters = function(block)
    {        
        skip = block.attr('dx_skip');
        
        filt_month = block.attr('dx_filt_month');
        filt_year = block.attr('dx_filt_year');
        
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializēto izdevumu bloku
     * 
     * @returns {undefined}
     */
    var initPublish = function()
    {        
        var page_elem = $(".dx-block-container-publish[dx_block_init='0']");
        
        if (page_elem.length == 0) {
            return; // nav vairs ko inicializēt
        }        
            
        setParameters(page_elem);

        handleBtnSearch();
        handleLoadMoreLink();
        drawCubeGalery();

        page_elem.attr('dx_block_init', 1); // uzstādam pazīmi, ka bloks ir inicializēts
       
    };

    return {
        init: function() {
            initPublish();
        }
    };
}();

$(document).ready(function() {
    BlockPublish.init();
});