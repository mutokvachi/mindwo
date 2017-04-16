/**
 * Crypto library which encrypt and decrypt data
 * @returns {window.DxCryptoClass}
 */
window.DxCryptoClass = function () {
    this.certificate;
    this.masterKey;
};

/**
 * Extends crypto library prototype
 * @param {object} param1 Crypto library
 * @param {function} param2 Extended functionality
 */
$.extend(window.DxCryptoClass.prototype, {
    /**
     * Catches error and output error to user
     * @param {object} err Error details
     * @returns {undefined}
     */
    catchError: function (err) {
        notify_err(Lang.get('crypto.e_unknown'));
        hide_page_splash(1);
        hide_form_splash(1);
    },
    /**
     * Create passwords CryptoKey object from given password
     * @param {string} password
     * @returns {undefined}
     */
    createPasswordKey: function (password) {
        var self = window.DxCrypto;

        // don't use native approaches for converting text, otherwise international
        // characters won't have the correct byte sequences. Use TextEncoder when
        // available or otherwise use relevant polyfills
        var passwordBuffer = self.stringToArrayBuffer(password);

        // Import password to CryptoKey object - base key which contains plain password castet as CryptoKey object
        return window.crypto.subtle.importKey(
                'raw',
                passwordBuffer,
                {name: 'PBKDF2'},
                false,
                ['deriveKey']
                )
                .then(function (baseKey) {
                    return self.derivePasswordKey(baseKey);
                })
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Derives password CryptoKey from base CryptoKey object
     * @param {CryptoKey} baseKey Key imported from plain string
     * @returns {undefined}
     */
    derivePasswordKey: function (baseKey) {
        // salt should be Uint8Array or ArrayBuffer
        var saltBuffer = window.DxCrypto.stringToArrayBuffer('YyZYm6EuGaa1BhbDPwjy');

        return window.crypto.subtle.deriveKey(
                {"name": 'PBKDF2',
                    "salt": saltBuffer,
                    // don't get too ambitious, or at least remember
                    // that low-power phones will access your app
                    "iterations": 1000,
                    "hash": 'SHA-256'
                },
                baseKey,
                {"name": 'AES-CTR', "length": 256}, // For AES the length required to be 128 or 256 bits (not bytes)

                false, // Whether or not the key is extractable (less secure) or not (more secure) when false, the key can only be passed as a web crypto object, not 

                ["wrapKey", "unwrapKey"] // this web crypto object will only be allowed for these functions
                )
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Generates users certificate (with public and private keys)
     * @param {CryptoKey} passwordKey Password 
     * @returns {undefined}
     */
    generateUserCert: function (passwordKey) {
        window.crypto.subtle.generateKey(
                {
                    name: "RSA-OAEP",
                    modulusLength: 2048, //can be 1024, 2048, or 4096
                    publicExponent: new Uint8Array([0x01, 0x00, 0x01]),
                    hash: {name: "SHA-256"} //can be "SHA-1", "SHA-256", "SHA-384", or "SHA-512"
                },
                true, //whether the key is extractable (i.e. can be used in exportKey)
                ["encrypt", "decrypt"] //must be ["encrypt", "decrypt"] or ["wrapKey", "unwrapKey"]
                )
                .then(function (asyncKey) {
                    window.DxCrypto.wrapPrivateKey(passwordKey, asyncKey);
                })
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Wraps users private key with passwords key
     * @param {CryptoKey} passwordKey Password key
     * @param {CryptoKey} asyncKey Users certificate
     * @returns {undefined}
     */
    wrapPrivateKey: function (passwordKey, asyncKey) {
        window.DxCrypto.certificate = asyncKey;
        
        window.crypto.subtle.wrapKey(
                "pkcs8", //can be "jwk", "raw", "spki", or "pkcs8"
                asyncKey.privateKey, // CTR the key you want to wrap, must be able to export to "raw" format // CBC the key you want to wrap, must be able to export to above format
                passwordKey, //the AES-CTR key with "wrapKey" usage flag
                {//these are the wrapping key's algorithm options
                    name: "AES-CTR",
                    //Don't re-use counters!
                    //Always use a new counter every time your encrypt!
                    counter: new Uint8Array(16),
                    length: 128 //can be 1-128
                })
                .then(function (wrappedPrivateKey) {
                    window.DxCrypto.saveUserCert(asyncKey.publicKey, wrappedPrivateKey);
                })
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Saves users certificate
     * @param {CryptoKey} publicKey Public keys CryptoKey object
     * @param {array} wrappedPrivateKey
     * @returns {undefined}
     */
    saveUserCert: function (publicKey, wrappedPrivateKey) {
        window.crypto.subtle.exportKey(
                "jwk", //can be "jwk" (public or private), "spki" (public only), or "pkcs8" (private only)
                publicKey //can be a publicKey or privateKey, as long as extractable was true
                )
                .then(function (publicKeyBuffer) {
                    var cert = {
                        publicKey: publicKeyBuffer,
                        privateKey: wrappedPrivateKey
                    };

                    // new Uint8Array(cert.wrappedPrivateKey)

                    hide_page_splash(1);
                })
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Converts string to array buffer
     * @param {string} string Inpur string
     * @returns {array} String converted to array buffer
     */
    stringToArrayBuffer: function (string) {
        var encoder = new TextEncoder("utf-8");
        return encoder.encode(string);
    },
    /**
     * Converts array buffer to string
     * @param {array} arrayBuffer Array buffer which will be converted to string
     * @returns {string} Output string
     */
    arrayBufferToHexString: function (arrayBuffer) {
        var byteArray = new Uint8Array(arrayBuffer);
        var hexString = "";
        var nextHexByte;

        for (var i = 0; i < byteArray.byteLength; i++) {
            nextHexByte = byteArray[i].toString(16);
            if (nextHexByte.length < 2) {
                nextHexByte = "0" + nextHexByte;
            }
            hexString += nextHexByte;
        }
        return hexString;
    },
    /**
     * Decryptes all fields
     * @returns {undefined}
     */
    decryptFields: function (privateKey) {
        show_page_splash(1);

        window.crypto.subtle.decrypt(
                {
                    name: "RSA-OAEP"
                },
                privateKey, //from generateKey or importKey above
                window.DxCrypto.masterKeyCrypted //ArrayBuffer of the data
                )
                .then(function (decrypted) {
                    //returns an ArrayBuffer containing the decrypted data
                    console.log(new Uint8Array(decrypted));

                    hide_page_splash(1);
                })
                .catch(window.DxCrypto.catchError);

        /*$('.dx-crypto-field').each(function () {
         this.crypto.decryptData();
         });*/
    },
    openPasswordForm: function (callback) {
        var title = Lang.get('crypto.title_modal_password');
        var body = '<label>' + Lang.get('crypto.label_password') + '</label><input class="form-control" id="dx-crypto-modal-input-password" type="password" />';

        PageMain.showConfirm(function () {
            window.DxCrypto.getUserCertificate(callback);
        }, null, title, body);
    },
    getUserCertificate: function (callback) {
        var password = $('#dx-crypto-modal-input-password').val();

        var cert = window.DxCrypto.certificate;

        window.DxCrypto.getPasswordKey(password)
                .then(function (passwordKey) {
                    return window.DxCrypto.unwrapPrivateKey(passwordKey, cert);
                })
                .then(function (privateKey) {
                    callback(privateKey);
                })
                .catch(window.DxCrypto.catchError);
    },
    unwrapPrivateKey: function (passwordKey, wrappedPrivateKey) {
        return window.crypto.subtle.unwrapKey(
                "pkcs8", //"jwk", "raw", "spki", or "pkcs8" (whatever was used in wrapping)
                wrappedPrivateKey, //the key you want to unwrap
                passwordKey, //the AES-CTR key with "unwrapKey" usage flag
                {//these are the wrapping key's algorithm options
                    name: "AES-CTR",
                    //Don't re-use counters!
                    //Always use a new counter every time your encrypt!
                    counter: new Uint8Array(16),
                    length: 128 //can be 1-128
                },
                {//this what you want the wrapped key to become (same as when wrapping)
                    name: "RSA-OAEP",
                    modulusLength: 2048, //can be 1024, 2048, or 4096
                    publicExponent: new Uint8Array([0x01, 0x00, 0x01]),
                    hash: {name: "SHA-256"} //can be "SHA-1", "SHA-256", "SHA-384", or "SHA-512"
                },
                false, //whether the key is extractable (i.e. can be used in exportKey)
                ["decrypt"] //the usages you want the unwrapped key to have
                )
                .then(function (privateKey) {
                    return privateKey;
                })
                .catch(window.DxCrypto.catchError);
    },
    getRandomMasterKey: function () {
        var masterKey = window.crypto.getRandomValues(new Uint8Array(36));

        window.DxCrypto.masterKey = masterKey;

        var publicKey = window.DxCrypto.certificate.publicKey;

        return window.crypto.subtle.encrypt(
                {
                    name: "RSA-OAEP"
                },
                publicKey, //from generateKey or importKey above
                masterKey //ArrayBuffer of data you want to encrypt
                )
                .then(function (encrypted) {
                    window.DxCrypto.masterKeyCrypted = encrypted;

                    //returns an ArrayBuffer containing the encrypted data
                    console.log(new Uint8Array(encrypted));

                    return new Uint8Array(encrypted);
                })
                .catch(window.DxCrypto.catchError);
    }
});

window.DxCrypto = new window.DxCryptoClass();
