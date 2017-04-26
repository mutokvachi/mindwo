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

            button.click(function (event) {
                event.preventDefault();
                event.stopPropagation();
                event.stopImmediatePropagation();
                window.DxCrypto.decryptFields();
            });
        },
        setAccessError: function () {
            var label = '<span class="label label-danger"> ' + Lang.get('crypto.e_no_access') + ' </span>';
            
            this.domObject.next('.dx-crypto-decrypt-btn').remove();
            this.domObject.after(label);
        },
        /**
         * Gets value of current element. It can be input or other container (e.g. div, span)
         * @returns {string}
         */
        getValue: function () {
            if (this.domObject.is('input') || this.domObject.is('textarea')) {
                return this.domObject.val();
            } else {
                return this.domObject.html();
            }
        },
        /**
         * Sets value of current element. It can be input or other container (e.g. div, span)
         * @param {string} value It will be set to element
         * @param {boolean} isVisible Shows element and hides decrypt button
         * @returns {undefined}
         */
        setValue: function (value, isVisible) {
            if (this.domObject.is('input') || this.domObject.is('textarea')) {
                this.domObject.val(value);
            } else {
                this.domObject.html(value);
            }

            if (isVisible) {
                // Shows field
                this.showField();
            } else {
                this.domObject.hide();
            }
        },
        /**
         * Shows field
         * @returns {undefined}
         */
        showField: function () {
            this.domObject.next('.dx-crypto-decrypt-btn').remove();
            this.domObject.show();
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
