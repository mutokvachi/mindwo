(function ($) {
    /**
     * Creates jQuery plugin for form chat
     * @returns DxFormChat
     */
    $.fn.DxFormChat = function () {
        return this.each(function () {
            if ($(this).data('dx_is_init') == 1) {
                return;
            }

            new $.DxFormChat($(this));
        });
    };

    /**
     * Class for form chat
     * @type DxCryptoField 
     */
    $.DxFormChat = function (domObject) {
        /**
         * Field's DOM object which is related to this class
         */
        this.domObject = domObject;

        /**
         * Chat DOM object
         */
        this.chatObject;

        /**
         * Chat's content (messages) DOM object
         */
        this.chatContentObject;

        /**
         * Last message's ID pulled from server
         */
        this.lastMessageID = 0;

        /**
         * List ID
         */
        this.listId = 0;

        /**
         * Item ID
         */
        this.itemId = 0;

        /** 
         * Form's title
         */
        this.formTitle = '';

        /**
         * State of chat window if it is visible
         */
        this.stateIsVisible = false;

        /**
         * State if update is running - post operation is in process
         */
        this.stateIsUpdateRunning = false;

        // Initializes class
        this.init();
    };

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxFormChat.prototype, {
        /**
         * Initializes field
         * @returns {undefined}
         */
        init: function () {
            var self = this;

            self.domObject.data('dx_is_init', 1);

            // Saves parameters
            self.listId = self.domObject.data('dx-list-id');
            self.itemId = self.domObject.data('dx-item-id');
            self.formTitle = self.domObject.data('dx-form-title');

            // Retrieve global chat window
            self.chatObject = $('.dx-form-chat-panel');

            self.chatContentObject = self.chatObject.find('.dx-form-chat-content');

            // Opens chat window
            self.domObject.click(function () {
                self.onOpenChatClick(self);
            });

            // Handle chat close when modal has been closed
            self.domObject.on("remove", function () {
                self.closeChatPanel(self);
            });

            // Handles chat close button
            self.chatObject.find('.dx-form-chat-btn-close').off('click');
            self.chatObject.find('.dx-form-chat-btn-close').click(function () {
                self.closeChatPanel(self);
            });

            // Message send button click
            self.chatObject.find('.dx-form-chat-btn-send').off('click');
            self.chatObject.find('.dx-form-chat-btn-send').click(function () {
                self.onMessageEnter(self);
            });

            // Auto-upload file when it is specified
            self.chatObject.find('.dx-form-chat-file-input').off('change');
            self.chatObject.find('.dx-form-chat-file-input').change(function () {
                self.onFileSelected(self);
            });

            // Send attachments
            self.chatObject.find('.dx-form-chat-btn-file').off('click');
            self.chatObject.find('.dx-form-chat-btn-file').click(function () {
                self.onClickAddFile(self);
            });

            // Opens modal which shows chat's users 
            self.chatObject.find('.dx-form-chat-btn-users').off('click');
            self.chatObject.find('.dx-form-chat-btn-users').click(function () {
                self.openChatUsersModal(self);
            });

            // Opens modal which allows to add user to chat
            self.chatObject.find('.dx-form-chat-btn-add-user').off('click');
            self.chatObject.find('.dx-form-chat-btn-add-user').click(function () {
                self.openAddChatUsersModal(self);
            });

            // On input key down if enter clicked without shift then we will send message on relase
            self.chatObject.find('.dx-form-chat-input-text').off('keydown');
            self.chatObject.find('.dx-form-chat-input-text').keydown(function (e) {
                if (e.keyCode == 13 && !e.shiftKey) {
                    e.preventDefault();
                }
            });

            // On input key op if enter clicked without shift then we send message
            self.chatObject.find('.dx-form-chat-input-text').off('keyup');
            self.chatObject.find('.dx-form-chat-input-text').keyup(function (e) {
                if (e.keyCode == 13 && !e.shiftKey) {
                    e.preventDefault();
                    self.onMessageEnter(self);
                }
            });

            // Retrieves chat messages and opens chat if messages found
            self.getChatData();
        },
        onClickAddFile: function (self) {
            self.chatObject.find('.dx-form-chat-file-input').click();
        },
        onFileSelected: function (self) {
            var formData = new FormData();

            var fileInput = self.chatObject.find('.dx-form-chat-file-input')[0];

            if (fileInput.files.length <= 0) {
                return;
            }

            // Attach files
            for (var i = 0; i < fileInput.files.length; i++) {
                formData.append('file[]', fileInput.files[i]);
            }

            formData.append('list_id', self.listId);
            formData.append('item_id', self.itemId);

            self.chatContentObject.append('<li class="dx-form-chat-progress">Progress</li>');

            $.ajax({
                url: DX_CORE.site_url + 'chat/message/save',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (res) {
                    self.chatContentObject.find('.dx-form-chat-progress').remove()
                },
                xhr: function () {
                    self.uploadProgress(self);
                },
                error: function (res) {
                    self.chatContentObject.find('.dx-form-chat-progress').remove();
                    notify_err(Lang.get('form.chat.e_file_not_saved'));
                }
            });

            self.chatObject.find('.dx-form-chat-file-input').val('');
        },
        /**
         * Shows upload progress
         * @param {DxFormChat} self Current form chat instance
         */
        uploadProgress: function (self) {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;

                    self.chatContentObject.find('.dx-form-chat-progress').html(percentComplete + '%');

                    //Do something with upload progress here
                }
            }, false);

            return xhr;
        },
        /**
         * Opens chat window on button click
         * @param {DxFormChat} self Current form chat instance
         */
        onOpenChatClick: function (self) {
            // Must save state because after openChatPanel function it changes to true 
            // We need to call getChatData only after openChatPanel to be sure thate there woun't be multiple running background processes of chat window
            var stateIsVisible = self.stateIsVisible;

            // Opens chat
            self.openChatPanel(self);

            if (!stateIsVisible) {
                // Loads chat data
                self.getChatData();
            }
        },
        /**
         * Opens chat panel
         * @param {DxFormChat} self Current form chat instance
         */
        openChatPanel: function (self) {
            self.chatObject.find('.dx-form-chat-input-text').focus();

            if (self.stateIsVisible) {
                return;
            }

            self.stateIsVisible = true;

            // Clears old data from chat
            self.clearChat();

            self.chatObject.slideUp(400, function () {
                self.chatObject.appendTo(document.body);

                self.chatObject.find('.caption-helper').html(self.formTitle);

                self.chatObject.zIndex(self.domObject.closest('.modal-scrollable').zIndex());

                self.chatObject.slideDown();
            });
        },
        /**
         * Gets chat users
         */
        getChatUsers: function () {
            var self = this;

            $.ajax({
                url: DX_CORE.site_url + 'chat/users/' + self.listId + '/' + self.itemId,
                type: "get",
                success: function (res) {
                    self.loadChatUsers(self, res);
                },
                error: function () {
                    self.loadChatUsers(self, null);
                }
            });
        },
        /**
         * Loads chat users in tooltip
         * @param {DxFormChat} self Current form chat instance
         * @param {object} res Result of data request
         */
        loadChatUsers: function (self, res) {
            var modal = $('.dx-form-chat-modal');

            modal.appendTo(document.body);

            modal.find('.modal-title').html(Lang.get('form.chat.chat') + ' - ' + Lang.get('form.chat.users'));

            if (res && res.success && res.success == 1) {
                modal.find('.modal-body').html(res.view);

                modal.find('.dx-form-chat-btn-del-user').off('click');
                modal.find('.dx-form-chat-btn-del-user').click(function () {
                    var btn = this;
                    self.onDeleteUserConfirm(self, btn);
                });
            } else {
                modal.find('.modal-body').html(Lang.get('form.chat.e_no_users'));
            }

            modal.modal('show');

            hide_page_splash(1);
        },
        /**
         * Ask to confirm user removal from chat
         * @param {DxFormChat} self Current form chat instance
         * @param {DOM} btn Clicked button dom object
         */
        onDeleteUserConfirm: function (self, btn) {
            var title = Lang.get('form.chat.title_confirm_del_user');
            var body = Lang.get('form.chat.description_confirm_del_user');

            PageMain.showConfirm(function () {
                self.onDeleteUser(self, btn);
            }, null, title, body);
        },
        /**
         * Removes user from chat
         * @param {DxFormChat} self Current form chat instance
         * @param {DOM} btn Clicked button dom object
         */
        onDeleteUser: function (self, btn) {
            show_page_splash(1);

            var data = {
                list_id: self.listId,
                item_id: self.itemId,
                user_id: $(btn).data('user-id')
            };

            $.ajax({
                url: DX_CORE.site_url + 'chat/user/remove',
                data: data,
                type: "post",
                success: function () {
                    btn.closest('.dx-form-chat-user-list-row').remove();

                    hide_page_splash(1);

                    notify_info(Lang.get('form.chat.i_user_removed'));
                },
                error: function () {
                    hide_page_splash(1);

                    notify_err(Lang.get('form.chat.e_user_not_removed'));
                }
            });
        },
        /**
         * Opens modal for adding user to chat
         * @param {DxFormChat} self Current form chat instance
         */
        openAddChatUsersModal: function (self) {
            show_page_splash(1);

            var modal = $('.dx-form-chat-user-add-modal');

            modal.appendTo(document.body);

            modal.find('.modal-title').html(Lang.get('form.chat.chat') + ' - ' + Lang.get('form.chat.btn_add_user'));

            modal.find('.dx-form-chat-btn-save-user').click(function () {
                self.onClickSaveUserToChat(self, modal);
            });

            modal.find('.dx-form-chat-input-save-user').val('');
            modal.find('.dx-form-chat-input-save-user-title').val('');

            // Initializes autocomplete select box
            AutocompleateField.initSelect(modal.find('.dx-form-chat-user-field'));

            modal.modal('show');

            hide_page_splash(1);
        },
        /**
         * Adds user to chat
         * @param {DxFormChat} self Current form chat instance
         * @param {DOM} modal Current modal window instance
         */
        onClickSaveUserToChat: function (self, modal) {
            show_page_splash(1);

            var user_id = modal.find('.dx-form-chat-input-save-user').val();

            if (!user_id || user_id < 0) {
                notify_err(Lang.get('form.chat.e_user_not_specified'));
            }

            var data = {
                list_id: self.listId,
                item_id: self.itemId,
                user_id: user_id
            };

            $.ajax({
                url: DX_CORE.site_url + 'chat/user/add',
                data: data,
                type: "post",
                success: function (res) {
                    hide_page_splash(1);

                    if (res && res.success && res.success == 1) {
                        notify_info(Lang.get('form.chat.i_user_added'));
                        modal.modal('hide');
                    } else if (res && res.msg) {
                        notify_err(res.msg);
                    } else {
                        notify_err(Lang.get('form.chat.e_user_not_added'));
                    }
                },
                error: function () {
                    hide_page_splash(1);

                    notify_err(Lang.get('form.chat.e_user_not_added'));
                }
            });
        },
        /**
         * Opens modal window with chat's users
         * @param {DxFormChat} self Current form chat instance
         */
        openChatUsersModal: function (self) {
            show_page_splash(1);

            self.getChatUsers();
        },
        /**
         * Hides chat panel
         * @param {DxFormChat} self Current form chat instance
         */
        closeChatPanel: function (self) {
            self.stateIsVisible = false;
            self.lastMessageID = 0;
            self.chatObject.slideUp();
        },
        /**
         * Clears chat content
         */
        clearChat: function () {
            this.chatContentObject.empty();
            this.lastMessageID = 0;
        },
        /**
         * Event handler when message has been entered and must be saved
         * @param {DxFormChat} self Current form chat instance
         * @returns {undefined}
         */
        onMessageEnter: function (self) {
            var textArea = self.chatObject.find('.dx-form-chat-input-text');

            var data = {
                list_id: self.listId,
                item_id: self.itemId,
                message: textArea.val()
            };

            if (data.message.length == 0) {
                return;
            }

            textArea.val('');

            $.ajax({
                url: DX_CORE.site_url + 'chat/message/save',
                data: data,
                type: "post",
                success: function () {

                },
                error: function () {
                    notify_err(Lang.get('form.chat.e_msg_not_saved'));
                }
            });
        },
        /**
         * Loads chat's data
         */
        getChatData: function () {
            var self = this;

            if (self.stateIsUpdateRunning) {
                return;
            }

            self.stateIsUpdateRunning = true;

            $.ajax({
                url: DX_CORE.site_url + 'chat/messages/' + self.listId + '/' + self.itemId + '/' + self.lastMessageID,
                type: "get",
                success: function (res) {
                    self.stateIsUpdateRunning = false;

                    self.onDataRecevied(self, res)
                },
                error: function (err) {
                    self.stateIsUpdateRunning = false;

                    // self.catchError(err, Lang.get('crypto.e_get_user_cert'));
                }
            });
        },
        /**
         * Processes retrieved data
         * @param {DxFormChat} self Current form chat instance
         * @param {object} res Retrieved data from server
         */
        onDataRecevied: function (self, res) {
            if (res && res.success && res.success == 1) {
                if (!self.stateIsVisible && res.view.length > 0) {
                    self.openChatPanel(self);
                }

                if (self.stateIsVisible) {
                    if (res.view.length > 0) {
                        self.chatContentObject.append(res.view);

                        var container = self.chatObject.find('.dx-form-chat-content-container');
                        container.scrollTop(container[0].scrollHeight - container[0].clientHeight);
                    }

                    if (res.last_message_id != 0) {
                        self.lastMessageID = res.last_message_id;
                    }
                }
            } else {
                if (res.msg) {
                    //  self.catchError(res, res.msg);
                } else {
                    // self.catchError(res, Lang.get('crypto.e_get_user_cert'));
                }
            }

            // Calls again after 1000 ms   
            setTimeout(function () {
                if (self.stateIsVisible) {
                    self.getChatData();
                }
            }, 1000);
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes chat window
    $('.dx-form-chat-btn-open').DxFormChat();
});

$(document).ajaxComplete(function () {
    // Initializes chat window
    $('.dx-form-chat-btn-open').DxFormChat();
});
