/**
 * Tabulāro sarakstu JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var BlockViews = function()
{
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
     * Pārlādē bloka tabulārā saraksta datus.
     * Pārlādē vai nu sarakstu, kas ir galvenajā lapā vai arī formā iekļauto sadaļas sarakstu
     * 
     * @param {string} grid_id Tabularā saraksta elementa HTML ID
     * @param {string} tab_id  Formas sadaļas (TABa) HTML elementa ID   
     * @returns {undefined}
     */
    var reloadBlockGrid = function(grid_id, tab_id)
    {
        if (tab_id)
        {
            reload_tab_grid(grid_id);
        }
        else
        {
            reload_grid(grid_id);
        }
    };

    /**
     * Nodrošina ritjoslas noritināšanu līdz lejai pēc saraksta datu pārlādes
     * 
     * @param {string} form_htm_id Formas HTML elementa ID
     * @returns {undefined}
     */
    var scrollElement = function(form_htm_id)
    {
        var elem = null;
        if (form_htm_id)
        {
            elem = $('#' + form_htm_id).find(".modal-body");
        }
        else
        {
            elem = $(document);
        }
        
        setTimeout(function() {
            elem.scrollTop(scrollTop);
        }, 100);
            
        
    };
    
    /**
     * Nodrošina pašreizējo vertikālās ritjoslas pozīcijas atcerēšanos
     * 
     * @param {string} form_htm_id Formas HTML elementa ID
     * @returns {undefined}
     */
    var setScrollTop = function(form_htm_id)
    {
        var elem = null;
        if (form_htm_id)
        {
            elem = $('#' + form_htm_id).find(".modal-body");
        }
        else
        {
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
    var handlePaginator = function(grid_id, tab_id, menu_id)
    {
        $('#paginator_' + grid_id + ' .dx-paginator-butons button').on('click', function(event) {
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
    var handleView = function(menu_id, tab_id, list_id, rel_field_id, rel_field_value, form_htm_id)
    {
        $('#' + menu_id + '_viewcbo').change(function(event) {
            event.preventDefault();

            if (tab_id)
            {
                load_tab_grid(tab_id, list_id, $('#' + menu_id + '_viewcbo option:selected').val(), rel_field_id, rel_field_value, form_htm_id, 1, 5, 1);
            }
            else
            {
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
    var handleFilter = function(grid_id, tab_id)
    {
        $('#filter_' + grid_id + ' input').on('keypress', function(event) {

            if (event.which === 13)
            {
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
    var handleSorting = function(grid_id, tab_id)
    {
        $('#' + grid_id + ' th.t_header').on('click', function(event) {
            event.preventDefault();
            
            if ($('#' + grid_id).data('sorting_field') == $(this).attr('fld_name'))
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
                $('#' + grid_id).data('sorting_field', $(this).attr('fld_name'));
                $('#' + grid_id).data('sorting_direction', '1'); // asc
            }
            
            reloadBlockGrid(grid_id, tab_id);
        });
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
    var handleBtnNew = function(grid_id, menu_id, list_id, rel_field_id, rel_field_value, form_htm_id)
    {
        $('#' + menu_id + '_new').button().click(function(event) {
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
    var handleBtnRefresh = function(menu_id, grid_id, tab_id)
    {
        $('#' + menu_id + '_refresh').button().click(function(event) {
            event.preventDefault();
            reloadBlockGrid(grid_id, tab_id);
        });
    };

    /**
     * Nodrošina tabulārā saraksta eksportu uz Excel lejuplādējamu datni uz pogas "Uz Excel" nospiešanu
     * 
     * @param {string} menu_id            Tabulārā saraksta augšējās rīkjoslas izvēlnes HTML elementa ID
     * @param {integer} view_id           Reģistra skata ID no tabulas dx_views
     * @param {integer} rel_field_id      Saistītā ieraksta lauka ID no datu bāzes tabulas dx_lists_fields
     * @param {integer} rel_field_value   Saistīta ieraksta lauka vērtība
     
     * @returns {undefined}
     */
    var handleBtnExcel = function(menu_id, view_id, rel_field_id, rel_field_value)
    {
        $('#' + menu_id + '_excel').button().click(function(event) {
            event.preventDefault();
            download_excel(view_id, rel_field_id, rel_field_value);
        });
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
    var handleRowBtnView = function(grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id)
    {
        $('#' + grid_id + ' a.dx-grid-cmd-view').click(function(event) {
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
    var handleRowBtnEdit = function(grid_form, grid_id, list_id, rel_field_id, rel_field_value)
    {
        $('#' + grid_id + ' a.dx-grid-cmd-edit').click(function(event) {
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
    var handleRowBtnDel = function(grid_id, list_id)
    {
        $('#' + grid_id + ' a.dx-grid-cmd-delete').click(function(event) {
            event.preventDefault();
            if (!confirm(DX_CORE.trans_confirm_delete))
            {
                return;
            }
            delete_multiple_items(list_id, grid_id, $(this).attr('dx_item_id'), 1);
        });
    };

    /**
     * Nodrošina iezīmēto tabulas rindu dzēšanu
     * 
     * @param {string} grid_id  Tabularā saraksta elementa HTML ID
     * @param {integer} list_id Reģistra ID no datu bāzes tabulas dx_lists
     * @returns {undefined}
     */
    var handleMarkBtnDel = function(grid_id, list_id)
    {
        $('#paginator_' + grid_id + ' a.dx-grid-cmd-delall').click(function(event) {
            event.preventDefault();

            var items = "";
            var cnt = 0;
            $('#' + grid_id + ' input.dx-grid-input-check:checked').each(function() {
                if (items.length > 0)
                {
                    items = items + "|";
                }
                items = items + $(this).attr('dx_item_id');
                cnt++;
            });

            if (cnt === 0)
            {
                notify_err("Iezīmējiet vismaz vienu ierakstu, kuru dzēst!");
                return;
            }

            var msg = 'Vai tiešām dzēst iezīmēto ierakstu?';

            if (cnt > 1)
            {
                msg = 'Vai tiešām dzēst ' + cnt + ' iezīmētos ierakstus?';
            }

            if (!confirm(msg))
            {
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
    var handleMarkBtnCheck = function(grid_id, form_htm_id)
    {
        $('#paginator_' + grid_id + ' a.dx-grid-cmd-markall').click(function(e) {        
        
            setScrollTop(form_htm_id);
            
            var cnt = 0;
            $('#' + grid_id + ' input.dx-grid-input-check').each(function() {
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
    var handleMarkCounter = function(grid_id)
    {
        $('#' + grid_id + ' input.dx-grid-input-check').change(function() {
            $('#paginator_' + grid_id + ' span.dx-marked-count-lbl').text($('#' + grid_id + ' input.dx-grid-input-check:checked').length);
        });
    };

    /**
     * Nodrošina reģistra iestatījumu formas atvēršanu
     * 
     * @param {string} grid_id Reģistra ID
     * @returns {undefined}
     */
    var handleRegisterSettings = function(block_el, grid_id, list_id) {        
        block_el.find(".dx-register-tools a.dx-register-settings").click(function() {            
            view_list_item("form", list_id, 3, 0, 0, grid_id, "");
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
    var openItemByID = function(grid_elem, grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id)
    {
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
     * Apstrādā un inicializē vēl neinicializētos skatu blokus
     * @returns {undefined}
     */
    var initViews = function()
    {        
        root_url = getBaseUrl();
        
        $(".dx-block-container-view[dx_block_init='0']").each(function() {
            
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
            handleBtnExcel(menu_id, view_id, rel_field_id, rel_field_value);
            handleRegisterSettings($(this), grid_id, list_id);
            
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
            handleMarkBtnDel(grid_id, list_id);
            handleMarkBtnCheck(grid_id, form_htm_id);
            handleMarkCounter(grid_id);
            
            openItemByID($(this), grid_form, grid_id, list_id, rel_field_id, rel_field_value, form_htm_id);
            
            $(this).attr('dx_block_init', 1); // uzstādam pazīmi, ka skata bloks ir inicializēts
        });
    };

    return {
        init: function() {
            initViews();
        }
    };
}();

$(function() {        
    BlockViews.init();    
});

$(document).ajaxComplete(function(event, xhr, settings) {            
    BlockViews.init();           
});