/**
 * Tabulāro sarakstu JavaScript funkcionalitāte
 *
 * @type _L4.Anonym$0|Function
 */
var BlockViews = function () {
    /**
     * SVS saknes domēna adrese
     * Šo parametru uzstāda PHP pusē ar Request::root() vērtību
     * @type String
     */
    var root_url = "";

    /**
     * Tiek izmantots lai atcerētos pašreizējo vertikālās ritjoslas pozīciju
     * @type Number
     */
    var scrollTop = 0;

    /**
     * Atveramā ieraksta ID
     * Ja vienā lapā būs n reģistri, tad pēc ID mēģinās atvērt pirmā reģistra kartiņu
     *
     * @type Number
     */
    var open_item_id = 0;

    /**
     * Indicates if mouse pointer is in filtering menu area
     * 
     * @type Number
     */
    var is_filter_menu_in = 0;
    
    /**
     * Pārlādē bloka tabulārā saraksta datus.
     * Pārlādē vai nu sarakstu, kas ir galvenajā lapā vai arī formā iekļauto sadaļas sarakstu
     *
     * @param {string} grid_id Tabularā saraksta elementa HTML ID
     * @param {string} tab_id  Formas sadaļas (TABa) HTML elementa ID
     * @returns {undefined}
     */
    var reloadBlockGrid = function (grid_id, tab_id) {
        if (tab_id) {
            reload_tab_grid(grid_id);
        }
        else {
            reload_grid(grid_id);
        }
    };

    /**
     * Nodrošina ritjoslas noritināšanu līdz lejai pēc saraksta datu pārlādes
     *
     * @param {string} form_htm_id Formas HTML elementa ID
     * @returns {undefined}
     */
    var scrollElement = function (form_htm_id) {
        var elem = null;
        if (form_htm_id) {
            elem = $('#' + form_htm_id).find(".modal-body");
        }
        else {
            elem = $(document);
        }

        setTimeout(function () {
            elem.scrollTop(scrollTop);
        }, 100);


    };

    /**
     * Nodrošina pašreizējo vertikālās ritjoslas pozīcijas atcerēšanos
     *
     * @param {string} form_htm_id Formas HTML elementa ID
     * @returns {undefined}
     */
    var setScrollTop = function (form_htm_id) {
        var elem = null;
        if (form_htm_id) {
            elem = $('#' + form_htm_id).find(".modal-body");
        }
        else {
            elem = $(document);
        }

        scrollTop = elem.scrollTop();
    };

    /**
     * Nodrošina tabulārā saraksta lapošanas funkcionalitāti
     *
     * @param {string} grid_id    Tabulārā saraksta HTML elementa ID
     * @param {string} tab_id     Formas sadaļas (TABa) HTML elementa ID
     * @param {string} menu_id    Tabulārā saraksta augšējās rīkjoslas izvēlnes HTML elementa ID
     * @returns {undefined}
     */
    var handlePaginator = function (grid_id, tab_id, menu_id) {
        $('#paginator_' + grid_id + ' .dx-paginator-butons button').on('click', function (event) {
            event.preventDefault();
            $('#' + grid_id).data('grid_page_nr', $(this).attr('data_page_nr'));
            $('#' + grid_id).data('view_id', $('#' + menu_id + '_viewcbo option:selected').val());

            reloadBlockGrid(grid_id, tab_id);
        });
    };

    /**
     * Nodrošina tabulārā saraksta pārlādi atbilstoši izvēlētajam skatam no izkrītošās izvēlnes
     *
     * @param {string} menu_id            Tabulārā saraksta augšējās rīkjoslas izvēlnes HTML elementa ID
     * @param {string} tab_id             Formas sadaļas (TABa) HTML elementa ID
     * @param {integer} list_id           Reģistra ID no datu bāzes tabulas dx_lists
     * @param {integer} rel_field_id      Saistītā ieraksta lauka ID no datu bāzes tabulas dx_lists_fields
     * @param {integer} rel_field_value    Saistīta ieraksta lauka vērtība
     * @param {string} form_htm_id        Formas, kuras sadaļā iekļauts tabulārais saraksts, HTML elementa ID
     * @returns {undefined}
     */
    var handleView = function (menu_id, tab_id, list_id, rel_field_id, rel_field_value, form_htm_id) {
        $('#' + menu_id + '_viewcbo').change(function (event) {
            event.preventDefault();

            if (tab_id) {
                load_tab_grid(tab_id, list_id, $('#' + menu_id + '_viewcbo option:selected').val(), rel_field_id, rel_field_value, form_htm_id, 1, 5, 1);
            }
            else {
                show_page_splash(1);
                var url = root_url + 'skats_' + $('#' + menu_id + '_viewcbo option:selected').val();
                window.location.assign(encodeURI(url));
            }
        });
    };

    /**
     * Nodrošina tabulārā saraksta filtrēšanas funkcionalitāti
     * Virs katras kolonnas ir teksta lauks, kurā, ievadot tekstu, notiek datu atlase ar AJAX pieprasījumu
     *
     * @param {string} grid_id Tabularā saraksta elementa HTML ID
     * @param {string} tab_id  Formas sadaļas (TABa) HTML elementa ID
     * @returns {undefined}
     */
    var handleFilter = function (grid_id, tab_id) {
        $('#filter_' + grid_id + ' input').on('keypress', function (event) {

            if (event.which === 13) {
                $('#' + grid_id).data('grid_page_nr', '1');
                reloadBlockGrid(grid_id, tab_id);
            }

        });
    };

    /**
     * Nodrošina tabulārā saraksta kārtošanas funkcionalitāti
     * Noklikšķinot uz kolonnas virsraksta, notiek datu kārtošana/atlase ar AJAX pieprasījumu
     *
     * @param {string} grid_id Tabularā saraksta elementa HTML ID
     * @param {string} tab_id  Formas sadaļas (TABa) HTML elementa ID
     * @returns {undefined}
     */
    var handleSorting = function (grid_id, tab_id) {
        $('#' + grid_id + ' th.t_header').on('click', function (event) {
            event.preventDefault();
            performSorting(grid_id, tab_id, $(this).attr('fld_name'));            
        });
    };
    
    var performSorting = function(grid_id, tab_id, fld_name) {
        if ($('#' + grid_id).data('sorting_field') == fld_name)
        {
            if ($('#' + grid_id).data('sorting_direction') == '1')
            {
                $('#' + grid_id).data('sorting_direction', '2'); // desc
            }
            else
            {
                if ($('#' + grid_id).data('sorting_direction') == '2')
                {
                    $('#' + grid_id).data('sorting_direction', '0'); // remove sorting
                    $('#' + grid_id).data('sorting_field', '');
                }
            }

        }
        else
        {
            $('#' + grid_id).data('sorting_field', fld_name);
            $('#' + grid_id).data('sorting_direction', '1'); // asc
        }

        reloadBlockGrid(grid_id, tab_id);
    };

    /**
     * Nodrošina jaunu ierakstu pievienošanu uz pogas "Jauns" nospiešanu
     *
     * @param {string} grid_id            Tabularā saraksta elementa HTML ID
     * @param {string} menu_id            Tabulārā saraksta augšējās rīkjoslas izvēlnes HTML elementa ID
     * @param {integer} list_id           Reģistra ID no datu bāzes tabulas dx_lists
     * @param {integer} rel_field_id      Saistītā ieraksta lauka ID no datu bāzes tabulas dx_lists_fields
     * @param {integer} rel_field_value   Saistīta ieraksta lauka vērtība
     * @param {string} form_htm_id        Formas, kuras sadaļā iekļauts tabulārais saraksts, HTML elementa ID
     * @returns {undefined}
     */
    var handleBtnNew = function (grid_id, menu_id, list_id, rel_field_id, rel_field_value, form_htm_id) {
        $('#' + menu_id + '_new').button().click(function (event) {
            event.preventDefault();
            new_list_item(list_id, rel_field_id, rel_field_value, form_htm_id, grid_id);
        });
    };

    /**
     * Nodrošina tabulārā sarakta pārlādēšanu (datu atjaunināšanu) uz pogas "Pārlādēt" nospiešanu
     *
     * @param {string} menu_id Tabulārā saraksta augšējās rīkjoslas izvēlnes HTML elementa ID
     * @param {string} grid_id Tabularā saraksta elementa HTML ID
     * @param {string} tab_id  Formas sadaļas (TABa) HTML elementa ID
     * @returns {undefined}
     */
    var handleBtnRefresh = function (menu_id, grid_id, tab_id) {
        $('#' + menu_id + '_refresh').button().click(function (event) {
            event.preventDefault();
            reloadBlockGrid(grid_id, tab_id);
        });
    };
    
    /**
     * Reloads report's grid with date from/to parameters
     * 
     * @param {string} grid_id Grid's HTML element ID
     * @param {string} tab_id Tab's grid HTML element ID
     * @param {object} el_block View's element
     * @returns {undefined}
     */
    var handleBtnPrepareReport = function(grid_id, tab_id, el_block) {
        el_block.find('.dx-report-filter-btn').click(function() {
            event.preventDefault();
            reloadBlockGrid(grid_id, tab_id);
        });  
    };

    /**
     * Show or hide filtering fields
     *
     * @param {string} menu_id Grid's toolbar section HTML element's ID
     * @param {string} grid_id Grid's HTML element ID
     * @param {string} tab_id  Tab's HTML element ID in case if this is subgrid in an form
     * @param {object} el_block View's block HTML element
     * @returns {undefined}
     */
    var handleMenuFilter = function (menu_id, grid_id, tab_id, el_block) {
        el_block.find(".dx-filter").click(function () {
            var el_icon = $(this).find("i.fa-check");
            var el_filters = $("#filter_" + grid_id);
            if (el_icon.is(':visible')) {
                // hide filters row

                var el_filt_data = el_block.find('input[name=filter_data]');

                if (el_filt_data.val().length > 0 && el_filt_data.val() != '[]') {
                    // was filtered grid - lets reload with clear data
                    el_filt_data.val('');
                    el_filters.find('input').val('');
                    reloadBlockGrid(grid_id, tab_id);
                }
                else {
                    el_filters.hide();
                    el_icon.hide();
                    PageMain.resizePage();
                }
            }
            else {
                // show filters row
                                   
                var menu = $("#grid_popup_" + grid_id);
                var fld_name = menu.attr('data-field');
                                
                el_filters.show();
                el_icon.show();
                
                setTimeout(function(){ 
                    el_block.find("input[sql_name=" + fld_name + "]").focus(); 
                }, 100);
                
                PageMain.resizePage();
            }
            $(this).closest(".dx-dropdown-content").hide();
        });
    };

    /**
     * Opend view editing form
     *
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var handleBtnEditView = function (view_container) {
        view_container.find('.dx-view-edit-btn').click(function () {

            var view_id = view_container.find('select.dx-views-cbo option:selected').val();
            var frm_el = $("#" + view_container.attr("id") + "_popup");

            var formData = "view_id=" + view_id;

            var request = new FormAjaxRequestIE9('view/open', "", "", formData);
            request.progress_info = true;

            request.callback = function (data) {
                frm_el.find(".modal-body").html(data['html']);
                frm_el.find(".modal-body .dx-cms-nested-list").nestable();
                setFldEventHandlers(frm_el, frm_el, view_container);
                handleSearchField();
                handleIsMyCheck(frm_el);

                frm_el.find(".dx-view-btn-copy").show();
                frm_el.find(".dx-view-btn-delete").show();
                frm_el.find("span.badge").html(Lang.get('grid.badge_edit'));

                frm_el.modal('show');
            };

            // execute AJAX request
            request.doRequest();
        });
    };

    /**
     * Sets handles for checkboxies (is default and is my view only)
     *
     * @param {object} frm_el Fields UI forms HTML object
     * @returns {undefined}
     */
    var handleIsMyCheck = function (frm_el) {
        frm_el.find("input[name=is_my_view]").change(function () {
            if ($(this).prop('checked')) {
                frm_el.find("input[name=is_default]").prop('checked', '').closest('span').hide();
            }
            else {
                frm_el.find("input[name=is_default]").closest('span').show();
            }
        });

        frm_el.find("input[name=is_default]").change(function () {
            if ($(this).prop('checked')) {
                frm_el.find("input[name=is_my_view]").prop('checked', '').closest('span').hide();
            }
            else {
                frm_el.find("input[name=is_my_view]").closest('span').show();
            }
        });
    };

    /**
     * Moves field from used section to available fields section
     *
     * @param {object} frm_el Fields UI forms HTML object
     * @param {object} fld_el Field element HTML object
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var removeFld = function (frm_el, fld_el, view_container) {
        frm_el.find('.dx-fields-container .dx-available ol.dd-list').append(fld_el.closest('.dd-item').clone());
        fld_el.closest('.dd-item').remove();

        var new_el = frm_el.find('.dx-fields-container .dx-available ol.dd-list .dd-item').last();
        setFldEventHandlers(frm_el, new_el, view_container);

        clearSearchIfLast(frm_el, 'dx-used');
    };

    /**
     * Moves field from available section to used fields section
     *
     * @param {object} frm_el Fields UI forms HTML object
     * @param {object} fld_el Field element HTML object
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var addFld = function (frm_el, fld_el, view_container) {
        frm_el.find('.dx-fields-container .dx-used ol.dd-list').append(fld_el.closest('.dd-item').clone());
        fld_el.closest('.dd-item').remove();

        var new_el = frm_el.find('.dx-fields-container .dx-used ol.dd-list .dd-item').last();
        setFldEventHandlers(frm_el, new_el, view_container);

        clearSearchIfLast(frm_el, 'dx-available');
    };

    /**
     * Clear fields search input in case if no more fields in container (and show again all fields in container)
     *      *
     * @param {object} frm_el Fields UI forms HTML object
     * @param {string} fields_class HTML class name of fields container (dx-used or dx-available)
     * @returns {undefined}
     */
    var clearSearchIfLast = function (frm_el, fields_class) {
        if (frm_el.find('.dx-fields-container .' + fields_class + ' ol.dd-list .dd-item:visible').length == 0) {
            var txt = frm_el.find('.dx-fields-container .' + fields_class).closest('.portlet').find('input.dx-search');
            if (txt.val().length > 0) {
                txt.val('');
                txt.closest(".portlet").find(".dx-fields-container .dd-item").show();
                txt.focus();
            }
        }
    };

    /**
     * Sets events for added/moved field
     *
     * @param {object} frm_el Fields UI forms HTML object
     * @param {object} fld_el Field element HTML object
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var setFldEventHandlers = function (frm_el, fld_el, view_container) {
        fld_el.find('.dx-cms-field-remove').click(function () {
            removeFld(frm_el, $(this), view_container);
        });

        fld_el.find('.dx-cms-field-add').click(function () {
            addFld(frm_el, $(this), view_container);
        });

        fld_el.find('.dx-fld-title').click(function () {
            openSettings($(this), view_container);
        });
    };

    /**
     * Opens field's setting form
     *
     * @param {object} title_el Field item title HTML element
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var openSettings = function (title_el, view_container) {
        if (title_el.closest('.dx-cms-nested-list').hasClass('dx-used')) {
            var item = title_el.closest('.dd-item');
            var sett_el = view_container.find('.dx-popup-modal-settings');

            sett_el.find("input[name=is_hidden]").prop("checked", (item.attr("data-is-hidden") == "1") ? "checked" : "");
            sett_el.find("input[name=field_title]").val(title_el.text());
            sett_el.find("select[name=field_operation]").val(item.attr("data-operation-id"));

            if (item.attr("data-field-type") == "autocompleate" || item.attr("data-field-type") == "rel_id") {
                sett_el.find("select[name=field_operation]").attr("data-criteria", "auto");

                var auto_fld = sett_el.find("div.dx-autocompleate-field");
                auto_fld.attr("data-rel-list-id", item.attr('data-rel-list-id'));
                auto_fld.attr("data-rel-field-id", item.attr('data-rel-field-id'));
                auto_fld.attr("data-item-value", item.attr('data-criteria'));
                auto_fld.attr("data-field-id", item.attr('data-id'));

                var formData = new FormData();
                formData.append("list_id", item.attr('data-rel-list-id'));
                formData.append("txt_field_id", item.attr('data-rel-field-id'));
                formData.append("txt_field_id", item.attr('data-rel-field-id'));
                formData.append("value_id", item.attr('data-criteria'));

                show_form_splash();
                $.ajax({
                    type: 'POST',
                    url: DX_CORE.site_url + "view/auto_data",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    async: false,
                    success: function (data) {
                        hide_form_splash();
                        auto_fld.attr("data-min-length", data['count']);
                        auto_fld.attr("data-item-text", data['txt']);
                    }
                });

                AutocompleateField.initSelect(auto_fld);
            }
            else {
                sett_el.find("select[name=field_operation]").attr("data-criteria", "text");
                sett_el.find("input[name=criteria_value]").val(item.attr("data-criteria"));
            }

            showHideCriteria(sett_el, sett_el.find("select[name=field_operation]"));

            var btn_save = sett_el.find('.dx-settings-btn-save');

            btn_save.off("click");
            btn_save.click(function () {
                var oper_el = sett_el.find('select[name=field_operation]');

                var crit_val = "";
                if (oper_el.attr("data-criteria") == "text") {
                    crit_val = sett_el.find('input[name=criteria_value]').val();
                }
                else {
                    crit_val = parseInt(sett_el.find('input.dx-auto-input-id').val());
                }

                if (oper_el.val() && oper_el.find('option:selected').attr('data-is-criteria') != "0" && !crit_val) {
                    notify_err(Lang.get('grid.error_filter_must_be_set'));
                    return false;
                }

                item.attr("data-criteria", crit_val);
                item.attr("data-is-hidden", sett_el.find('input[name=is_hidden]').is(":checked") ? 1 : 0);
                item.attr("data-operation-id", oper_el.val());

                sett_el.modal('hide');
            });

            sett_el.modal('show');
        }
    };

    /**
     * Handles event for show or hide criteria field depending on selected operation
     *
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var handleFieldOperation = function (view_container) {
        var sett_el = view_container.find('.dx-popup-modal-settings');
        sett_el.find('select[name=field_operation]').change(function () {
            showHideCriteria(sett_el, $(this));
        });
    };

    /**
     * Shoe or hide criteria field depending on selected operation
     * @param {object} sett_el Setting popup form HTML element
     * @param {object} sel_el Operation select HTML element
     * @returns {undefined}
     */
    var showHideCriteria = function (sett_el, sel_el) {
        if (sel_el.find('option:selected').attr('data-is-criteria') != "0") {
            if (sel_el.attr("data-criteria") == "text") {
                sett_el.find(".dx-criteria-text").show();
                sett_el.find(".dx-criteria-auto").hide();
                sett_el.find("input[name=criteria_value]").focus();
            }
            else {
                sett_el.find(".dx-criteria-text").hide();
                sett_el.find(".dx-criteria-auto").show();
                sett_el.find('.dx-auto-input-select2').select2("open");
            }
        }
        else {
            sett_el.find("input[name=criteria_value]").val('');
            sett_el.find(".dx-criteria-auto").hide();
            sett_el.find(".dx-criteria-text").hide();
            sett_el.find('.dx-auto-input-select2').select2('data', {id: 0, text: ""});
            sett_el.find("input.dx-auto-input-id").val(0);
        }
    };

    /**
     * Handles fields searching functionality
     * @returns {undefined}
     */
    var handleSearchField = function () {
        $("input.dx-search").on("keyup", function () {
            if (!$(this).val()) {
                $(this).closest(".portlet").find(".dx-fields-container .dd-item").show();
                return;
            }
            $(this).closest(".portlet").find(".dx-fields-container .dd-item").hide();
            $(this).closest(".portlet").find(".dx-fields-container .dx-fld-title:contains('" + $(this).val() + "')").closest(".dd-item").show();

        });
    };

    /**
     * Handles view copy function
     *
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var handleBtnCopy = function (view_container) {
        var pop_el = $("#" + view_container.attr("id") + "_popup");
        pop_el.find(".dx-view-btn-copy").click(function () {
            var frm_el = pop_el.find(".dx-view-edit-form");
            frm_el.data('view-id', 0);
            pop_el.find(".dx-view-btn-copy").hide();
            pop_el.find(".dx-view-btn-delete").hide();
            pop_el.find("span.badge").html(Lang.get('grid.badge_new'));
            frm_el.find("input[name=view_title]").val(frm_el.find("input[name=view_title]").val() + " - " + Lang.get('grid.title_copy')).focus();

            frm_el.find('input[name=is_default]').prop("checked", '').show().closest('span').show();
            frm_el.find('input[name=is_my_view]').prop("checked", '').closest('span').show();
        });
    };

    /**
     * Handles button "Delete" pressing
     *
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var handleBtnDelete = function (view_container) {
        var pop_el = $("#" + view_container.attr("id") + "_popup");
        pop_el.find(".dx-view-btn-delete").click(function () {
            PageMain.showConfirm(deleteView, view_container, null, Lang.get('grid.confirm_delete'), Lang.get('form.btn_yes'), Lang.get('form.btn_no'));
        });
    };

    /**
     * Handles view deletion functionality
     *
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var deleteView = function (view_container) {
        var pop_el = $("#" + view_container.attr("id") + "_popup");
        var frm_el = pop_el.find(".dx-view-edit-form");

        var formData = new FormData();
        formData.append("view_id", frm_el.data('view-id'));
        formData.append("list_id", frm_el.data('list-id'));
        formData.append('tab_id', view_container.attr('dx_tab_id'));

        var request = new FormAjaxRequest('view/delete', "", "", formData);
        request.progress_info = true;

        request.callback = function (data) {
            if (data["success"] == 1) {
                pop_el.modal('hide');
                reloadAnotherView(view_container, data["view_id"]);
            }
        };

        // execute AJAX request
        request.doRequest();
    };

    /**
     * Handles button event - save view data
     *
     * @param {object} view_container Grid view main object's HTML element
     * @returns {undefined}
     */
    var handleBtnSaveView = function (view_container) {
        var pop_el = $("#" + view_container.attr("id") + "_popup");
        pop_el.find(".dx-view-btn-save").click(function () {

            var frm_el = pop_el.find(".dx-view-edit-form");
            var view_id = frm_el.data('view-id');
            var grid_el = view_container.find('.dx-grid-table').last();

            var formData = new FormData();
            formData.append("view_id", view_id);
            formData.append("list_id", frm_el.data('list-id'));
            formData.append("view_title", frm_el.find('input[name=view_title]').val());
            formData.append("is_default", frm_el.find('input[name=is_default]').is(":checked") ? 1 : 0);
            formData.append("is_my_view", frm_el.find('input[name=is_my_view]').is(":checked") ? 1 : 0);
            formData.append("fields", getFieldsState(frm_el.find('.dx-fields-container .dx-used')));
            formData.append('grid_id', grid_el.attr('id'));

            var request = new FormAjaxRequest('view/save', "", "", formData);
            request.progress_info = true;

            request.callback = function (data) {
                if (data["success"] == 1) {

                    pop_el.modal('hide');
                    pop_el.attr("id", pop_el.attr("id") + "_" + $(".dx-popup-modal").length);

                    if (view_id == 0) {
                        reloadAnotherView(view_container, data["view_id"]);
                    }
                    else {
                        reloadBlockGrid(grid_el.attr('id'), grid_el.data('tab_id'));
                    }
                }
            };

            // execute AJAX request
            request.doRequest();

        });
    };

    /**
     * Loads view after previous view deletion or new view creation
     *
     * @param {object} view_container Grid view main object's HTML element
     * @param {integer} view_id View ID
     * @returns {undefined}
     */
    var reloadAnotherView = function (view_container, view_id) {
        if (view_container.attr('dx_tab_id')) {
            load_tab_grid(view_container.attr('dx_tab_id'), view_container.attr('dx_list_id'), view_id, view_container.attr('dx_rel_field_id'), view_container.attr('dx_rel_field_value'), view_container.attr('dx_form_htm_id'), 1, 5, 1);
        }
        else {
            show_page_splash(1);
            var url = root_url + 'skats_' + view_id;
            window.location.assign(encodeURI(url));
        }
    };

    /**
     * Prepares JSON string with all fields included in view (in correct order)
     *
     * @param {object} block Fields container HTML element
     * @returns {string}
     */
    var getFieldsState = function (block) {
        var ret_arr = new Array();

        block.find(".dd-item").each(function () {
            var item = {
                "field_id": $(this).attr('data-id'),
                "aggregation_id": $(this).attr('data-aggregation-id'),
                "list_id": $(this).attr('data-list-id'),
                "is_hidden": $(this).attr('data-is-hidden'),
                "operation_id": $(this).attr('data-operation-id'),
                "criteria": $(this).attr('data-criteria')
            };
            ret_arr.push(item);
        });

        return JSON.stringify(ret_arr);
    };

    /**
     * Exports grid data to the Excel
     *
     * @param {string} menu_id            Grid's menu HTML element ID
     * @param {integer} view_id           View ID from the table dx_views
     * @param {integer} rel_field_id      Related field ID (by which grid is binded)
     * @param {integer} rel_field_value   Related field value
     * @param {string} form_htm_id        Form's HTML ID
     * @param {string} grid_id            Grid's GUID
     * @returns {undefined}
     */
    var handleBtnExcel = function (menu_id, view_id, rel_field_id, rel_field_value, form_htm_id, grid_id) {
        $('#' + menu_id + '_excel').button().click(function (event) {
            event.preventDefault();

            if (!isRelatedItemSaved(rel_field_id, rel_field_value, form_htm_id)) {
                return;
            }

            download_excel(view_id, rel_field_id, rel_field_value, grid_id);
        });
    };

    /**
     * Opens data import form
     *
     * @param {string} menu_id Menu item HTML id
     * @param {integer} rel_field_id Related field ID (by which grid is binded)
     * @param {integer} rel_field_value Related field value
     * @param {string} form_htm_id Form's HTML ID
     * @returns {undefined}
     */
    var handleBtnImport = function (menu_id, rel_field_id, rel_field_value, form_htm_id) {
        $('#' + menu_id + '_import').button().click(function (event) {
            event.preventDefault();

            if (!isRelatedItemSaved(rel_field_id, rel_field_value, form_htm_id)) {
                return;
            }

            var import_frm = $("#form_import_" + menu_id);

            import_frm.modal('show');

        });
    };

    /**
     * Checks if form is saved (item have an ID)
     * This check must be done for forms, where an subgrid is included
     *
     * @param {integer} rel_field_id Related field ID (by which grid is binded)
     * @param {integer} rel_field_value Related field value
     * @param {string} form_htm_id Form's HTML ID
     * @returns {Boolean} True - if everything ok, False - if form must be saved before
     */
    var isRelatedItemSaved = function (rel_field_id, rel_field_value, form_htm_id) {

        if (rel_field_id > 0 && rel_field_value == 0) {
            rel_field_value = $("#" + form_htm_id + " input[name='item_id']").val();

            if (rel_field_value == 0) {
                notify_err(Lang.get('errors.first_save_for_related'));
                return false;
            }
        }

        return true;
    };

    /**
     * Starts data importing from Excel
     *
     * @param {string} menu_id Menu item HTML id
     * @returns {undefined}
     */
    var handleBtnStartImport = function (menu_id) {
        $('#btn_start_import_' + menu_id).button().click(function (event) {
            event.preventDefault();

            var import_frm = $("#form_import_" + menu_id);
            var file_name = import_frm.find("input[name=import_file]").val();

            var formData = new FormData();

            if (!process_Input_simple(import_frm.attr("id"), formData) || file_name.length == 0) {
                notify_err(import_frm.attr("data-trans-invalid-file"));
                return;
            }

            if (!isOkImportFileExt(import_frm, file_name)) {
                return;
            }

            formData.append("list_id", import_frm.attr("data-list-id"));

            import_frm.find(".dx-import-fields").hide();
            import_frm.find(".dx-import-progress").show();
            import_frm.find('.alert-error').hide();

            $('#btn_start_import_' + menu_id).hide();

            var request = new FormAjaxRequest("import_excel", "", "", formData);

            request.progress_info = "";
            request.err_callback = function (err) {
                import_frm.find(".dx-import-progress").hide();
                import_frm.find(".dx-import-fields").show();
                $('#btn_start_import_' + menu_id).show();

                import_frm.find('.alert-error').html(err).show();
            };

            request.callback = function (data) {
                var msg = getImportMsg(data);

                reload_grid(import_frm.attr("data-grid-id"));
                notify_info(msg);

                import_frm.find(".dx-import-progress").hide();


                import_frm.find('.alert-info').html(msg).show();

                prepareErrors(data, import_frm);
            };

            // perform Ajax request
            request.doRequest();
        });
    };

    var getImportMsg = function (data) {
        var msg = Lang.get('grid.success');
        var cnt = "";

        if (data["imported_count"] > 0) {
            cnt = Lang.get('grid.count_imported') + data["imported_count"];
        }

        if (data["updated_count"] > 0) {
            if (data["imported_count"] > 0) {
                cnt = cnt + ". ";
            }
            cnt = Lang.get('grid.count_updated') + data["updated_count"] + ".";
        }

        if (cnt == "") {
            msg = msg + " " + Lang.get('grid.nothing_imported');
        }
        else {
            msg = msg + " " + cnt;
        }

        return msg;
    };

    /**
     * Validated uploaded file extension - is it supported
     *
     * @param {object} import_frm Importing HTML form's element
     * @param {string} file_name File name
     * @returns {Boolean}   True - if extension is valid, False - if invalid
     */
    var isOkImportFileExt = function (import_frm, file_name) {
        var ext = file_name.split('.').pop().toLowerCase();   //Check file extension if valid or expected

        var valid_ext = ['xlsx', 'xls', 'csv', 'zip'];

        if ($.inArray(ext, valid_ext) == -1) {
            notify_err(import_frm.attr("data-trans-invalid-file-format"));
            return false;
        }

        return true;
    };

    /**
     * Prepares/set importing error message - there can be several errors in 1 response, we need to concatenate them
     *
     * @param {JSON} data           AJAX JSON response object
     * @param {object} import_frm   HTML element ID for import form
     * @returns {undefined}
     */
    var prepareErrors = function (data, import_frm) {
        var err_arr = [];

        err_arr[0] = concatErrFields(import_frm, data["not_match"]);
        err_arr[1] = concatErr(import_frm, data["duplicate"], "data-trans-excel-row");
        err_arr[2] = concatErr(import_frm, data["dependency"], "data-trans-excel-dependent");

        var err_txt = "";
        for (var $i = 0; $i < err_arr.length; $i++) {
            if (err_txt.length > 0) {
                err_txt = err_txt + "<br /><br />";
            }

            err_txt = err_txt + err_arr[$i];
        }

        if (data["errors"].length > 0) {
            if (err_txt.length > 0) {
                err_txt = err_txt + "<br /><br />";
            }
            err_txt = err_txt + data["errors"];
        }


        if (err_txt.length > 0) {
            import_frm.find('.alert-error').html(err_txt).show();
        }
    };

    /**
     * Prepare message with error rows
     * @param {object} import_frm Importing form HTML element
     * @param {string} rows Row numbers delimited by coma
     * @param {string} err_attribute Error data attribute of importing from
     * @returns {Function|_L19.String|String}
     */
    var concatErr = function (import_frm, rows, err_attribute) {
        if (!rows) {
            return "";
        }

        var htm = "";

        if (rows.length > 0) {
            htm = import_frm.attr(err_attribute) + rows;
        }

        return htm;
    };

    /**
     * Prepare message with ignored fields
     * @param {object} import_frm Importing form HTML element
     * @param {array} flds JSON array with ignored fields
     * @returns {undefined}
     */
    var concatErrFields = function (import_frm, flds) {
        if (!flds) {
            return "";
        }

        var htm = "";
        var not_match = function (item, index) {
            if (htm.length > 0) {
                htm = htm + ", ";
            }

            htm = htm + item;
        };
        flds.forEach(not_match);

        if (htm.length > 0) {
            htm = import_frm.attr("data-trans-ignored-columns") + htm;
        }

        return htm;
    };

    /**
     * Nodrošina tabulas rindas konteksta izvēlnes "Skatīt" funcionalitāti - atver ieraksta skatīšanās formu
     *
     * @param {string} grid_form          Formas URL (bez root daļas), kas piesaistīta reģistram ierakstu attēlošanai
     * @param {string} grid_id            Tabularā saraksta elementa HTML ID
     * @param {integer} list_id           Reģistra ID no datu bāzes tabulas dx_lists
     * @param {integer} rel_field_id      Saistītā ieraksta lauka ID no datu bāzes tabulas dx_lists_fields
     * @param {integer} rel_field_value   Saistīta ieraksta lauka vērtība
     * @param {string} form_htm_id        Formas, kuras sadaļā iekļauts tabulārais saraksts, HTML elementa ID
     * @returns {undefined}
     */
    var handleRowBtnView = function (grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id) {
        $('#' + grid_id + ' a.dx-grid-cmd-view').click(function (event) {
            event.preventDefault();
            show_form_splash();
            show_page_splash();
            view_list_item(grid_form, $(this).attr('dx_item_id'), list_id, rel_field_id, rel_field_value, grid_id, form_htm_id);
        });
    };

    /**
     * Nodrošina tabulas rindas konteksta izvēlnes "Rediģēt" funkcionalitāti - atver ieraksta redigēšanas formu
     *
     * @param {string} grid_form          Formas URL (bez root daļas), kas piesaistīta reģistram ierakstu attēlošanai
     * @param {string} grid_id            Tabularā saraksta elementa HTML ID
     * @param {integer} list_id           Reģistra ID no datu bāzes tabulas dx_lists
     * @param {integer} rel_field_id      Saistītā ieraksta lauka ID no datu bāzes tabulas dx_lists_fields
     * @param {integer} rel_field_value   Saistīta ieraksta lauka vērtība
     * @returns {undefined}
     */
    var handleRowBtnEdit = function (grid_form, grid_id, list_id, rel_field_id, rel_field_value) {
        $('#' + grid_id + ' a.dx-grid-cmd-edit').click(function (event) {
            event.preventDefault();
            show_form_splash();
            show_page_splash();
            open_form(grid_form, $(this).attr('dx_item_id'), list_id, rel_field_id, rel_field_value, grid_id, 1, '');
        });
    };

    /**
     * Nodrošina tabulas rindas konteksta izvēlnes "Dzēst" funkcionalitāti - atver ieraksta dzēšanas formu
     *
     * @param {string} grid_id  Tabularā saraksta elementa HTML ID
     * @param {integer} list_id Reģistra ID no datu bāzes tabulas dx_lists
     * @returns {undefined}
     */
    var handleRowBtnDel = function (grid_id, list_id) {
        $('#' + grid_id + ' a.dx-grid-cmd-delete').click(function (event) {
            event.preventDefault();
            if (!confirm(DX_CORE.trans_confirm_delete)) {
                return;
            }
            delete_multiple_items(list_id, grid_id, $(this).attr('dx_item_id'), 1);
        });
    };

    /**
     * Nodrošina iezīmēto tabulas rindu dzēšanu
     *
     * @param {object} block    Grida bloka elements
     * @param {string} grid_id  Tabularā saraksta elementa HTML ID
     * @param {integer} list_id Reģistra ID no datu bāzes tabulas dx_lists
     * @returns {undefined}
     */
    var handleMarkBtnDel = function (block, grid_id, list_id) {
        $('#paginator_' + grid_id + ' a.dx-grid-cmd-delall').click(function (event) {
            event.preventDefault();

            var items = "";
            var cnt = 0;
            $('#' + grid_id + ' input.dx-grid-input-check:checked').each(function () {
                if (items.length > 0) {
                    items = items + "|";
                }
                items = items + $(this).attr('dx_item_id');
                cnt++;
            });

            if (cnt === 0) {
                notify_err(block.attr("data-trans-msg-marked"));
                return;
            }

            var msg = block.attr("data-trans-confirm-del1");

            if (cnt > 1) {
                msg = block.attr("data-trans-confirm-del-all").replace('%s', cnt);
            }

            if (!confirm(msg)) {
                return;
            }

            delete_multiple_items(list_id, grid_id, items, ((cnt > 1) ? 0 : 1));
        });
    };

    /**
     * Nodrošina visu tabulas rindu iezīmēšanu
     *
     * @param {string} grid_id      Tabularā saraksta elementa HTML ID
     * @param {string} form_htm_id  Formas HTML elementa ID
     * @returns {undefined}
     */
    var handleMarkBtnCheck = function (grid_id, form_htm_id) {
        $('#paginator_' + grid_id + ' a.dx-grid-cmd-markall').click(function (e) {

            setScrollTop(form_htm_id);

            var cnt = 0;
            $('#' + grid_id + ' input.dx-grid-input-check').each(function () {
                $(this).prop('checked', true);
                cnt++;
            });

            $('#paginator_' + grid_id + ' span.dx-marked-count-lbl').text(cnt);

            scrollElement(form_htm_id);
        });
    };

    /**
     * Nodrošina iezīmēto tabulas rindu skaita atjaunināšanu pēc rindas iezīmēšanas/atzīmēšanas
     *
     * @param {string} grid_id Tabularā saraksta elementa HTML ID
     * @returns {undefined}
     */
    var handleMarkCounter = function (grid_id) {
        $('#' + grid_id + ' input.dx-grid-input-check').change(function () {
            $('#paginator_' + grid_id + ' span.dx-marked-count-lbl').text($('#' + grid_id + ' input.dx-grid-input-check:checked').length);
        });
    };

    /**
     * Opens settings form for register
     *
     * @param {string} block_el HTML element id
     * @param {string} grid_id  Register grid HTML element id
     * @param {integer} list_id  Register id (from db table dx_lists)
     * @returns {undefined}
     */
    var handleRegisterSettings = function (block_el, grid_id, list_id) {
        block_el.find(".dx-register-tools a.dx-register-settings").click(function () {
            view_list_item("form", list_id, 3, 0, 0, grid_id, "");
        });
    };

     /**
     * Opens settings form for form
     *
     * @param {string} block_el HTML element id
     * @param {string} grid_id  Register grid HTML element id
     * @param {integer} form_id  Form id (from db table dx_forms)
     * @returns {undefined}
     */
    var handleFormSettings = function (block_el, grid_id, form_id) {
        block_el.find(".dx-register-tools a.dx-form-settings").click(function () {
            view_list_item("form", form_id, 10, 0, 0, grid_id, "");
        });
    };
    
     /**
     * Opens settings form for view
     *
     * @param {string} block_el HTML element id
     * @param {string} grid_id  Register grid HTML element id
     * @param {integer} view_id  View id (from db table dx_views)
     * @returns {undefined}
     */
    var handleViewSettings = function (block_el, grid_id, view_id) {
        block_el.find(".dx-register-tools a.dx-view-settings").click(function () {
            view_list_item("form", view_id, 6, 0, 0, grid_id, "");
        });
    };
    
    /**
     * Nodrošina tabulas rindas konteksta izvēlnes "Skatīt" funcionalitāti - atver ieraksta skatīšanās formu
     *
     * @param {object} grid_elem          Reģistra HTML elementa objekts
     * @param {string} grid_form          Formas URL (bez root daļas), kas piesaistīta reģistram ierakstu attēlošanai
     * @param {string} grid_id            Tabularā saraksta elementa HTML ID
     * @param {integer} list_id           Reģistra ID no datu bāzes tabulas dx_lists
     * @param {integer} rel_field_id      Saistītā ieraksta lauka ID no datu bāzes tabulas dx_lists_fields
     * @param {integer} rel_field_value   Saistīta ieraksta lauka vērtība
     * @param {string} form_htm_id        Formas, kuras sadaļā iekļauts tabulārais saraksts, HTML elementa ID
     * @returns {undefined}
     */
    var openItemByID = function (grid_elem, grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id) {
        if (open_item_id > 0) {
            return; // vienā lapā pieļaujams atvērt tikai 1 reģistra kartiņu
        }

        var item_id = parseInt(grid_elem.attr('dx_open_item_id'));

        if (!isNaN(item_id) && item_id > 0) {
            open_item_id = item_id;
            view_list_item(grid_form, item_id, list_id, rel_field_id, rel_field_value, grid_id, form_htm_id);
        }
    };

    /**
     * Recalculates grid height to set scrollbars
     *
     * @returns {undefined}
     */
    var initHeight = function () {
        try {
            var grid_el = $("#td_data .dx-grid-outer-div");
            var grid_top = grid_el.offset().top;
            var win_h = $(window).height();

            var scrl = 0;

            if (grid_el.hasScrollBar('horizontal')) {
                scrl = 8;
            }

            var adjust_h = 80;

            if ($("body").hasClass("dx-horizontal-menu-ui")) {
                adjust_h = 70;
            }

            var max_h = win_h - grid_top - adjust_h + scrl; //bija 100 / 70
            grid_el.css('max-height', max_h + 'px');

            var page_h = $("#td_data").offset().top;
            var page_min = win_h - page_h;
            $("#td_data").css('min-height', page_min + 'px');

            $(".dx-page-container").css('padding-bottom', '0px');
            $("#td_data .dx-paginator-butons").css('margin-right', 'auto');
        }
        catch (e) {
            console.log("Init Height error");
        }
    };
    
    /**
     * Add popup dropdown behavior to all items wich have dropdown logic
     * 
     * @param {object} el_block Grid HTML object
     * @returns {undefined}
     */
    var addHoverDropdowns = function(el_block) {
        el_block.find(".dropdown-toggle").dropdownHover();
    };

    /**
     * Show/hide popup on filtering button hovering
     * 
     * @param {type} el_block
     * @returns {undefined}
     */
    var addHoverFilters = function(el_block) {
        
        el_block.find("a.header-filter").click(function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            var el_popup = el_block.find('div.dx-dropdown-content');
            if (el_popup.is(':visible')) {
                el_popup.hide();
                is_filter_menu_in = 0;
            }
            else {
                el_popup.show();
            }
        });
        
        el_block.find("a.header-filter").hover(
            function() {
                var grid_id = el_block.attr("dx_grid_id");                    
                var menu = $("#grid_popup_" + grid_id);
                
                var offset = $(this).offset();
                var height = $(this).closest('thead').height();
                                
                var frame_offset = el_block.offset();
                
                menu.css('top', height + 'px');
                menu.css('left', offset.left - $(this).outerWidth() - frame_offset.left + 'px');
                
                var fld_name = $(this).closest('th').attr('fld_name');
                menu.attr('data-field', fld_name);
                menu.show();
            }, function() {
                var fld_name = $(this).closest('th').attr('fld_name');
                setTimeout(function(){
                    var grid_id = el_block.attr("dx_grid_id");                    
                    var menu = $("#grid_popup_" + grid_id);  
                    
                    if (!is_filter_menu_in && fld_name === menu.attr('data-field')) {
                        menu.hide();
                    }
                }, 500);
                
            }
        );

        el_block.find("div.dx-dropdown-content").hover(
            function() {
                is_filter_menu_in = 1;
            }, function() {
                is_filter_menu_in = 0;
                el_block.find('div.dx-dropdown-content').hide();
            }
        );
    };
    
    /**
     * Hanldes filtering menu clicking - make sorting
     * 
     * @param {object} el_block Grid's HTML element
     * @param {string} grid_id Grid ID
     * @param {string} tab_id Tab ID
     * @returns {undefined}
     */
    var handleFilteringOptions = function(el_block, grid_id, tab_id) {
        el_block.find('div.dx-dropdown-content a.dx-sort-asc').click(function() {
            var grid_id = el_block.attr("dx_grid_id");
            var menu = $("#grid_popup_" + grid_id); 
                    
            var field_name = menu.attr('data-field');
            
            $('#' + grid_id).data('sorting_field', field_name);
            $('#' + grid_id).data('sorting_direction', '1');
            
            reloadBlockGrid(grid_id, tab_id);
        });
        
        el_block.find('div.dx-dropdown-content a.dx-sort-desc').click(function() {
            var grid_id = el_block.attr("dx_grid_id");
            var menu = $("#grid_popup_" + grid_id); 
                    
            var field_name = menu.attr('data-field');
            
            $('#' + grid_id).data('sorting_field', field_name);
            $('#' + grid_id).data('sorting_direction', '2');
            
            reloadBlockGrid(grid_id, tab_id);
        });
        
        el_block.find('div.dx-dropdown-content a.dx-sort-none').click(function() { 
            
            $('#' + grid_id).data('sorting_field', '');
            $('#' + grid_id).data('sorting_direction', '0');
            
            reloadBlockGrid(grid_id, tab_id);
        });
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializētos skatu blokus
     * @returns {undefined}
     */
    var initViews = function () {
        root_url = getBaseUrl();

        $(".dx-block-container-view[dx_block_init='0']").each(function () {

            var grid_id = $(this).attr('dx_grid_id');
            var tab_id = $(this).attr('dx_tab_id');
            var menu_id = $(this).attr('dx_menu_id');
            var list_id = $(this).attr('dx_list_id');
            var rel_field_id = $(this).attr('dx_rel_field_id');
            var rel_field_value = $(this).attr('dx_rel_field_value');
            var form_htm_id = $(this).attr('dx_form_htm_id');
            var view_id = $(this).attr('dx_view_id');
            var grid_form = $(this).attr('dx_grid_form');

            // Augšējā rīkjosla ar pogām un skatu izkrītošo izvēlni
            handleView(menu_id, tab_id, list_id, rel_field_id, rel_field_value, form_htm_id);
            handleBtnNew(grid_id, menu_id, list_id, rel_field_id, rel_field_value, form_htm_id);
            handleBtnRefresh(menu_id, grid_id, tab_id);
            handleBtnPrepareReport(grid_id, tab_id, $(this));
            handleBtnExcel(menu_id, view_id, rel_field_id, rel_field_value, form_htm_id, grid_id);
            
            // Setting menu items handlers
            handleRegisterSettings($(this), grid_id, list_id); // grid settings
            handleFormSettings($(this), grid_id, $(this).attr('data-form-id')); // form settings
            handleViewSettings($(this), grid_id, view_id); // view settings
            
            handleBtnImport(menu_id, rel_field_id, rel_field_value, form_htm_id);
            handleBtnStartImport(menu_id);
            handleMenuFilter(menu_id, grid_id, tab_id, $(this));

            addHoverDropdowns($(this));
            addHoverFilters($(this));
            
            handleFilteringOptions($(this), grid_id, tab_id);
            
            // view editing
            handleBtnEditView($(this));
            handleBtnSaveView($(this));
            handleBtnCopy($(this));
            handleBtnDelete($(this));
            handleFieldOperation($(this));

            // Saraksta kolonnu funkcionalitāte
            handleFilter(grid_id, tab_id);
            handleSorting(grid_id, tab_id);

            // Saraksta lapošana
            handlePaginator(grid_id, tab_id, menu_id);

            // Saraksta rindas konteksta izvēlnes
            handleRowBtnView(grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id);
            handleRowBtnEdit(grid_form, grid_id, list_id, rel_field_id, rel_field_value);
            handleRowBtnDel(grid_id, list_id);

            // Iezīmēto rindu izvēlnes
            handleMarkBtnDel($(this), grid_id, list_id);
            handleMarkBtnCheck(grid_id, form_htm_id);
            handleMarkCounter(grid_id);

            openItemByID($(this), grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id);

            if (!tab_id) {
                $("body").addClass("dx-grid-in-page");
            }
            
            if(!dx_is_cssonly)
			{
				PageMain.addResizeCallback(initHeight);
	
				initHeight();
	
				var $table = $(this).find('table.dx-grid-table');
	
				$table.floatThead({
					scrollContainer: function($table)
					{
						return $table.closest('.dx-grid-outer-div');
					}
				});
	
				PageMain.addResizeCallback(function()
				{
					$table.floatThead('reflow');
				});
				setTimeout(function() { PageMain.resizePage(); }, 100);
			}
			
			else
			{
				var container = $('.dx-grid-inner-container');
				var divs = $('.dx-grid-table thead div');
				container.scroll(function()
				{
					divs.css({
						top: container.scrollTop()
					});
				});
			}
			
            $(this).attr('dx_block_init', 1); // uzstādam pazīmi, ka skata bloks ir inicializēts
        });
    };

    return {
        init: function () {
            initViews();
        },
        initHeight: function () {
            initHeight();
        }
    };
}();

// Overide default jQuery "contains" function to search case insensitive
$.expr[":"].contains = $.expr.createPseudo(function (arg) {
    return function (elem) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});

$.fn.hasScrollBar = function (direction) {
    if (direction == 'vertical') {
        return this.get(0).scrollHeight > this.innerHeight();
    }
    else if (direction == 'horizontal') {
        return this.get(0).scrollWidth > this.innerWidth();
    }
    return false;

};

$(function () {
    BlockViews.init();
});

$(document).ajaxComplete(function (event, xhr, settings) {
    BlockViews.init();
});
