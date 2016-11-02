/**
 * JavaScript logic for horizontal menu forms UI
 * 
 * @type _L4.Anonym$0|Function
 */
var HFormUI = function()
{ 
    var handleFormClose = function(grid_id) {
        $(".dx-form-fullscreen-frame .dx-form-close-btn").click(function() {
            $("#td_form_data").html("");
            $("#td_data").show();
            stop_executing(grid_id);
        });
    };
    
    /**
     * Inits horizontal menu page UI
     * 
     * @returns {undefined}
     */
    var initUI = function(grid_id) {       
       handleFormClose(grid_id);
    };

    return {
        init: function(grid_id) {
            initUI(grid_id);
        }
    };
}();