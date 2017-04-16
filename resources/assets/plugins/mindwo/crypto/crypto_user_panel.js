(function ($)
{
    /**
     * Creates jQuery plugin for crypto fields
     * @returns DxCryptoUserPanel
     */
    $.fn.DxCryptoUserPanel = function ()
    {
        return this.each(function ()
        {
            this.crypto = new $.DxCryptoUserPanel($(this));
        });
    };

    /**
     * Class for managing crypto fields
     * @type DxCryptoUserPanel 
     */
    $.DxCryptoUserPanel = function (domObject) {
        /**
         * Field's DOM object which is related to this class
         */
        this.domObject = domObject;

        // Initializes class
        this.init();
    };
    /**
     * Extends crypto library prototype
     * @param {object} param1 Crypto library
     * @param {function} param2 Extended functionality
     */
    $.extend($.DxCryptoUserPanel.prototype, {
        /**
         * Initializes user panel
         * @returns {undefined}
         */
        init: function () {
            if (!window.crypto || !window.crypto.subtle) {
                return;
            }

            if (!window.TextEncoder) {
                return;
            }

            var self = this;

            if (self.domObject.data('dx_is_init') == 1) {
                return;
            }

            self.domObject.data('dx_is_init', 1);

            $('.dx-crypto-generate-cert-btn', self.domObject).click(this.openGenerateCertificate);

            $('.dx-crypto-generate-masterkey-btn', self.domObject).click(function () {
                var masterKey = window.DxCrypto.getRandomMasterKey();

                $('#dx-master-key', self.domObject).html(masterKey);
            });

            $('#dx-crypto-modal-generate-cert').on('click', '.dx-crypto-modal-accept', this.validateCertPassword);
        },
        /**
         * Opens modal windows where user inputs password for certificate
         * @returns {undefined}
         */
        openGenerateCertificate: function (self) {
            $('#dx-crypto-modal-generate-cert').modal('show');
        },
        /**
         * Validates users password
         * @returns {Boolean}
         */
        validateCertPassword: function () {            
            var password = $('#dx-crypto-modal-input-password').val();
            var password_2 = $('#dx-crypto-modal-input-password-again').val();

            if (password == '' || password != password_2) {
                return false;
            }
            
            show_page_splash(1);

            window.DxCrypto.createPasswordKey(password)
                    .then(function (passwordKey) {
                        window.DxCrypto.generateUserCert(passwordKey);
                    })
                    .catch(window.DxCrypto.catchError);

            $('#dx-crypto-modal-generate-cert').modal('hide');
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes all found worklfow containers
    $('.dx-crypto-user_panel-page[data-dx_is_init!=1]').DxCryptoUserPanel();
});

$(document).ajaxComplete(function () {
    // Initializes all found worklfow containers
    $('.dx-crypto-user_panel-page[data-dx_is_init!=1]').DxCryptoUserPanel();
});
