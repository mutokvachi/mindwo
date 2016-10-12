/**
 * Darbinieka profila bloka JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockEmplProfile = function()
{ 
    /**
     * Vai ir inicializēts izmēru koriģētājs
     * 
     * @type Boolean
     */
    var is_resize_init = false;
    
    /**
     * Inicializē rediģēšanas pogu darbinieka profilam
     * @param {object} block Profila bloka HTML objekts
     * 
     * @returns {undefined}
     */
    var handleBtnEditProfile = function(block) {
        block.find(".dx-edit-general").click(function(){
            open_form('form', $(this).attr('dx_employee_id'), $(this).attr('dx_list_id'), 0, 0, '', 1, '');
        });
    };
    
    /**
     * Inicializē darbinieku prombūtņu pievienošanas pogu
     * @param {object} block Profila bloka HTML objekts
     * @returns {undefined}
     */
    var handleBtnLeaveAdd = function(block) {
        block.find(".dx-employee-leave-add-btn").click(function() {
            new_list_item(285, 0, 0, '', '');
        });
    };
    
    /**
     * Inicializē darbinieku bonusu pievienošanas pogu
     * @param {object} block Profila bloka HTML objekts
     * @returns {undefined}
     */
    var handleBtnBonusAdd = function(block) {
        block.find(".dx-employee-bonus-add-btn").click(function() {
            new_list_item(287, 0, 0, '', '');
        });
    };
    
    /**
     * Pārinicializē tabu lai korekti saliek izvēlnes mainot lapas izmēru
     * @returns {undefined}
     */
    var resizeBlock = function() {
        if ($().tabdrop) {
            $('.tabbable-tabdrop .nav-pills, .tabbable-tabdrop .nav-tabs').tabdrop({
                text: '<i class="fa fa-ellipsis-v"></i>&nbsp;<i class="fa fa-angle-down"></i>'
            });
        }
    }
    
    /**
     * Inicializē datuma intervāla uzstādīšanas komponenti
     * 
     * @returns {undefined}
     */
    var initDateRange = function() {
        var arr_date_param = {
            range_id: "defaultrange",
            el_date_from: "date_from",
            el_date_to: "date_to",
            page_selector: ".dx-empl-changes-search",
            form_selector: ".search-tools-form",
            arr_ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                    'Previous 3 days': [moment().subtract('days', 6), moment()],
                    'Next 3 days': [moment(), moment().subtract('days', -6)],
                    'This month': [moment().startOf('month'), moment().endOf('month')],
                    'Previous month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
                }
        };
        
        DateRange.init(arr_date_param, null);
    }
    
    /**
     * Inicializē darbinieku izmaiņu bloka JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initBlock = function() {        
        
        $(".dx-employee-profile[dx_is_init='0']").each(function() {
            
            handleBtnEditProfile($(this));
            handleBtnLeaveAdd($(this));
            handleBtnBonusAdd($(this));
            
            initDateRange();
            
            $(this).attr('dx_is_init', 1); // uzstādam pazīmi, ka bloks inicializēts
        }); 
        
        if (!is_resize_init) {
            //PageMain.addResizeCallback(resizeBlock);
            is_resize_init = true;
        }
    };    

    return {
        init: function() {
            initBlock();
        }
    };
}();

$(document).ready(function() {
    BlockEmplProfile.init();
});