var DX_CORE = {
    site_url: "",
    site_public_url: "",
    progress_gif_url: "",
    exec_guids: [],
    last_execs: [],
    last_times: [],
    forms_ids: [],
    forms_guids: [],
    forms_callbacks: [],
    items_ids: [],
    form_height_ratio: 0.9,
    valid_elements: "",
    valid_styles: "",
    max_upload_size: 2,
    post_max_size: 4,
    trans_data_processing: "Datu apstrāde... Lūdzu, uzgaidiet...",
    trans_please_wait: "Lūdzu, uzgaidiet...",
    trans_sys_error: "Tīkla vai servera kļūda! Pārbaudiet interneta savienojumu un/vai mēģiniet atkārtot darbību vēlāk.",
    trans_session_end: "Lietotāja sesija ir beigusies. Nepieciešams autorizēties atkārtoti.",
    trans_general_error: "Sistēmas kļūda - nav iespējams ielādēt datus!",
    trans_first_save_msg: "Vispirms saglabājiet formu un tad veiciet saistīto datu ievadi!",
    trans_data_saved: "Dati veiksmīgi saglabāti!",
    trans_data_deleted: "Ieraksts veiksmīgi dzēsts!",
    trans_data_deleted_all: "Visi iezīmētie ieraksti veiksmīgi dzēsti!",
    trans_word_generating: "Ģenerē Word datni. Lūdzu, uzgaidiet...",
    trans_word_generated: "Word datne veiksmīgi noģenerēta!",
    trans_excel_downloaded: "Excel datne veiksmīgi lejupielādēta!",
    trans_file_downloaded: "Datne veiksmīgi lejupielādēta!",
    trans_file_error: "Pievienotās datnes '%n' izmērs ir %s MB un tas pārsniedz pieļaujamo limitu %u MB! Datus nav iespējams saglabāt.",
    trans_confirm_delete: "Vai tiešām dzēst ierakstu?",
    trans_page_fullscreen: "Lapa pa visu ekrānu",
    trans_page_boxed: "Rādīt lapu rāmī",
    //tree - chosing item
    trans_tree_close: "Aizvērt",
    trans_tree_chosen: "Izvēlētā vērtība:",
    trans_tree_cancel: "Atcelt",
    trans_tree_choose: "Izvēlēties"


};

function getProgressInfo() {
    return "<img src='" + DX_CORE.progress_gif_url + "' alt='" + DX_CORE.trans_data_processing + "' title='" + DX_CORE.trans_data_processing + "' /> <span style='font-size: 12px; color: silver;'>" + DX_CORE.trans_data_processing + "</span>";
}

function register_form(form_htm_id, item_id) {
    var index = DX_CORE.forms_ids.indexOf(form_htm_id);

    if (index > -1) {
        console.log("Forma jau ir reģistrēta JavaScript masīvā: " + form_htm_id);
        return false;
    }

    DX_CORE.forms_ids.push(form_htm_id);
    DX_CORE.items_ids.push(item_id);

    return true;
}

function unregister_form(form_htm_id) {
    var index = DX_CORE.forms_ids.indexOf(form_htm_id);

    if (index > -1) {
        DX_CORE.forms_ids.splice(index, 1); // remove element
        DX_CORE.items_ids.splice(index, 1); // remove element
        return true;
    }

    console.log("Forma nav reģistrēta JavaScript masīvā: " + form_htm_id);
    return false;
}

function get_previous_form_by_list(current_form_htm_id, previous_item_id) {
    var index = DX_CORE.forms_ids.indexOf(current_form_htm_id);

    if (index > -1) {
        for (var i = index; i >= 0; i--) {
            if (DX_CORE.items_ids[i] == previous_item_id) {
                return DX_CORE.forms_ids[i];
            }
        }
        console.log("Norādītais reģistrs nav JavaScript masīvā: " + previous_item_id);
        return;
    }

    console.log("Forma nav reģistrēta JavaScript masīvā: " + current_form_htm_id);
    return null;
}

function get_last_form_id()
{
    if (DX_CORE.forms_ids.length == 0)
    {
        return "";
    }

    return DX_CORE.forms_ids[DX_CORE.forms_ids.length - 1];
}

