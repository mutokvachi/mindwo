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

            // Opens chat window
            self.domObject.click(function () {
                self.openChatPanel(self);
            });

            // Handle chat close when modal has been closed
            self.domObject.on("remove", function () {
                self.closeChatPanel(self);
            });

            // Handles chat close button
            self.chatObject.find('.dx-form-chat-btn-close').click(function () {
                self.closeChatPanel(self);
            });

            self.chatObject.find('.dx-form-chat-btn-send').click(function(){
                self.onMessageEnter(self);
            });
            self.chatObject.find('.dx-form-chat-input-text').keyup(function (e) {
                if (e.keyCode == 13 && e.keyCode != 16) {
                    self.onMessageEnter(self);
                }
            });

            // Clears old data from chat
            self.clearChat();

            // Loads chat data
            self.getChatData(1);
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

            self.chatObject.slideUp(400, function () {
                self.stateIsVisible = true;

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
            self.chatObject.slideUp();
        },
        /**
         * Clears chat content
         */
        clearChat: function () {
            this.chatObject.find('.dx-form-chat-content').empty();
        },
        /**
         * Event handler when message has been entered and must be saved
         * @param {DxFormChat} self Current form chat instance
         * @returns {undefined}
         */
        onMessageEnter: function (self) {
            var data = {
                list_id: self.listId,
                item_id: self.itemId,
                message: self.chatObject.find('.dx-form-chat-input-text').html()
            };

            $.ajax({
                url: DX_CORE.site_url + 'chat/message/save',
                data: data,
                type: "post",
                success: function(){

                },
                error: function(){
                    notify_err(Lang.get('forms.chat.e_msg_not_saved'));
                }
            });
        },
        /**
         * Loads chat's data
         */
        getChatData: function (isInit) {
            var self = this;

            $.ajax({
                url: DX_CORE.site_url + 'chat/messages/' + self.listId + '/' + self.itemId + '/' + isInit,
                type: "get",
                success: function (res) {
                    self.onDataRecevied(self, res)
                },
                error: function (err) {
                    // self.catchError(err, Lang.get('crypto.e_get_user_cert'));
                }
            });
        },
        onDataRecevied: function (self, res) {
            if (res && res.success && res.success == 1) {

                // Calls again after 1000 ms
                /*
                if(self.stateIsVisible){
                setTimeout(1000, function () {
                    self.getChatData(0);
                });
                }*/
            } else {
                if (res.msg) {
                    //  self.catchError(res, res.msg);
                } else {
                    // self.catchError(res, Lang.get('crypto.e_get_user_cert'));
                }
            }
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
