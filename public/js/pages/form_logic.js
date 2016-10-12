/**
 * CMS formu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var FormLogic = function()
{    
    /**
     * JSON masīvs kā teksts ar sākotnējo saskaņotāju stāvokli
     * @type string
     */
    var state_approvers = "";
    
    /**
     * Reģistrē dokumentu - piešķir reģistrēšanas numuru
     * 
     * @param {Formas elements} section
     * @returns {undefined}
     */
    var registerDoc = function(section) {
        
        var item_id = section.parent().find('input[name=item_id]').val();
        var list_id = section.find('.dx-reg-nr-field').attr('dx_list_id');
        var reg_nr_field_id = section.find('.dx-reg-nr-field').attr('dx_reg_nr_field_id');
        
        var formData = "item_id=" + item_id + "&list_id=" + list_id + "&regn_nr_field_id=" + reg_nr_field_id;
        var grid_id = section.attr("dx_grid_id");
        
        var request = new FormAjaxRequestIE9 ("register_document", "", grid_id, formData);            
        request.progress_info = "Reģistrēšana... Lūdzu, uzgaidiet...";                       

        request.callback = function(data) {
            
            section.find('.dx-reg-nr-field input').val(data.reg_nr);
            
            if (data.reg_date_fld) {
                section.find('div[dx_fld_name=' + data.reg_date_fld + ']').html(data.reg_date_htm);
            }
            
            section.find('.dx-reg-nr-field .input-group-btn').hide();
            
            if (grid_id)
            {
                reload_grid(grid_id);
            }
        
            notify_info("Dokuments veiksmīgi reģistrēts!");
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    /*
    * Inicializē darbplūsmu 
    * 
    * @param   save_url      string    URL darbplūsmas iznicializēšanai
    * @param   form_id       string     Formas HTML objekta identifikators
    * @param   grid_htm_id   string Reģistra tabulārā skata HTML objekta identifikators - nepieciešams lai atjauninātu saraksta datus
    * @param   list_id       integer     Reģistra identifikators
    * @param   item_id       integer     Ieraksta identifikators
    * @param   approvers     string      Ja norādīts, tad JSON teksta veidā ar saskaņotāju masīva informāciju
    * @param   frm_init      object      HTML objekts saskaņošanas formai
    * @param   is_paralel    boolean     False - secīgā saskaņošana, True - paralēlā saskaņošana
    * @return  void
    */ 
   function init_workflow(save_url, form_htm_id, grid_htm_id, list_id, item_id, approvers, frm_init, is_paralel)
   {
       var formData = new FormData();
       formData.append("list_id", list_id);
       formData.append("item_id", item_id);
       formData.append("approvers", approvers);
       formData.append("is_paralel", is_paralel);
              
       var request = new FormAjaxRequest (save_url, form_htm_id, grid_htm_id, formData);

       request.callback = function(data) {

           if (grid_htm_id)
           {
               reload_grid(grid_htm_id);
           }

           // Atjauninam statusa lauka vērtību
           $( "#" + form_htm_id  + " input[dx_fld_name='dx_item_status_id']").val(data['doc_status']);

           // Noslēpjam augšejo pogu joslu
           $("#top_toolbar_" + form_htm_id).hide();

           notify_info("Darbplūsmas process veiksmīgi uzsākts!");

           // Pārlādējam darbplūsmas uzdevumu sadaļu, lai parādās jaunais uzdevums
           $( "#" + form_htm_id + " button[dx_attr='refresh']").click();
           
           // aizveram formu
           if (frm_init) {
            frm_init.modal('hide');
           }
       };

       // izpildam AJAX pieprasījumu
       request.doRequest();
   };
   
   /**
    * Atver darbplūsmas uzsākšanas formu ar noklusētajiem saskaņotājiem
    * 
    * @param {object} section Dokumentu formas HTML objekts
    * @param {integer} item_id Dokumenta ieraksta ID
    * @returns {undefined}
    */
   var openWFInitForm = function(section, item_id) {
        
        var frm_init = $("#form_init_wf_" + section.attr('dx_form_id'));
        
        if (frm_init.attr('dx_is_init') == "0") {
            initFormInit(frm_init, section);
        }
        
        var formData = new FormData();
        formData.append("list_id", section.attr("dx_list_id"));
        formData.append("item_id", item_id);

        var request = new FormAjaxRequest ("workflow_custom_approve", "", "", formData);

        request.callback = function(data) {
            
            var block = frm_init.find(".modal-body .dx-cms-init-approvers");
            block.html(data.html);
            
            state_approvers = getApproversState(block);
            
            frm_init.find(".dx-cms-nested-list").nestable(); // ToDo: Jāatceras uzlikt Metronic labojumu citādi var pavilkt pa labi nevajadzīgi
            handleApproverRemoval(block);
            
            frm_init.modal('show');
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
   };
   
   /**
    * Izgūst tekstu (JSON teksts, pārveidots masīvs) ar apstiprinātāju un termiņu stāvokli
    * 
    * @param {object} block Apstiprinātāju HTML bloks
    * @returns {_L6.getApproversState.ret_arr}
    */
   var getApproversState = function(block) {
       var ret_arr = new Array();
       
       block.find(".dd-item").each(function() {
           var item = {"empl_id": $(this).attr('data-id'), "due_days": $(this).find('input[name=due_days]').val()};
           ret_arr.push(item);
       });
       
       return JSON.stringify(ret_arr);
   };
   
   /**
    * Nodrošina iespēju noņemt saskaņotāju
    * 
    * @param {object} block Saskaņotāju HTML bloka elements
    * @returns {undefined}
    */
   var handleApproverRemoval = function(block) {
       block.find(".dx-cms-approver-remove[dx_is_init=0]").click(function() {
           $(this).parent().parent().parent().parent().remove();
       });
       
       block.find(".dx-cms-approver-remove[dx_is_init=0]").attr("dx_is_init", 1);
   }
   
    /**
     * Inicializē darbplūsmas uzsākšanas formu
     * 
     * @param {object} frm_init Saskaņotāju iestatīšanas formas HTML objekts
     * @param {object} section Dokumenta formas HTML objekts
     * @returns {undefined}
     */
    var initFormInit = function(frm_init, section) {
        
        handleApproverFormOpen(frm_init);
        handleApproverInsert(frm_init);
        
        var frm_appr = $("#form_init_wf_approver_" + frm_init.attr("dx_frm_uniq_id"));
        handleApproverLookup(frm_appr);
        
        handleCustomWorkflowStart(frm_init, section);
        
        frm_init.attr('dx_is_init', 1); // uzstādam pazīmi, ka forma inicializēta
    };
    
    /**
     * Darbplūsmas uzsākšanas pogas nospiešana - gadījumā ar iestatītiem saskaņotājiem
     * 
     * @param {object} frm_init Saskaņotāju iestatīšanas formas HTML objekts
     * @param {object} section Dokumenta formas HTML objekts
     * @returns {undefined}
     */
    var handleCustomWorkflowStart = function(frm_init, section) {
        frm_init.find(".dx-cms-wf-btn-start").click(function() {            
            
            var item_id = $( "#item_edit_form_" + section.attr("dx_form_id")  + " input[name='item_id']").val();
            var block = frm_init.find(".modal-body .dx-cms-init-approvers");
            
            var approvers = getApproversState(block);
            
            if (approvers == "[]") {
                notify_err("Lūdzu, norādiet vismaz vienu saskaņotāju!");
                return;
            }
            
            var is_paralel = 0;
            
            if (state_approvers == approvers) {                
                approvers = ""; // nav izmaiņu
            }
            else {
                is_paralel = frm_init.find('input[name=order]:checked').val();
            }
            
            init_workflow('workflow_init', 'list_item_view_form_' + section.attr("dx_form_id"), section.attr("dx_grid_id"), section.attr("dx_list_id"), item_id, approvers, frm_init, is_paralel);
            
        });
    };
    
    /**
     * Atver papildus saskaņotāja pievienošanas formu
     * 
     * @param {object} frm_init Saskaņošanas formas HTML objekts
     * @returns {undefined}
     */
    var handleApproverFormOpen = function(frm_init) {
        var frm_appr = $("#form_init_wf_approver_" + frm_init.attr("dx_frm_uniq_id"));
        
        frm_appr.on('shown', function () {
            frm_appr.find("input[name=empl_txt]").select2('data', {id:0, text:"", position_title: ""});
            frm_appr.find("input[name=empl_txt]").select2("open");
            frm_appr.find(".dx-cms-empl-position-title").text("");
        });
                    
        frm_init.find(".dx-cms-wf-btn-add-approver").click(function() {            
            frm_appr.modal('show');            
        });
    };
    
    /**
     * Pievieno izvēlēto saskaņotāju
     * @param {object} frm_init Saskaņošanas formas HTML objekts
     * @returns {undefined}
     */
    var handleApproverInsert = function(frm_init) {
        var frm_appr = $("#form_init_wf_approver_" + frm_init.attr("dx_frm_uniq_id"));
        
        frm_appr.find(".dx-cms-wf-btn-add-approver").click(function() {
            
            var data = frm_appr.find("input[name=empl_txt]").select2('data');
            
            if (!data || data.id == 0) {
                notify_err(frm_appr.attr('dx_error_empl_not_set'));
                return;
            }
                        
            if (!validateApproverUniq(frm_init, data.id)) {
                return;
            }
            
            var template = frm_appr.find('.dx-cms-approver-item-template').html();
            
            template = template.replace("[empl_id]", data.id);
            template = template.replace("[display_name]", data.text);
            template = template.replace("[position_title]", data.position_title);
            template = template.replace("[due_days]", 1);
            
            var approvers_block = frm_init.find(".modal-body .dx-cms-nested-list");
            approvers_block.append(template);
            
            handleApproverRemoval(approvers_block);
            frm_init.find(".dx-cms-nested-list").nestable("init");
            
            frm_appr.find("input[name=empl_txt]").select2('data', {id:0, text:"", position_title: ""});
            frm_appr.find(".dx-cms-empl-position-title").text("");
            frm_appr.find("input[name=empl_txt]").select2("open");
            
            notify_info(frm_appr.attr('dx_added_success'));
        });
    };
    
    /**
     * Inicializēs darbinieku uzmeklēšanas lauku
     * 
     * @param {object} frm_appr Formas HTML objekts
     * @returns {undefined}
     */
    var handleApproverLookup = function(frm_appr) {
        
        frm_appr.find("input[name=empl_txt]").select2({
            placeholder: frm_appr.attr('dx_search_placeholder'),
            minimumInputLength: 3,
            ajax: {
                type: 'POST',
                url: DX_CORE.site_url  + 'workflow_find_approver',
                processData: false,
                contentType: false,
                dataType: 'json',
                quietMillis: 250,
                cache: true,
                data: function (term, page) {
                    return {
                        q: term
                    };
                },
                results: function (data, page) {                 
                    // parse the results into the format expected by Select2.
                    // since we are using custom formatting functions we do not need to alter the remote JSON data

                    if (data.success == 1)
                    {
                        return { results: data.data};
                    }
                    else
                    {
                        if (data.error)
                        {
                                notify_err(data.error);
                        }
                        else
                        {
                                notify_err(frm_appr.attr('dx_system_error'));
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    if( jqXHR.status === 422 ) 
                     {
                         var errors = jqXHR.responseJSON;
                         var errorsHtml= '<ul>';
                         $.each( errors, function( key, value ) {
                             errorsHtml += '<li>' + value[0] + '</li>'; 
                         });
                         errorsHtml += '</ul>';
                         toastr.error(errorsHtml);
                     }
                     else   
                     {
                         notify_err(frm_appr.attr('dx_system_error'));
                     }
                }
            }
        }).on('change', function(event) {

            var data = $(this).select2('data');
            var position_title = "";
            
            if (data) {
                position_title = data.position_title;
            }
            
            frm_appr.find(".dx-cms-empl-position-title").text(position_title);
        });  
    };
    
    /**
     * Pārbauda pievienojamā saskaņotāja unikalitāti (lai jau nav pievienots)
     * @param {object} frm_init Saskaņošanas formas HTML objekts
     * @param {integer} empl_id Darbinieka ID
     * @returns {Boolean}
     */
    var validateApproverUniq = function(frm_init, empl_id) {
        if (frm_init.find(".modal-body .dx-cms-init-approvers").find(".dd-item[data-id=" + empl_id + "]").length > 0) {
            var frm_appr = $("#form_init_wf_approver_" + frm_init.attr("dx_frm_uniq_id"));
            
            notify_err(frm_appr.attr('dx_error_already_added'));
            return false;
        }
        
        return true;
    };
    
    /**
     * Reģistrēšanas pogas funkcionalitāte
     * 
     * @param {object} section Formas HTML elementa objekts
     * @returns {undefined}
     */
    var handleRegBtnClick = function(section){
       
        section.find('.dx-reg-nr-field button').click(function(){
            if (confirm("Lūdzu, apstipriniet - vai reģistrēt dokumentu?")) {
                registerDoc(section);
            }
        });  
    };
    
    /**
     * Darbplūsmas inicializēšanas pogas funkcionalitāte
     * 
     * @param {object} section Formas HTML elementa objekts
     * @returns {undefined}
     */
    var handleWFInitBtnClick = function(section) {        
        $("#list_item_view_form_" + section.attr("dx_form_id")).find(".dx-init-wf-btn").click(function() {
            
            var item_id = $( "#item_edit_form_" + section.attr("dx_form_id")  + " input[name='item_id']").val();
            
            if (item_id === 0)
            {
                notify_err("Lai uzsāktu darbplūsmu, vispirms veiciet datu saglabāšanu!");
                return;
            }
       
            if (section.attr("dx_is_custom_approve") == "1") {
                openWFInitForm(section, item_id);
            }
            else {
                init_workflow('workflow_init', 'list_item_view_form_' + section.attr("dx_form_id"), section.attr("dx_grid_id"), section.attr("dx_list_id"), item_id, "", null, 0);
            }
        });
    };
    
    /**
     * Informatīvā uzdevuma formas atvēršanas pogas funkcionalitāte
     * 
     * @param {object} section Formas HTML elementa objekts
     * @returns {undefined}
     */
    var handleInfoTaskBtnClick = function(section) {
        $("#list_item_view_form_" + section.attr("dx_form_id")).find(".dx-for-info-btn").click(function() {
            var item_id = $( "#item_edit_form_" + section.attr("dx_form_id")  + " input[name='item_id']").val();
            
            if (item_id === 0)
            {
                notify_err("Lai dokumentu nodotu informācijai, vispirms tas ir jāsaglabā!");
                return;
            }
            
            var frm_info = $("#form_task_info_" + section.attr('dx_form_id'));
            
            if (frm_info.attr("dx_is_init") == "0") {
                
                handleApproverLookup(frm_info);
                handleInfoTaskSendBtnClick(frm_info, section, item_id);
                App.initSlimScroll('.scroller');
                
                frm_info.attr("dx_is_init", "1");
            }                      
            
            frm_info.on('shown', function () {
                var cnt = parseInt(frm_info.find(".dx-cms-info-task-count").text())
                
                if (cnt == 0) {
                    frm_info.find("input[name=empl_txt]").select2('data', {id:0, text:"", position_title: ""});
                    frm_info.find("input[name=empl_txt]").select2("open");
                    frm_info.find(".dx-cms-empl-position-title").text("");
                }
            });
            
            frm_info.modal('show');            
        });
    };
    
    /**
     * Izveido informācijas uzdevumu
     * 
     * @param {object} frm_info Informācijas formas HTML objekts
     * @param {object} section Dokumenta formas HTML objekts
     * @param {integer} item_id Ieraksta ID
     * @returns {undefined}
     */
    var handleInfoTaskSendBtnClick = function(frm_info, section, item_id) {
        frm_info.find(".dx-cms-info-btn-send").click(function() {
            var data = frm_info.find("input[name=empl_txt]").select2('data');
            
            if (!data || data.id == 0) {
                notify_err(frm_info.attr('dx_error_empl_not_set'));
                frm_info.find("input[name=empl_txt]").select2("open");
                return;
            }
            
            var formData = new FormData();
            formData.append("list_id", section.attr("dx_list_id"));
            formData.append("item_id", item_id);
            formData.append("empl_id", data.id);
            formData.append("task_info", frm_info.find("textarea[name=task_details]").val());

            var request = new FormAjaxRequest ("send_info_task", "", "", formData);

            request.callback = function() {
               notify_info("Dokuments veiksmīgi nodots informācijai darbiniekam " + data.text + "!");
               
               frm_info.find(".dx-cms-no-info").remove();
               frm_info.find(".dx-cms-info-list .scroller").append("<p>" + data.text + "</p>");
               
               var cnt = parseInt(frm_info.find(".dx-cms-info-task-count").text()) + 1;
               
               $("#list_item_view_form_" + section.attr("dx_form_id")).find(".dx-cms-info-task-count").text(cnt);               
               $("#list_item_view_form_" + section.attr("dx_form_id")).find(".dx-cms-info-task-count").show();
               frm_info.find(".dx-cms-info-task-count").text(cnt);
               
               frm_info.find("input[name=empl_txt]").select2('data', {id:0, text:"", position_title: ""});
               
               frm_info.find(".dx-cms-empl-position-title").text("");
            };

            // izpildam AJAX pieprasījumu
            request.doRequest();
        });
    };
    
    /**
     * Adjusts tabs with custom data appearance
     * @param {object} section HTML form element ID
     * @returns {undefined}
     */
    var adjustDataTabs = function(section) {
        
        var frm_main = $("#list_item_view_form_" + section.attr("dx_form_id"));
        
        frm_main.on('shown', function () {               
            adjustDataTabsLeft(section);
        });
    };
    
    /**
     * Adjusts data tabs fields position - move to left to align in the same line as general fields
     * NOTE: it is expected that the first tab in form is data tab (not with register attached)
     * @param {object} section HTML form's element
     * @returns {undefined}
     */
    var adjustDataTabsLeft = function(section) {
        var frm = $("#item_edit_form_" + section.attr("dx_form_id")); 
            
        var f_el = document.getElementById(frm.attr("id"));
        var fRect = f_el.getBoundingClientRect();
        var general_width = fRect.right-fRect.left;
        var margin = 0;
        
        var first_tab = frm.find(".tab-content .form-horizontal").first();
        
        if (first_tab) {
            var element = document.getElementById(first_tab.attr("id"));
            if (element) {
            
                var elemRect = element.getBoundingClientRect();
                var sect_width = elemRect.right - elemRect.left;    

                margin = sect_width - general_width+40+2;

                frm.find(".tab-content .form-horizontal").each(function() {
                    $(this).css("margin-left", margin + "px");
                });
                
                frm.find(".tab-content .dx-group-label").each(function() {
                    $(this).css("margin-left", Math.abs(margin) + "px");
                });
            }
        }
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializētās formas
     * 
     * @returns {undefined}
     */
    var initForm = function()
    {        
        $(".dx-cms-form-fields-section[dx_is_init='0']").each(function() {
            handleRegBtnClick($(this));
            handleWFInitBtnClick($(this));
            handleInfoTaskBtnClick($(this));
            adjustDataTabs($(this));
            $(this).attr('dx_is_init', 1); // uzstādam pazīmi, ka forma inicializēta
        });
        
    };

    return {
        init: function() {
            initForm();
        },
        adjustDataTabs: function(frm) {
            adjustDataTabsLeft(frm);
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {            
    FormLogic.init();           
});