function is_executing(guid) {

    if (guid.length == 0) {
        return false;
    }

    var index = DX_CORE.exec_guids.indexOf(guid);

    if (index > -1) {
        return true;
    }

    var index2 = DX_CORE.last_execs.indexOf(guid);
    if (index2 > -1) {
        var dat_now = new Date();
        var dat_prev = DX_CORE.last_times[index2];
        var dif = dat_now.getTime() - dat_prev.getTime();
        if (dif < 200) {
            DX_CORE.last_times[index2] = dat_now;

            return true; // its needed to wait at least 200 milisec between clicks
        }
    }

    return false;
}

function add_form_callbacks(form_guid, arr_callbacks) {
    if (form_guid.length == 0) {
        return;
    }

    var index = DX_CORE.forms_guids.indexOf(form_guid);

    if (index == -1) {
        DX_CORE.forms_guids.push(form_guid);
        DX_CORE.forms_callbacks.push(arr_callbacks);
        return;
    }

    DX_CORE.forms_callbacks[index] = arr_callbacks;
}

function get_form_callbacks(form_guid) {
    if (form_guid.length == 0) {
        return;
    }

    var index = DX_CORE.forms_guids.indexOf(form_guid);

    if (index == -1) {
        return;
    }

    return DX_CORE.forms_callbacks[index];
}

function start_executing(guid) {
    if (guid.length > 0) {
        DX_CORE.exec_guids.push(guid);
    }
}

function stop_executing(guid) {
    if (guid.length == 0) {
        return;
    }

    var index = DX_CORE.exec_guids.indexOf(guid);

    if (index > -1) {
        var index2 = DX_CORE.last_execs.indexOf(guid);
        if (index2 == -1) {
            DX_CORE.last_execs.push(guid);
            DX_CORE.last_times.push(new Date());
        } else {
            DX_CORE.last_times[index2] = new Date();
        }
        DX_CORE.exec_guids.splice(index, 1); // remove element
    }
}

function stop_executing_forced(guid) {
    if (guid.length == 0) {
        return;
    }

    var index = DX_CORE.exec_guids.indexOf(guid);

    if (index > -1) {
        var index2 = DX_CORE.last_execs.indexOf(guid);
        var dat = new Date();
        dat.setDate(dat.getDate() - 1);

        if (index2 == -1) {
            DX_CORE.last_execs.push(guid);

            DX_CORE.last_times.push(dat);
        } else {
            DX_CORE.last_times[index2] = dat;
        }
        DX_CORE.exec_guids.splice(index, 1);
    }
}

/**
 * Parāda progresa ziņojumu
 * 
 * @param string txt    Progresa ziņojuma teksts
 * @returns {undefined}
 */
function show_dx_progres(txt)
{
    show_page_splash();
}

/**
 * Paslēpj progresa ziņojumu
 * 
 * @returns void
 */
function hide_dx_progres()
{
    hide_page_splash();
}

function notify_info(txt) {
    toastr.success(escapeHtml(txt));
}

function notify_err(txt) {
    toastr.error(escapeHtml(txt));
}

function authorize_user(login, password) {
    var open_url = DX_CORE.site_url + "authorize_user.php?login=" + login + "&password=" + password + "&sc_width=" + screen.width + "&sc_height=" + (screen.height - 100);
    var rez = "";

    $.ajax({
        url: open_url,
        async: false,
        dataType: "html",
        success: function (data) {
            rez = data;
        }
    });

    return rez;

}

function load_view_in_grid(list_id, view_id) {
    var open_url = DX_CORE.site_url + "list_grid_with_menu.php?list_id=" + list_id + "&view_id=" + view_id;

    var menu_title = $("#grid_title").html();

    $("#td_data").html("");
    $("#grid_title").html(getProgressInfo());

    var td_height = 300;
    if ($("#td_content")) {
        td_height = $("#td_content").height() - 200;
    }

    $.ajax({
        url: open_url,
        dataType: "html",
        data: {
            gridheigh: td_height
        },
        success: function (data) {
            if (data == '[ERR_SESSION_ENDED]') {
                $("#grid_title").html("");
                notify_err(DX_CORE.trans_session_end);
            } else {
                $("#td_data").html(data);
                $("#grid_title").html(menu_title);
            }
        },
        error: function () {
            $("#grid_title").html("");
        }
    });
}

