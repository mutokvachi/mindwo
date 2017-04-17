/**
 * Crypto library which encrypt and decrypt data
 * @returns {window.DxCryptoClass}
 */
window.DxCryptoClass = function () {
    this.certificate;
    this.masterKeyGroups = new Array();
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
     * @param {type} msg Error message (optional)
     * @returns {undefined}
     */
    catchError: function (err, msg) {
        if (!msg) {
            msg = Lang.get('crypto.e_unknown');
        }

        hide_page_splash(1);
        hide_form_splash(1);
        notify_err(msg);
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
                    window.crypto.subtle.exportKey(
                            "spki", //can be "jwk" (public or private), "spki" (public only), or "pkcs8" (private only)
                            asyncKey.publicKey //can be a publicKey or privateKey, as long as extractable was true
                            )
                            .then(function (publicKeyBuffer) {
                                window.DxCrypto.saveUserCert(publicKeyBuffer, wrappedPrivateKey);
                            })
                            .catch(window.DxCrypto.catchError);
                })
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Saves users certificate
     * @param {array} publicKeyBuffer Public key's array buffer
     * @param {array} wrappedPrivateKey Wrapped private key's array buffer
     * @returns {undefined}
     */
    saveUserCert: function (publicKeyBuffer, wrappedPrivateKey) {
        var self = window.DxCrypto;

        var publicKeyBlob = new Blob([new Uint8Array(publicKeyBuffer)], {type: "application/octet-stream"});
        var privateKeyBlob = new Blob([new Uint8Array(wrappedPrivateKey)], {type: "application/octet-stream"});

        var fd = new FormData();
        fd.append('public_key', publicKeyBlob);
        fd.append('private_key', privateKeyBlob);

        // new Uint8Array(cert.wrappedPrivateKey)

        $.ajax({
            url: DX_CORE.site_url + 'crypto/save_cert',
            data: fd,
            type: "post",
            processData: false,
            dataType: "json",
            contentType: false,
            success: function (res) {
                hide_page_splash(1);

                if (res && res.success) {
                    notify_info(Lang.get('crypto.i_save_cert_success'));
                    $('.dx-crypto-generate-cert-btn').hide();
                    $('.dx-crypto-generate-new-cert-btn').show();
                } else {
                    self.catchError(res);
                }
            },
            error: function (err) {
                self.catchError(err, Lang.get('crypto.e_save'));
            }
        });
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
     * @param {ArrayBuffer} arrayBuffer Array buffer which will be converted to string
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
    /**
     * Open modal window and ask user to input his certificate's password
     * @param {function} callback Function to call after certificate has been retrieved
     * @returns {undefined}
     */
    requestUserCertificatePassword: function (callback) {
        var title = Lang.get('crypto.title_modal_password');
        var body = '<label>' + Lang.get('crypto.label_password') + '</label><input class="form-control" id="dx-crypto-modal-input-password" type="password" />';

        PageMain.showConfirm(function () {
            window.DxCrypto.decryptUserCertificate(callback);
        }, null, title, body);
    },
    /**
     * Decryptes user's certificate and stores in memory
     * @param {function} callback Function to call after certificate has been retrieved
     * @returns {undefined}
     */
    decryptUserCertificate: function (callback) {
        var password = $('#dx-crypto-modal-input-password').val();
        $('#dx-crypto-modal-input-password').val('');

        // Import public key from raw format to CryptoKey object
        window.crypto.subtle.importKey(
                "spki", //can be "jwk" (public or private), "spki" (public only), or "pkcs8" (private only)
                window.DxCrypto.rawCertificate.publicKey,
                {//these are the algorithm options
                    name: "RSA-OAEP",
                    hash: {name: "SHA-256"}, //can be "SHA-1", "SHA-256", "SHA-384", or "SHA-512"
                },
                false, //whether the key is extractable (i.e. can be used in exportKey)
                ["encrypt"] //"encrypt" or "wrapKey" for public key import or
                //"decrypt" or "unwrapKey" for private key imports
                )
                .then(function (publicKey) {
                    // Saves imported public key
                    window.DxCrypto.certificate = {
                        publicKey: publicKey
                    };

                    // Generate password key (Cryptokey object) from password
                    return window.DxCrypto.createPasswordKey(password);
                })
                .then(function (passwordKey) {
                    // Unwraps password
                    return window.DxCrypto.unwrapPrivateKey(passwordKey);
                })
                .then(function (privateKey) {
                    window.DxCrypto.certificate.privateKey = privateKey;

                    window.DxCrypto.rawCertificate = undefined;

                    callback();
                })
                .catch(window.DxCrypto.catchError);
    },
    /**
     * Unwraps user's private key
     * @param {CryptoKey} passwordKey Password CryptoKey object derived from password
     * @param {ArrayBuffer} wrappedPrivateKey ArrayBuffer contains private key
     * @returns {CryptoKey} Unwrapped private key
     */
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
    /**
     * Get specified users certificate
     * @param {int} userId User's ID whose certificate we want to retrieve
     * @param {function} callback Function to call after certificate has been retrieved
     * @returns {undefined}
     */
    getUserCertificate: function (userId, callback) {
        $.ajax({
            url: DX_CORE.site_url + 'crypto/get_user_cert/' + userId,
            type: "get",
            success: function (res) {
                if (res && res.success && res.success == 1) {
                    callback(res.public_key, res.private_key);
                } else {
                    if (res.msg) {
                        window.DxCrypto.catchError(res, res.msg);
                    } else {
                        window.DxCrypto.catchError(res, Lang.get('crypto.e_get_user_cert'));
                    }
                }
            },
            error: function (err) {
                window.DxCrypto.catchError(err, Lang.get('crypto.e_get_user_cert'));
            }
        });
    },
    /**
     * Gets current users certificate from server and the store it in memory.
     * @param {function} callback Function to call after certificate has been stored in memory
     * @returns {undefined}
     */
    getCurrentUserCertificate: function (callback) {
        var self = window.DxCrypto;

        self.getUserCertificate(0, function (public_key, private_key) {
            self.rawCertificate = {
                publicKey: new Uint8Array(public_key),
                privateKey: new Uint8Array(private_key)
            };

            self.requestUserCertificatePassword(callback);
        });
    },
    /**
     * Generates master key for user in specified master key group
     * @param {int} masterKeygroupId Master keys group ID
     * @param {int} userId User's ID
     * @returns {undefined}
     */
    generateMasterKey: function (masterKeyGroupId, userId) {
        var self = window.DxCrypto;

        if (!self.certificate || !self.certificate.publicKey) {
            // Retrieves certificate and calls this function again
            self.getCurrentUserCertificate(function () {
                self.generateMasterKey(masterKeyGroupId, userId);
            });
        } else if (!(masterKeyGroupId in self.masterKeyGroups)) {
            // Retrieve master key
        } else {
            // ģenerē sertitifkātu
        }





        var publicKey = window.DxCrypto.certificate.publicKey;

        window.crypto.subtle.generateKey(
                {
                    name: "AES-CTR",
                    length: 256 //can be  128, 192, or 256
                },
        true, //whether the key is extractable (i.e. can be used in exportKey)
                ["encrypt", "decrypt"] //must be ["encrypt", "decrypt"] or ["wrapKey", "unwrapKey"]
                )
                .then(function (masterKey) {
                    return window.crypto.subtle.wrapKey(
                            "pkcs8", //can be "jwk", "raw", "spki", or "pkcs8"
                            masterKey, // CTR the key you want to wrap, must be able to export to "raw" format // CBC the key you want to wrap, must be able to export to above format
                            publicKey, //the AES-CTR key with "wrapKey" usage flag
                            {//these are the wrapping key's algorithm options
                                name: "RSA-OAEP",
                                hash: {name: "SHA-256"}
                            });
                })
                .then(function (wrappedMasterKey) {
                    window.DxCrypto.masterkey = wrappedMasterKey;
                })
                .catch(window.DxCrypto.catchError);
    }
});

window.DxCrypto = new window.DxCryptoClass();
