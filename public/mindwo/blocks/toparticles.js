/**
 * TOP ziņu slīdrādes JavaScript funkcionalitāte
 * Vienā lapā pieļaujams tikai 1 slīdrādes objekts
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockTopArticles = function()
{    
    /**
     * Slīdrādes objekts
     * 
     * @type type
     */
    var jssor_1_slider = null;
    
    /**
     * Sldrādes slaidu maiņas intervāls milisekundēs
     * 
     * @type Number
     */
    var transition_time = 5000;
    
    /**
     * Uzstāda slīdrādes izmērus atbilstoši lapas izmēram
     * 
     * @returns {undefined}
     */
    var scaleSlider = function() {
        var refSize = jssor_1_slider.$Elmt.parentNode.clientWidth;
        
        var slider = $('#top-article-slider-container');
        
        if (!slider.length) {
            return; // no slider in page
        }
        
        var cont_width = slider.parent().css('width').replace(/[^-\d\.]/g, '')-1;
        cont_width = cont_width + 1-29;
        
        if (refSize) {
            refSize = Math.min(refSize, cont_width);
            jssor_1_slider.$ScaleWidth(refSize);
        }
        else {
            window.setTimeout(ScaleSlider, 30);
        }
    };
    
    /**
     * Atgriež slaidu animācijas iestatījumu masīvu
     * 
     * @returns {Array}
     */
    var getSliderTransitions = function(){
        return [              
          [{b:0,d:600,y:-290,e:{y:27}}],
          [{b:0,d:1000,y:185},{b:1000,d:500,o:-1},{b:1500,d:500,o:1},{b:2000,d:1500,r:360},{b:3500,d:1000,rX:30},{b:4500,d:500,rX:-30},{b:5000,d:1000,rY:30},{b:6000,d:500,rY:-30},{b:6500,d:500,sX:1},{b:7000,d:500,sX:-1},{b:7500,d:500,sY:1},{b:8000,d:500,sY:-1},{b:8500,d:500,kX:30},{b:9000,d:500,kX:-30},{b:9500,d:500,kY:30},{b:10000,d:500,kY:-30},{b:10500,d:500,c:{x:87.50,t:-87.50}},{b:11000,d:500,c:{x:-87.50,t:87.50}}],
          [{b:0,d:600,x:410,e:{x:27}}],
          [{b:-1,d:1,o:-1},{b:0,d:600,o:1,e:{o:5}}],
          [{b:-1,d:1,c:{x:175.0,t:-175.0}},{b:0,d:800,c:{x:-175.0,t:175.0},e:{c:{x:7,t:7}}}],
          [{b:-1,d:1,o:-1},{b:0,d:600,x:-570,o:1,e:{x:6}}],
          [{b:-1,d:1,o:-1,r:-180},{b:0,d:800,o:1,r:180,e:{r:7}}],
          [{b:0,d:1000,y:80,e:{y:24}},{b:1000,d:1100,x:570,y:170,o:-1,r:30,sX:9,sY:9,e:{x:2,y:6,r:1,sX:5,sY:5}}],
          [{b:2000,d:600,rY:30}],
          [{b:0,d:500,x:-105},{b:500,d:500,x:230},{b:1000,d:500,y:-120},{b:1500,d:500,x:-70,y:120},{b:2600,d:500,y:-80},{b:3100,d:900,y:160,e:{y:24}}],
          [{b:0,d:1000,o:-0.4,rX:2,rY:1},{b:1000,d:1000,rY:1},{b:2000,d:1000,rX:-1},{b:3000,d:1000,rY:-1},{b:4000,d:1000,o:0.4,rX:-1,rY:-1}]             
        ];
    };
    
    /**
     * Uzzmīmē slaideri
     * 
     * @returns {undefined}
     */
    var drawSlider = function() {
        
        var jssor_1_SlideoTransitions = getSliderTransitions();
        
        var jssor_1_options = {
          $AutoPlay: true,
          $Idle: transition_time, //transition_time
          $CaptionSliderOptions: {
            $Class: $JssorCaptionSlideo$,
            $Transitions: jssor_1_SlideoTransitions,
            $Breaks: [
              [{d:2000,b:1000}]
            ]
          },
          $ArrowNavigatorOptions: {
            $Class: $JssorArrowNavigator$
          },
          $BulletNavigatorOptions: {
            $Class: $JssorBulletNavigator$
          }
        };

        jssor_1_slider = new $JssorSlider$("top-article-slider-container", jssor_1_options);

        // responsivitātes nodrošināšana

        scaleSlider();
        $Jssor$.$AddEvent(window, "load", scaleSlider);
        $Jssor$.$AddEvent(window, "resize", scaleSlider);
        $Jssor$.$AddEvent(window, "orientationchange", scaleSlider);
    };

    
    /**
     * Inicializē slīdrādes bloku
     * 
     * @returns {undefined}
     */
    var initSlider = function()
    {        
        if ($("#top-article-slider-container").length == 0){
            return; // lapā nav slīdrādes
        }
        
        if ($("#top-article-slider-container").find("img").length == 0) {
            $("#top-article-slider-container").hide();
            return;// nevienai ziņai nav galvenais attēls
        }
           
        transition_time = parseInt($("#top-article-slider-container").attr('dx_transition_time'));
        drawSlider();
        
        // Pievienojam slīdrādes pārzīmēšanas izsaukumu uz lapas/loga izmēra izmaiņām
        PageMain.addResizeCallback(scaleSlider);
    };

    return {
        init: function() {
            initSlider();
        }
    };
}();

$(document).ready(function() {
    BlockTopArticles.init();
});