function menu_click(list_id, url, full_path, is_target_blank, menu_title) {
    if (list_id == 0) {
        if (url == "") {
            notify_err("Saitei nav piesaistīts neviens objekts vai vietne!");
            return;
        }

        alert("Under construction");
        return;
    }

    load_grid("td_data", list_id, 0);
}

function get_popup_item_by_id(item_id, item_url, item_title) {
    $('#popup_window .modal-header h4').html(item_title);

    $("#popup_body").html(getProgressInfo());
    $('#popup_window').modal('show');

    var formData = "item_id=" + item_id;

    var request = new FormAjaxRequestIE9(item_url, "", "", formData);
    request.progress_info = "";

    request.callback = function (data) {

        try
        {
            var myData = data;
            if (myData['success'] == 1) {
                $('#popup_body').html(myData['html']);
            } else {
                $('#popup_window').modal('hide');
                notify_err(myData['error']);
            }
        } catch (err)
        {
            $('#popup_window').modal('hide');
            notify_err(escapeHtml(err));
        }

    };

    // izpildam AJAX pieprasījumu
    request.doRequest();
}

function get_popup_item_by_id_ie9(item_id, item_url, item_title, callback_fn) {
    $('#popup_window .modal-header h4').html(item_title);

    $("#popup_body").html(getProgressInfo());
    $('#popup_window').modal('show');

    var formData = "item_id=" + item_id;

    var request = new FormAjaxRequestIE9(item_url, "", "", formData);
    request.progress_info = "";

    request.callback = function (data) {

        try
        {
            var myData = data;
            if (myData['success'] == 1) {
                $('#popup_body').html(myData['html']);
                callback_fn();
            } else {
                $('#popup_window').modal('hide');
                notify_err(myData['error']);
            }
        } catch (err)
        {
            $('#popup_window').modal('hide');
            notify_err(escapeHtml(err));
        }

    };

    // izpildam AJAX pieprasījumu
    request.doRequest();
}

