/**
 * Galeriju JavaScript funkcionalitāte
 * Vienā lapā var būt viens galerijas bloks
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockGaleries = function()
{    
    /**
     * Norāda, cik ieraksti jāizlaiž pieprasot ielādēt nākamo galereiju porciju, spiežot pogu "Ielādēt vairāk"
     * 
     * @type Number
     */
    var skip = 0;
    
    /**
     * Norāda, pēc kāda datu avota ID tika veikta meklēšana
     * Nepieciešams, lai padotu AJAX pieprasījumam nākamo galeriju pielādēšanai
     * 
     * @type Number
     */
    var filt_source_id = 0;
    
    /**
     * Norāda, pēc kāda gada tika veikta meklēšana
     * Nepieciešams, lai padotu AJAX pieprasījumam nākamo galeriju pielādēšanai
     * 
     * @type Number
     */
    var filt_year = 0;
    
    /**
     * Uzstāda galerijas datu avota ID
     * 
     * @type Number
     */
    var source_id = 0;
    
    /**
     * Uzstāda lapas ID, kurā tiks ielādēta galerijas detalizācija
     * 
     * @type Number
     */
    var article_page_id = 0;
    
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
        var formData = "param=" + encodeURIComponent("OBJ=GALERIES|SOURCE=" + source_id + "|ARTICLEPAGE=" + article_page_id) + "&skip=" + skip + "&source_id=" + filt_source_id + "&year=" + filt_year;
                
        /*
        formData.append("param", "OBJ=GALERIES|SOURCE=" + source_id + "|ARTICLEPAGE=" + article_page_id);
        formData.append("skip", skip);
        formData.append("source_id", filt_source_id);
        formData.append("year", filt_year);
        */
       
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
     * Vērtību notīrīšanas saites nospiešana - notīra izkrītošo izvēlņu vērtības
     * 
     * @returns {undefined}
     */
    var handleLinkClear = function() {
        $("#clear_link").click(function(){
           clearFields();
        });
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
     * Ielādēt vairāk pogas nospiešana - izpilda AJAX pieprasījumu un ielāde papildus galerijas
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
     * Uzstāda galeriju bloka parametrus
     * 
     * @param {object} block Galerijas bloka elements
     * @returns {undefined}
     */
    var setParameters = function(block)
    {
        article_page_id = block.attr('dx_article_page_id');
        source_id = block.attr('dx_source_id');
        
        skip = block.attr('dx_skip');
        
        filt_source_id = block.attr('dx_filt_source_id');
        filt_year = block.attr('dx_filt_year');
        
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializēto galeriju bloku
     * 
     * @returns {undefined}
     */
    var initGaleries = function()
    {        
        var page_elem = $(".dx-block-container-galery[dx_block_init='0']");
        
        if (page_elem.length == 0) {
            return; // nav vairs ko inicializēt
        }
            
        setParameters(page_elem);

        handleLinkClear();
        handleBtnSearch();
        handleLoadMoreLink();

        drawCubeGalery();                       

        page_elem.attr('dx_block_init', 1); // uzstādam pazīmi, ka bloks ir inicializēts
        
    };

    return {
        init: function() {
            initGaleries();
        }
    };
}();

$(document).ready(function() {
    BlockGaleries.init();
});