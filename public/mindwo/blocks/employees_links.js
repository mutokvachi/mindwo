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
     * Nosūta formas datus uz server - izpilda SUBMIT
     * Ja nepieciešams, tad nomaina formas URL (action atribūts)
     * 
     * @returns {undefined}
     */
    var submitForm = function(block_elem) {
        var frm = block_elem.find('form');
        
        if (post_url.length > 0) {
            frm.attr('action', DX_CORE.site_url + post_url);
        }
        
        frm.submit();
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
            show_page_splash();
            clearFields();

            block_elem.find('input[name=department]').val($(this).attr('dx_attr'));
            block_elem.find('select[name=source_id]').val($(this).attr('dx_source_id'));

            submitForm(block_elem);
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
            show_page_splash();
            clearFields();

            block_elem.find('input[name=position]').val($(this).attr('dx_attr'));
            block_elem.find('[name=source_id]').val($(this).attr('dx_source_id'));
            
            submitForm(block_elem);
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
            show_page_splash();
            clearFields();

            block_elem.find('input[name=cabinet]').val($(this).attr('dx_attr'));
            block_elem.find('input[name=office_address]').val($(this).attr('dx_office_address'));

            submitForm(block_elem);
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
            show_page_splash();
            clearFields();

            block_elem.find('input[name=manager_id]').val($(this).attr('dx_manager_id'));

            submitForm(block_elem);
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
            show_page_splash();
            clearFields();

            block_elem.find('input[name=subst_empl_id]').val($(this).attr('dx_subst_empl_id'));
            submitForm(block_elem);
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