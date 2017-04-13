(function ($)
{
    /**
     * Creates jQuery plugin for crypto fields
     * @returns DxCryptoField
     */
    $.fn.DxCryptoField = function ()
    {
        return this.each(function ()
        {
            this.crypto = new $.DxCryptoField($(this));
        });
    };

    /**
     * Class for managing crypto fields
     * @type DxCryptoField 
     */
    $.DxCryptoField = function (domObject) {
        /**
         * Field's DOM object which is related to this class
         */
        this.domObject = domObject;

        // Initializes class
        this.init();
    };

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxCryptoField.prototype, {
        /**
         * Initializes field
         * @returns {undefined}
         */
        init: function () {
            var self = this;

            if (self.domObject.data('dx_is_init') == 1) {
                return;
            }

            self.domObject.data('dx_is_init', 1);

            self.domObject.css('visibility', 'hidden');
            self.domObject.hide();

            self.addDecryptButton(self);
        },
        /**
         * Replaces field's html with button for decryption
         * @param {$.DxCryptoField} self
         * @returns {undefined}
         */
        addDecryptButton: function (self) {
            var button = $('<button class="btn btn-xs default dx-crypto-decrypt-btn"> ' +
                    Lang.get('crypto.decrypt_btn') +
                    ' <i class="fa fa-lock"></i></button>');

            self.domObject.after(button);

            button.click(self.openPasswordForm);
        },
        /**
         * Decryptes data and restore original html and value
         * @returns {undefined}
         */
        decryptData: function () {
            this.domObject.next('.dx-crypto-decrypt-btn').remove();
            this.domObject.css('visibility', 'visible');
            this.domObject.show();
        },
        openPasswordForm: function () {
            var title = Lang.get('crypto.title_modal_password');
            var body = '<label>' + Lang.get('crypto.label_password') + '</label><input class="form-control" id="dx-crypto-modal-input-password" type="password" />';

            PageMain.showConfirm(window.DxCrypto.decryptFields, null, title, body);
        },
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes all found worklfow containers
    $('.dx-crypto-field[data-dx_is_init!=1]').DxCryptoField();
});

$(document).ajaxComplete(function () {
    // Initializes all found worklfow containers
    $('.dx-crypto-field[data-dx_is_init!=1]').DxCryptoField();
});
