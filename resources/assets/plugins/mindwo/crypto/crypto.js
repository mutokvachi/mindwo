/**
 * Crypto library which encrypt and decrypt data
 * @returns {window.DxCryptoClass}
 */
window.DxCryptoClass = function () {
    this.certificate;
};

/**
 * Extends crypto library prototype
 * @param {object} param1 Crypto library
 * @param {function} param2 Extended functionality
 */
$.extend(window.DxCryptoClass.prototype, {
    /**
     * Decryptes all fields
     * @returns {undefined}
     */
    decryptFields: function () {
        $('.dx-crypto-field').each(function () {
            this.crypto.decryptData();
        });
    },
    /**
     * Import password as base key which is plain password converted to CryptoKey object
     * @param {string} password
     * @returns {undefined}
     */
    importPassword: function (password) {
        var self = window.DxCrypto;

        // don't use native approaches for converting text, otherwise international
        // characters won't have the correct byte sequences. Use TextEncoder when
        // available or otherwise use relevant polyfills
        var passwordBuffer = self.stringToArrayBuffer(password);

        // Import password to CryptoKey object
        window.crypto.subtle.importKey(
                'raw',
                passwordBuffer,
                {name: 'PBKDF2'},
                false,
                ['deriveKey']
                )
                .then(function (baseKey) {
                    self.derivePasswordKey(baseKey);
                })
                .catch(function (err) {
                    console.error(err);
                });
    },
    /**
     * Derives password CryptoKey from base CryptoKey object
     * @param {CryptoKey} baseKey Key imported from plain string
     * @returns {undefined}
     */
    derivePasswordKey: function (baseKey) {
        // salt should be Uint8Array or ArrayBuffer
        var saltBuffer = window.DxCrypto.stringToArrayBuffer('YyZYm6EuGaa1BhbDPwjy');

        window.crypto.subtle.deriveKey(
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
                .then(function (passwordKey) {
                    window.DxCrypto.generateUserCert(passwordKey);
                })
                .catch(function (err) {
                    console.error(err);
                });
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
                .catch(function (err) {
                    console.error(err);
                });
    },
    /**
     * Wraps users private key with passwords key
     * @param {CryptoKey} passwordKey Password key
     * @param {CryptoKey} asyncKey Users certificate
     * @returns {undefined}
     */
    wrapPrivateKey: function (passwordKey, asyncKey) {
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
                    var cert = {
                        publicKey: asyncKey.publicKey,
                        wrappedPrivateKey: wrappedPrivateKey
                    };

                    window.DxCrypto.saveUserCert(cert);
                })
                .catch(function (err) {
                    console.error(err);
                });
    },
    /**
     * Saves users certificate
     * @param {JSON} cert
     * @returns {undefined}
     */
    saveUserCert: function (cert) {
        window.DxCrypto.certificate = cert;

        console.log('succ');
        console.log(new Uint8Array(cert.wrappedPrivateKey));
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
    getPrivateKey: function(){
        
    },
    getRandomMasterKey: function () {
        var masterKey = window.crypto.getRandomValues(new Uint8Array(36));

        window.crypto.subtle.encrypt(
                {
                    name: "AES-CTR",
                    //Don't re-use counters!
                    //Always use a new counter every time your encrypt!
                    counter: new Uint8Array(16),
                    length: 128 //can be 1-128
                },
                key, //from generateKey or importKey above
                masterKey //ArrayBuffer of data you want to encrypt
                )
                .then(function (encrypted) {
                    //returns an ArrayBuffer containing the encrypted data
                    console.log(new Uint8Array(encrypted));
                })
                .catch(function (err) {
                    console.error(err);
                });

        /*var charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
         
         if (window.crypto && window.crypto.getRandomValues)
         {
         var result = "";
         var length = 36;
         var values = new Uint32Array(length);
         
         window.crypto.getRandomValues(values);
         
         for (var i = 0; i < length; i++)
         {
         result += charset[values[i] % charset.length];
         }
         
         return result;
         } else{
         console.error('nevar uzģenerēt');
         }*/
    }
});

window.DxCrypto = new window.DxCryptoClass();
