/**
 * Atrasto darbinieku saišu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var EmployeesLinks = function()
{ 
    /**
     * Funkcija, kas notīra meklēšanas lauku vērtības
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearFields = null;
    
    /**
     * POST formas URL
     * 
     * @type String
     */
    var post_url = "";

    /**
     * Nosūta formas datus uz serveri - izpilda lapas pārlādi ar GET parametriem
     * Ja nepieciešams, tad nomaina formas URL (action atribūts)
     * 
     * @param {string} url_params GET pieprasījuma parametri
     * @returns {undefined}
     */
    var submitForm = function(url_params) {
        show_page_splash();
        url_params = url_params + "&is_from_link=1";
        window.location.href = DX_CORE.site_url + "search?" + encodeURI(url_params);
    };
    
    /**
     * Nodrošina struktūrvienības saites funkcionalitāti - atlasa darbiniekus, kuriem tāda pati struktūrvienība
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @returns {undefined}
     */
    var handleLinkDepartment = function(block_elem) {

        block_elem.find('a.dx_department_link').click(function(e) {
            e.preventDefault();
            var url_params = "department_id=" + $(this).attr('dx_dep_id') + "&source_id=" + $(this).attr('dx_source_id');
            submitForm(url_params);
        });
    };

    /**
     * Nodrošina amata saites funkcionalitāti - atlasa darbiniekuks, kuriem tāds pats amats
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @returns {undefined}
     */
    var handleLinkPosition = function(block_elem) {
        block_elem.find('a.dx_position_link').click(function(e) {
            e.preventDefault();
            
            var url_params = "position=" + $(this).attr('dx_attr') + "&source_id=" + $(this).attr('dx_source_id');
            submitForm(url_params);
        });
    };

    /**
     * Nodrošina kabineta saites funkcionalitāti - atlasa darbiniekus, kuriem tāds pats kabinets un adrese
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @returns {undefined}
     */
    var handleLinkCabinet = function(block_elem) {
        block_elem.find('a.dx_cabinet_link').click(function(e) {
            e.preventDefault();
            
            var url_params = "cabinet=" + $(this).attr('dx_attr') + "&office_address=" + $(this).attr('dx_office_address');
            submitForm(url_params);
        });
    };

    /**
     * Nodrošina tiešā vadītāja saites funkcionalitāti - atlasa darbiniekus, kuriem tāds pats tiešais vadītājs kā arī pašu tiešo vadītāju
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @returns {undefined}
     */
    var handleLinkManager = function(block_elem) {
        block_elem.find('a.dx_manager_link').click(function(e) {
            e.preventDefault();

            var url_params = "manager_id=" + $(this).attr('dx_manager_id');
            submitForm(url_params);
        });
    };

    /**
     * Nodrošina aizvietotāja saites funkcionalitāti - atlasa darbinieka aizvietotāju
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @returns {undefined}
     */
    var handleLinkSubstit = function(block_elem) {
        block_elem.find('a.dx_substit_link').click(function(e) {
            e.preventDefault();
            
            var url_params = "subst_empl_id=" + $(this).attr('dx_subst_empl_id');
            submitForm(url_params);
        });
    };
    
    var handleOpenProfile = function(block_elem) {
        block_elem.find('a.dx-open-profile').click(function(e) {
            e.preventDefault();
            
            if ($(this).attr("dx_profile_url") == "/") {
                // open public profile
                window.location.href = DX_CORE.site_url + $(this).attr("dx_profile_url") + "?empl_id" + $(this).attr("dx_empl_id");
            }
            else {
                // open HR profile
                view_list_item("form", $(this).attr('dx_empl_id'), $(this).attr('dx_empl_list_id'), 0, 0, "", ""); 
            }
        });
    };
    
    /**
     * Uzstāda POST formas URL
     * 
     * @param {string} url Formas URL
     * @returns {undefined}
     */
    var initFormUrl = function(url) {
        post_url = url;
    };
    
    /**
     * Uzstāda meklēšanas rezultātos attēloto saišu funkcionalitāti
     * Nospiežot uz saitēm, tiek veikta darbinieku meklēšana - piemēram, visi tiešā vadītāja pakļautie darbinieki, vai visi darbinieki, kas ir no viena kabineta utt
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @returns {undefined}
     */
    var initLinksHandles = function(block_elem) {        
        handleLinkDepartment(block_elem);
        handleLinkPosition(block_elem);
        handleLinkCabinet(block_elem);
        handleLinkManager(block_elem);
        handleLinkSubstit(block_elem);
        handleOpenProfile(block_elem);
    };
    
    /**
     * Inicializē darbinieku lapas JavaScript funkcionalitāti
     * 
     * @param {Object} block_elem jQuery objekts HTML elementam, kurā tiek ielādēta darbinieku meklēšanas funkcionalitāte
     * @param {Function} callback_clearFields Uzstāda funkciju, kura notīra meklēšanas laukus
     * @returns {undefined}
     */
    var initLinks = function(block_elem, callback_clearFields) {        
        
        clearFields = callback_clearFields;
        
        initLinksHandles(block_elem);
    };

   

    return {
        init: function(block_elem, callback_clearFields) {
            initLinks(block_elem, callback_clearFields);
        },
        setFormUrl: function(url) {
            initFormUrl(url);
        }
    };
}();