function download_file(item_id, list_id, file_field_id) {

    show_form_splash();
    show_page_splash();
    var open_url = DX_CORE.site_url + "download_filejs_" + item_id + "_" + list_id + "_" + file_field_id;

    $.fileDownload(open_url, {
        successCallback: function (url) {
            hide_form_splash();
            hide_page_splash();
            notify_info(DX_CORE.trans_file_downloaded);
        },
        failCallback: function (html, url) {
            hide_form_splash();
            hide_page_splash();
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
}

function escapeHtml(string) {
    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
    };

    return String(string).replace(/[&<>"'\/]/g, function (s) {
        return entityMap[s];
    });
}

function download_excel(view_id, rel_field_id, rel_field_value, grid_id) {
    show_page_splash();
    var open_url = DX_CORE.site_url + "excel"; // _" + view_id + "_" + rel_field_id + "_" + rel_field_value;

    var formData = "view_id=" + view_id + "&rel_field_id=" + rel_field_id + "&rel_field_value=" + rel_field_value + "&_token=" + $('meta[name="csrf-token"]').attr('content') + "&grid_id=" + grid_id;

    $.fileDownload(open_url, {
        httpMethod: 'POST',
        data: formData,
        successCallback: function (url) {
            hide_page_splash();
            notify_info(DX_CORE.trans_excel_downloaded);
        },
        failCallback: function (html, url) {
            hide_page_splash();
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
}

/**
 * Pārbauda, vai Interneta pārlūks ir MS Internet Explorer
 * 
 * @return   bool   True - IE, False - cits pārlūks
 */
function is_IE() {

    var ua = window.navigator.userAgent;
    var msie = ua.indexOf("MSIE ");

    if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
        return true;
    }

    return false;
}

var is_splash_lock = 0;

/**
 * Parāda Metronic progresa paziņojumu - visa lapa tiek atspējota
 * 
 * @return   void
 */
function show_page_splash(is_lock)
{
    // If modal is opened then redirect to form splash function
   /* var modals = $('.modal.in');
    if (modals.length > 0) {
        for (var i = 0; i < modals.length; i++) {
            if ($(modals[i]).is(':visible')) {
                show_form_splash(is_lock);
                return;
            }
        }
    }*/

    console.log('show_page_splash');

    if (is_splash_lock == 1) {
        return;
    }

    if (is_lock == 1) {
        is_splash_lock = 1;
    }

    if (App) {
        App.blockUI({message: DX_CORE.trans_please_wait, boxed: true, zIndex: 15000000, target: 'body'});
    }
}


/**
 * Noņem Metronic progresa paziņojumu - visa lapa kļūst pieejama
 * 
 * @return   void
 */
function hide_page_splash(is_unlock)
{
    // If modal is opened then redirect to form splash function    
   /* var modals = $('.modal.in');
    if (modals.length > 0) {
        for (var i = 0; i < modals.length; i++) {
            if ($(modals[i]).is(':visible')) {
                hide_form_splash(is_unlock);
                return;
            }
        }
    }*/

    console.log('hide_page_splash');

    if (is_splash_lock == 1 && is_unlock != 1) {
        return;
    }

    if (is_unlock == 1) {
        is_splash_lock = 0;
    }

    if (App) {
        App.unblockUI('body');
    }
}

/**
 * Parāda Metronic progresa paziņojumu - forma tiek atspējota
 * 
 * @return   void
 */
function show_form_splash(is_lock)
{
    console.log('show_form_splash1');
    
    show_page_splash(is_lock);

    /* if (is_splash_lock == 1) {
        return;
    }

    if (is_lock == 1) {
        is_splash_lock = 1;
    }

    if (App) {
        App.blockUI({
            target: '.modal-content',
            boxed: true,
            message: DX_CORE.trans_please_wait,
            cenrerY: true
        });
    }*/
}

/**
 * Noņem Metronic progresa paziņojumu - forma kļūst pieejama
 * 
 * @return   void
 */
function hide_form_splash(is_unlock)
{
    console.log('hide_form_splash1');

    hide_page_splash(is_unlock);

    /* if (is_splash_lock == 1 && is_unlock != 1) {
        return;
    }

    if (is_unlock == 1) {
        is_splash_lock = 0;
    }

    if (App) {
        App.unblockUI('.modal-content');
    }*/
}

/*
 * AJAX pieprasījumu izpildīšanas objekts. Objektam var konfigurēt arī callback funkciju
 * 
 * @param   url             string    URL darbplūsmas iznicializēšanai
 * @param   form_htm_id     string    Formas HTML objekta identifikators
 * @param   grid_htm_id     string    Reģistra tabulārā skata HTML objekta identifikators - nepieciešams lai atjauninātu saraksta datus
 * @param   formData        Array     Masīvs ar formas POST datiem
 * @return  void
 */
function FormAjaxRequest(url, form_htm_id, grid_htm_id, formData) {
    this.url = url;
    this.form_htm_id = form_htm_id;
    this.grid_htm_id = grid_htm_id;
    this.formData = formData;

    this.progress_info = DX_CORE.trans_data_processing;
    this.callback = null;
    this.err_callback = null;

    this.doRequest = function () {

        if (this.form_htm_id)
        {
            if (is_executing(this.form_htm_id))
            {
                return;
            }

            start_executing(this.form_htm_id);
        }

        // vēlreiz jādefinē, lai šie mainīgie ir pieejami ajax funkcijās, jo tajās "this" ir jau cits objekts
        var callback = this.callback;
        var form_htm_id = this.form_htm_id;
        var progress_info = this.progress_info;
        var err_callback = this.err_callback;

        $.ajax({
            type: 'POST',
            url: DX_CORE.site_url + this.url,
            data: this.formData,
            processData: false,
            contentType: false,
            dataType: "json",
            async: true,
            success: function (data) {
                try
                {
                    if (data["success"] === 1)
                    {
                        callback.call(this, data);
                        hide_page_splash();
                        hide_form_splash();
                    } else
                    {
                        notify_err(data["error"]);
                        hide_page_splash(1);
                        hide_form_splash(1);

                        if (err_callback)
                        {
                            err_callback.call(this, data["error"]);
                        }
                    }
                } catch (err)
                {
                    notify_err(escapeHtml(err));
                    hide_page_splash(1);
                    hide_form_splash(1);
                    if (err_callback)
                    {
                        err_callback.call(this, escapeHtml(err));
                    }
                }

                if (form_htm_id)
                {
                    stop_executing(form_htm_id);
                }

            },
            beforeSend: function () {
                if (progress_info) {
                    show_page_splash();
                    show_form_splash();
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                var err_txt = PageMain.getAjaxErrTxt(jqXHR);
                if (err_callback)
                {
                    err_callback.call(this, err_txt);
                }
                console.log("AJAX kļūda: " + errorThrown);
                if (form_htm_id)
                {
                    stop_executing(form_htm_id);
                }
            }
        });
    };
}

/*
 * AJAX pieprasījumu izpildīšanas objekts (IE9 atbalsts). Objektam var konfigurēt arī callback funkciju
 * Funkciju paredzēts izsaukt no publiskā portāla nevis no SVS
 * Atsķirība - šai funkcijai nevar padot FormData, bet gan tikai tekstu, kurā ar & atdalīti parametri, piemēram, p1=vertiba1&p2=vertiba2
 * 
 * @param   url             string    URL darbplūsmas iznicializēšanai
 * @param   form_htm_id     string    Formas HTML objekta identifikators
 * @param   grid_htm_id     string    Reģistra tabulārā skata HTML objekta identifikators - nepieciešams lai atjauninātu saraksta datus
 * @param   formData        string    Ar & atdalīti parametri un to vērtības teksta veidā
 * @return  void
 */
function FormAjaxRequestIE9(url, form_htm_id, grid_htm_id, formData) {
    this.url = url;
    this.form_htm_id = form_htm_id;
    this.grid_htm_id = grid_htm_id;
    this.formData = formData;

    this.progress_info = DX_CORE.trans_data_processing;
    this.callback = null;
    this.err_callback = null;

    this.doRequest = function () {

        if (this.form_htm_id)
        {
            if (is_executing(this.form_htm_id))
            {
                return;
            }

            start_executing(this.form_htm_id);
        }

        // vēlreiz jādefinē, lai šie mainīgie ir pieejami ajax funkcijās, jo tajās "this" ir jau cits objekts
        var callback = this.callback;
        var form_htm_id = this.form_htm_id;
        var progress_info = this.progress_info;
        var err_callback = this.err_callback;

        $.ajax({
            type: 'POST',
            url: DX_CORE.site_url + this.url,
            data: this.formData,
            async: true,
            success: function (data) {
                try
                {
                    if (data["success"] === 1)
                    {
                        callback.call(this, data);
                    } else
                    {
                        notify_err(data["error"]);
                        if (err_callback)
                        {
                            err_callback.call(this, data["error"]);
                        }
                    }
                } catch (err)
                {
                    notify_err(escapeHtml(err));
                    if (err_callback)
                    {
                        err_callback.call(this, escapeHtml(err));
                    }
                }

                if (form_htm_id)
                {
                    stop_executing(form_htm_id);
                }
            },
            beforeSend: function () {
                if (progress_info != "") {
                    show_dx_progres(progress_info);
                    show_form_splash();
                }
            },
            complete: function () {
                if (progress_info != "") {
                    hide_dx_progres();
                    hide_form_splash();
                }
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                var err_txt = PageMain.getAjaxErrTxt(jqXHR);
                if (err_callback)
                {
                    err_callback.call(this, err_txt);
                }
                console.log("AJAX kļūda: " + errorThrown);
                if (form_htm_id)
                {
                    stop_executing(form_htm_id);
                }
            }
        });
    };
}

/**
 * Izgūst vietnes domēna URL daļu (beidzas ar /)
 * 
 * @returns {string}
 */
function getBaseUrl() {

    return DX_CORE.site_url;
}
;

/**
 * Atgriež elementa vērtību. Ja elements nav atrasts, tad noklusēto vērtību
 * 
 * @param {Object} parent_elem Vecākais elements, kurā tiks veikta meklēšana
 * @param {string} selector    Meklējamais elements
 * @param {mixed} default_val  Noklusētā vērtība
 * @returns {mixed}
 */
function getElementVal(parent_elem, selector, default_val) {
    var elem = parent_elem.find(selector);

    if (!elem.length)
    {
        return default_val;
    }

    return elem.val();
}
;

var debug_first_log = 0;
function debug_log(txt) {
    var d = new Date();
    var n = d.getTime();

    if (debug_first_log == 0) {
        debug_first_log = n;
    }

    var tm = n - debug_first_log;
    console.log("Miliseconds: " + tm + ": " + txt);
}