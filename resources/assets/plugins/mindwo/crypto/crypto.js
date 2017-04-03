/**
 * Crypto library which encrypt and decrypt data
 * @returns {window.DxCrypto}
 */
window.DxCrypto = window.DxCrypto || function () {

}

/**
 * Extends crypto library prototype
 * @param {object} param1 Crypto library
 * @param {function} param2 Extended functionality
 */
$.extend(window.DxCrypto, {
    /**
     * Initializes field
     * @returns {undefined}
     */
    decryptData: function () {
        var self = window.DxCrypto;

        self.openPasswordForm(self.decryptFields);
    },
    decryptFields: function () {
        $('.dx-crypto-field').each(function () {
            this.crypto.decryptData();
        });
    },
    openPasswordForm: function (callback, callbackParameters) {
        var modal = $('#dx-crypto-modal');

        var accept_btn = modal.find('#dx-crypto-modal-accept');

        accept_btn.click(function () {
            accept_btn.off('click');

            callback(callbackParameters);
        });

        modal.modal('show');
    }
});