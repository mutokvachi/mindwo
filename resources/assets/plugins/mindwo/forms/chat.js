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
         * Last time when messages pulled from server
         */
        this.lastUpdateTime = 0;

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
            self.chatObject.find('.dx-form-chat-btn-close').click(function () {
                self.closeChatPanel(self);
            });

            // Message send button click
            self.chatObject.find('.dx-form-chat-btn-send').click(function () {
                self.onMessageEnter(self);
            });

            // On input key down if enter clicked without shift then we will send message on relase
            self.chatObject.find('.dx-form-chat-input-text').keydown(function (e) {
                if (e.keyCode == 13 && !e.shiftKey) {
                    e.preventDefault();
                }
            });

            // On input key op if enter clicked without shift then we send message
            self.chatObject.find('.dx-form-chat-input-text').keyup(function (e) {
                if (e.keyCode == 13 && !e.shiftKey) {
                    e.preventDefault();
                    self.onMessageEnter(self);
                }
            });

            // Retrieves chat messages and opens chat if messages found
            self.getChatData();
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
         * Hides chat panel
         * @param {DxFormChat} self Current form chat instance
         */
        closeChatPanel: function (self) {
            self.stateIsVisible = false;
            self.lastUpdateTime = 0;
            self.chatObject.slideUp();
        },
        /**
         * Clears chat content
         */
        clearChat: function () {
            this.chatObject.find('.dx-form-chat-content').empty();
            self.lastUpdateTime = 0;
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
                url: DX_CORE.site_url + 'chat/messages/' + self.listId + '/' + self.itemId + '/' + self.lastUpdateTime,
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

                if (self.stateIsVisible && self.lastUpdateTime != res.time) {

                    if (res.view.length > 0) {
                        self.chatContentObject.append(res.view);

                        var container = self.chatObject.find('.dx-form-chat-content-container');
                        container.scrollTop(container[0].scrollHeight - container[0].clientHeight);
                    }

                    self.lastUpdateTime = res.time;
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
