/**
 * Contains logic for viewing and editing employee's notes
 * @type Window.DxEmpNotes|window.DxEmpNotes 
 */
window.DxEmpNotes = window.DxEmpNotes || {
    /**
     * User ID which is loaded
     */
    userId: 0,
    /**
     * Parameter if control is loaded
     */
    isLoaded: false,
    /**
     * Parameter if note is sending to server
     */
    isSending: false,
    /**
     * Default color for chat form background
     */
    chatFormColorDefault: 'white',
    /**
     * Initializes component
     */
    init: function (userId) {
        window.DxEmpNotes.userId = userId;

    },
    /**
     * Loads view
     * @returns {undefined}
     */
    loadView: function () {
        if (window.DxEmpNotes.isLoaded) {
            return;
        }

        show_page_splash(1);

        $.ajax({
            url: DX_CORE.site_url + 'employee/notes/get/view/' + window.DxEmpNotes.userId,
            type: "get",
            success: window.DxEmpNotes.onLoadViewSuccess,
            error: function (data) {
                hide_page_splash(1);
            }
        });
    },
    /**
     * Evnet handler when view is successfully loaded
     * @returns {string} View's HTML
     */
    onLoadViewSuccess: function (data) {
        $('#dx-tab_notes').html(data);

        window.DxEmpNotes.chatFormColorDefault = $('.dx-emp-notes-chat-form').css("background-color");

        $('.dx-emp-notes-btn').click(window.DxEmpNotes.onNoteEnter);
        $('.dx-emp-notes-input-text').keyup(function (e) {
            if (e.keyCode == 13) {
                window.DxEmpNotes.onNoteEnter();
            }
        });

        $('.dx-emp-notes-chat').on('click', '.dx-emp-notes-btn-link-edit', {}, window.DxEmpNotes.onEditClick);
        $('.dx-emp-notes-chat').on('click', '.dx-emp-notes-btn-link-delete', {}, window.DxEmpNotes.onDeleteClick);
        
        $('.dx-emp-notes-btn-whosee').popover();

        window.DxEmpNotes.isLoaded = true;

        hide_page_splash(1);
    },
    /**
     * Retrieve data for saving
     * @returns {object} Prepared data
     */
    getDataForSave: function () {
        var data = {};

        data.note_id = $('.dx-emp-notes-input-id').val();
        data.user_id = window.DxEmpNotes.userId;
        data.note_text = $('.dx-emp-notes-input-text').val();

        return data;
    },
    /**
     * Event handler when note saving is initiated
     * @returns {undefined}
     */
    onNoteEnter: function () {
        if (window.DxEmpNotes.isSending) {
            return;
        }

        window.DxEmpNotes.showLoading();

        var data = window.DxEmpNotes.getDataForSave();
        $.ajax({
            url: DX_CORE.site_url + 'employee/notes/save',
            data: data,
            type: "post",
            success: window.DxEmpNotes.onSuccessSave,
            error: window.DxEmpNotes.onAjaxError
        });
    },
    /**
     * Load selected note's data into note input boxes
     * @param {object} e Evenet caller
     * @returns {undefined}
     */
    onEditClick: function (e) {
        var edit_btn = $(e.target);

        var note_id = edit_btn.closest('.message').find('.dx-emp-notes-edit-id').val();
        var note_text = edit_btn.closest('.message').find('.dx-emp-notes-edit-body').html();

        $('.dx-emp-notes-input-id').val(note_id);
        $('.dx-emp-notes-input-text').val(note_text);
        $('.dx-emp-notes-input-text').focus();

        // Animate 
        var chat_form = $('.dx-emp-notes-chat-form');

        chat_form.animate({backgroundColor: '#7bb6de'}, 'slow', function () {
            chat_form.animate({backgroundColor: window.DxEmpNotes.chatFormColorDefault}, 'slow');
        });

    },
    /**
     * Event handler for delete click. Opens modal confirmation window
     * @param {object} e Event arguments
     * @returns {undefined}
     */
    onDeleteClick: function (e) {
        var del_btn = $(e.target);

        var note_id = del_btn.closest('.message').find('.dx-emp-notes-edit-id').val();

        PageMain.showConfirm(window.DxEmpNotes.onDeleteConfirm,
                note_id,
                Lang.get('empl_profile.notes.delete_note_title'),
                Lang.get('empl_profile.notes.delete_note_text'),
                Lang.get('form.btn_delete'),
                '');
    },
    /**
     * Event handler when delete operation is confirmed
     * @param {integer} id Note's ID which will be deleted
     * @returns {undefined}
     */
    onDeleteConfirm: function (id) {
        if (window.DxEmpNotes.isSending) {
            return;
        }

        window.DxEmpNotes.showLoading();
        
         var data = {
             note_id: id
         };
        
        $.ajax({
            url: DX_CORE.site_url + 'employee/notes/delete',
            data: data,
            type: "delete",
            success: window.DxEmpNotes.onSuccessDelete,
            error: window.DxEmpNotes.onAjaxError
        });
    },
    /**
     * Shows loading box
     * @returns {undefined}
     */
    showLoading: function () {
        window.DxEmpNotes.isSending = true;
        show_page_splash(1);
    },
    /**
     * Hides loading box
     * @returns {undefined}
     */
    hideLoading: function () {
        window.DxEmpNotes.isSending = false;
        hide_page_splash(1);
    },
    /**
     * Event on successful note delete
     * @param {integer} note_id Note id which was deleted
     */
    onSuccessDelete: function (note_id) {
        if (note_id) {
            $('.dx-emp-notes-edit-id[value=' + note_id + ']').closest('li').remove();
        }

        window.DxEmpNotes.hideLoading();
    },
    /**
     * Event on successful data save
     * @param {array} data Data returned about saved noted. Contains view for new note
     */
    onSuccessSave: function (data) {
        // Removes old noted if existed, because it will be moved to top of the list as latest note
        var note_id = $('.dx-emp-notes-input-id').val();
        if (note_id) {
            $('.dx-emp-notes-edit-id[value=' + note_id + ']').closest('li').remove();
        }
        $('.dx-emp-notes-input-id').val('');
        $('.dx-emp-notes-input-text').val('');

        window.DxEmpNotes.hideLoading();

        if (data.view) {
            $('.dx-emp-notes-chat').prepend($(data.view).fadeIn());
        }
    },
    /**
     * Event when ajax request gets error
     * @param {array} data Data containing error information
     */
    onAjaxError: function (data) {
        window.DxEmpNotes.hideLoading();
    }
};