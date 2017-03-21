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

           // reinit top menu
           $("#top_toolbar_" + form_htm_id).find(".dx-wf-menu-group").remove();
           $("#top_toolbar_" + form_htm_id).prepend(data.status_btn);
           var frm_section = $( "#" + form_htm_id).find('.dx-cms-form-fields-section');
           handleTaskHistoryMenuClick(frm_section);
           handleCancelWorkflowMenuClick(frm_section);
           
           $("#top_toolbar_" + form_htm_id).find(".dx_form_btns_left").html(data.left_btns);           
           initTopLeftBtns(frm_section);
                      
           // hide reject info if it is displayed
           $("#" + form_htm_id).find('.dx-reject-info').hide();
           
           notify_info(Lang.get('task_form.msg_workflow_startet'));

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
                notify_err(Lang.get('task_form.err_provide_approver'));
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
                notify_err(Lang.get('task_form.err_first_save_to_init'));
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
                notify_err(Lang.get('task_form.err_first_save_to_info'));
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
                    frm_info.find("select[name=role]").val(0);
                    frm_info.find(".dx-cms-empl-position-title").text("");
                    frm_info.find("select[name=role]").focus();
                }
            });
            
            frm_info.modal('show');            
        });
    };
    
    /**
     * Handles menu click for task history opening
     * @param {object} section Form object
     * @returns {undefined}
     */
    var handleTaskHistoryMenuClick = function(section) {
        $("#list_item_view_form_" + section.attr("dx_form_id")).find(".dx-menu-task-history").click(function() {
            var item_id = $( "#item_edit_form_" + section.attr("dx_form_id")  + " input[name='item_id']").val();
            var list_id = section.attr("dx_list_id");
            
            $('#popup_window .modal-header h4').html(Lang.get('task_form.history_title'));

            $("#popup_body").html(getProgressInfo());
            $('#popup_window').modal('show');

            var formData = "item_id=" + item_id + "&list_id=" + list_id;

            var request = new FormAjaxRequestIE9 ('get_tasks_history', "", "", formData);            
            request.progress_info = "";                       

            request.callback = function(data) {
                $('#popup_body').html(data['html']);
            };

            // execute AJAX request
            request.doRequest();
        });
    };
    
    /**
     * Handles history link click - opens history details list
     * @param {object} section
     * @returns {undefined}
     */
    var handleItemHistoryClick = function(section) {
        $("#list_item_view_form_" + section.attr("dx_form_id")).find(".dx-cms-history-link").click(function() {
            var item_id = $( "#item_edit_form_" + section.attr("dx_form_id")  + " input[name='item_id']").val();
            var list_id = section.attr("dx_list_id");
            
            $('#popup_window .modal-header h4').html("<i class='fa fa-history'></i> " + Lang.get('form.history_form_title'));

            $("#popup_body").html(getProgressInfo());
            $('#popup_window').modal('show');

            var formData = "item_id=" + item_id + "&list_id=" + list_id;

            var request = new FormAjaxRequestIE9 ('get_item_history', "", "", formData);            
            request.progress_info = "";                       

            request.callback = function(data) {
                $('#popup_body').html(data['html']);
                handleHistoryDetailsClick($('#popup_body'));
            };

            // execute AJAX request
            request.doRequest();
        });
    };
    
    /**
     * Handles history details button click
     * @param {object} frm History details container
     * @returns {void}
     */
    var handleHistoryDetailsClick = function(frm) {
        frm.find(".dx-cms-history_details").click(function() {
           view_list_item('form', $(this).data('item_id'), $(this).data('list_id'), 0, 0, '', ''); 
        });
    };
    
    /**
     * Opens worfklow cancelation form
     * 
     * @param {object} section Form object
     * @returns {undefined}
     */
    var handleCancelWorkflowMenuClick = function(section) {
        var main_form = $("#list_item_view_form_" + section.attr("dx_form_id"));
        main_form.find(".dx-menu-cancel-workflow").click(function() {
            
            var cancel_form = $('#wf_cancel_form_' + section.attr("dx_form_id"));
            
            if (cancel_form.data('is-init') == "0") {
                cancel_form.find('.dx-btn-cancel-wf').click(function() {
                    handleCancelWorkflowBtnClick(cancel_form, main_form);
                });
                
                cancel_form.on('shown', function () {
                    cancel_form.find('textarea[name=comment]').focus();
                    cancel_form.find('.modal-body').css('height', '138px');
                });
                
                cancel_form.data('is-init', 1);
            }
            
            cancel_form.modal('show');
        });
    };
    
    /**
     * Handles workflow cancelation button pressing - cancels workflow
     * @returns {undefined}
     */
    var handleCancelWorkflowBtnClick = function(frm, main_form) {
        
        var item_id = main_form.find("input[name='item_id']").val();
        var list_id = frm.data('list-id');
        var grid_id = frm.data('grid-id');
        
        var comment = frm.find('textarea[name=comment]').val();
        
        if (comment.length == 0) {
            notify_err(Lang.get('task_form.err_comment_required_to_cancel'));
            comment.focus();
            return;
        }
        
        var formData = new FormData();
        formData.append("item_id", item_id);
        formData.append("list_id", list_id);
        formData.append("comment", comment);
        
        show_form_splash(1);
        var request = new FormAjaxRequest('cancel_workflow', "", "", formData);            
        request.progress_info = "";                       

        request.callback = function(data) {
            notify_info(Lang.get('task_form.msg_wf_canceled'));
            hide_form_splash(1);
            if (grid_id) {
                reload_grid(grid_id);
            }
            main_form.find(".dx-wf-divider-cancel").hide();
            main_form.find(".dx-menu-cancel-workflow").hide();
            main_form.find(".dx-wf-menu-btn").removeClass("blue-hoki").addClass("red-soft").css('border-color', '#E43A45');
            main_form.find(".dx-wf-menu-btn-title").html(Lang.get('task_form.doc_rejected'));
            
            var frm_section = main_form.find('.dx-cms-form-fields-section');
            $("#top_toolbar_" + main_form.attr('id')).find(".dx_form_btns_left").html(data.left_btns);           
            initTopLeftBtns(frm_section);
            
            frm.modal('hide');
        };

        // execute AJAX request
        request.doRequest();
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
            var role_id = frm_info.find('select[name=role] option:selected').val();
            var data = frm_info.find("input[name=empl_txt]").select2('data');
            var empl_id = 0;
            
            if (data && data.id > 0) {
                empl_id = data.id;
            }
            
            if (role_id == 0 && empl_id == 0) {
                notify_err(frm_info.attr('dx_error_empl_not_set'));
                frm_info.find("input[name=empl_txt]").select2("open");
                return;
            }
            
            var formData = new FormData();
            formData.append("list_id", section.attr("dx_list_id"));
            formData.append("item_id", item_id);
            formData.append("empl_id", empl_id);
            formData.append("role_id", role_id);
            formData.append("task_info", frm_info.find("textarea[name=task_details]").val());

            var request = new FormAjaxRequest ("send_info_task", "", "", formData);

            request.callback = function(ret) {
                
                var cnt_add = ret.users.length;

                if (cnt_add == 0) {
                    notify_err(Lang.get('wf_info_task.err_nothing_done'));
                    return;
                }
                
                var msg_end = "n";
                
                if (cnt_add == 1) {
                    msg_end = "1";
                }
                
                var msg = Lang.get('wf_info_task.msg_done') + " " + cnt_add + " " + Lang.get('wf_info_task.msg_done_end_' + msg_end) + "!";

                notify_info(msg);                

                frm_info.find(".dx-cms-no-info").remove();
                
                $.each( ret.users, function( key, value ) {
                    frm_info.find(".dx-cms-info-list .scroller").append("<p>" + value.display_name + "</p>");
                });

                var cnt = parseInt(frm_info.find(".dx-cms-info-task-count").text()) + cnt_add;

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
                
        var tabs_pane = frm.find(".dx-cms-left-tabs");
        
        if (tabs_pane.length > 0) {
            var tab_pane_width = tabs_pane.first().width();
            
            var margin = -(tab_pane_width + 40 + 2);
            
            frm.find(".tab-content .form-horizontal").each(function() {
                $(this).css("margin-left", margin + "px");
            });

            frm.find(".tab-content .dx-group-label").each(function() {
                $(this).css("margin-left", Math.abs(margin) + "px");
            });
        }
    };
    
    /**
     * Sets event handlers on forms top left menu buttons
     * 
     * @param {object} section Form object
     * @returns {undefined}
     */
    var initTopLeftBtns = function(section) {
        var frm = $("#list_item_view_form_" + section.attr("dx_form_id"));
        var item_id = frm.find("input[name='item_id']").val();
        var list_id = section.attr('dx_list_id');
        var parent_field_id = section.data('parent-field-id');
        var parent_item_id = section.data('parent-item-id');
        var grid_htm_id = section.attr('dx_grid_id');
        var frm_uniq_id = section.attr('dx_form_id');
        
        frm.find('.dx-form-btn-edit[data-is-init!="1"]').click(function() {
            open_form('form', item_id, list_id, parent_field_id, parent_item_id, grid_htm_id, 1, 'list_item_view_form_' + frm_uniq_id);
            $(this).attr('data-is-init', 1);
        });
        
        frm.find('.dx-form-btn-delete[data-is-init!="1"]').click(function() {
            delete_list_item('list_item_view_form_' + frm_uniq_id, grid_htm_id);
            $(this).attr('data-is-init', 1);
        });
        
        frm.find('.dx-form-btn-word[data-is-init!="1"]').click(function() {
            generate_word(item_id, list_id, grid_htm_id, 'list_item_view_form_' + frm_uniq_id);
            $(this).attr('data-is-init', 1);
        });
        
        frm.find('.dx-form-btn-print[data-is-init!="1"]').click(function() {
            downloadFormPDF(list_id, item_id);
            $(this).attr('data-is-init', 1);
        });
        
        handleWFInitBtnClick(section);
        handleInfoTaskBtnClick(section);
        
        initCancelLogic(section, list_id, item_id);
    };
    
    /**
     * Init lock/unclock logic on cancelation events
     * 
     * @param {object}  section CMS forms fields section HTML element
     * @param {integer} list_id Register ID
     * @param {integer} item_id Item ID
     * @returns {undefined}
     */
    var initCancelLogic = function(section, list_id, item_id) {
        if (item_id == 0 || section.data('is-edit-mode') !== 1) {
            return;
        }
        
        section.closest('.modal-content').find('.dx-form-header button.dx-form-close-btn').click(function() {
            unlockItem(list_id, item_id);
        });
        
        section.closest('.modal-content').find('.modal-footer button.dx-btn-cancel-form').click(function() {
            unlockItem(list_id, item_id);
        });
        
        $(window).on('beforeunload', function()
        {
            unlockItem(list_id, item_id);

            setTimeout(function(){ 
                // unload canceled...
                $.ajax({
                    type: 'GET',
                    url: DX_CORE.site_url + 'form/lock_item/' + list_id + '/' + item_id,
                    dataType: 'json',
                    success: function(data) {
                        // item unlocked
                    }
                });
            }, 3000);
        });
    };
    
    /**
     * Unlocks item so other users can edit it
     * 
     * @param {integer} list_id Register ID
     * @param {integer} item_id Item ID
     * @returns {undefined}
     */
    var unlockItem = function(list_id, item_id) {
        show_form_splash(1);
                        
        $.ajax({
            type: 'GET',
            url: DX_CORE.site_url + 'form/unlock_item/' + list_id + '/' + item_id,
            dataType: 'json',
            success: function(data) {
                // item unlocked
                hide_form_splash(1);
            }
        });
    };
    
    /**
     * Generated and downloads PDF withs forms data
     * 
     * @param {integer} list_id Register ID
     * @param {integer} item_id Item ID
     * @returns {undefined}
     */
    function downloadFormPDF(list_id, item_id) {

       show_form_splash();
       show_page_splash();
       var open_url = DX_CORE.site_url + "get_form_pdf_" + item_id + "_" + list_id;
       
       $.fileDownload(open_url, {
           successCallback: function(url) {
               hide_form_splash();
               hide_page_splash();
               notify_info(DX_CORE.trans_file_downloaded);            
           },
           failCallback: function(html, url) {
               hide_form_splash();
               hide_page_splash(); 
               console.log("Download PDF Error: " + html);
               try {
                   var myData = JSON.parse(html);
                   if (myData['success'] == 0) {
                       notify_err(myData['error']);
                   }
               } catch (err) {
                   notify_err(DX_CORE.trans_sys_error);
               }
           }
       });
   };
    
    /**
     * Sets focus on first editable field
     * 
     * @param {object} frm Form HTML object
     * @returns {undefined}
     */
    var setFocusFirstField = function(frm) {
        setTimeout(function(){ 
            frm.find(':input:enabled:visible:not(.dx-not-focus):first').focus(); 
        }, 1000);        
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
            handleTaskHistoryMenuClick($(this));
            handleCancelWorkflowMenuClick($(this));
            handleItemHistoryClick($(this));
            
            adjustDataTabs($(this));
            setFocusFirstField($(this));
            
            initTopLeftBtns($(this));
            
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

$(window).on('beforeunload', function()
{
    var edit_forms = $(".dx-cms-form-fields-section[data-is-edit-mode=1]");
    
    if(edit_forms.length > 0){
        hide_page_splash(1);
        hide_form_splash(1);
        return 'Your changes have not been saved.';
    }
});