/**
 * Crypto library which encrypt and decrypt data
 * @returns {window.DxCryptoRegenClass}
 */
window.DxCryptoRegenClass = function () {
    this.regenRecordTotalProcessedCounter = 0;
    this.regenRecordTotalCount = 0;
    this.regenRecordProcessedCount = 0;
    this.regenProcessId = 0;
    this.masterKeyGroupId = 0;
    this.regenCache;
    this.regenCancel = false;
    this.newMasterKey;
    this.wrappedMasterKeys = {};
};

/**
 * Extends crypto library prototype
 * @param {object} param1 Crypto library
 * @param {function} param2 Extended functionality
 */
$.extend(window.DxCryptoRegenClass.prototype, {
    catchError: function (err) {
        var self = window.DxCryptoRegen;

        self.resetRegenTempData();

        self.cancelProcess();

        $('#dx-crypto-modal-regen-masterkey').modal('hide');

        window.DxCrypto.catchError(err);
    },
    resetRegenTempData: function () {
        var self = window.DxCryptoRegen;

        self.regenRecordTotalProcessedCounter = 0;
        self.regenRecordTotalCount = 0;
        self.regenRecordProcessedCount = 0;
        self.regenProcessId = 0;
        self.masterKeyGroupId = 0;
        self.regenCache = undefined;
        self.newMasterKey = undefined;
        self.wrappedMasterKeys = {};
        self.regenCancel = false;

        var modal = $('#dx-crypto-modal-regen-masterkey');

        var bar = modal.find('.dx-crypto-modal-regen-progress-bar');
        bar.attr('aria-valuenow', 0);
        bar.attr('aria-valuemax', 100);
        bar.width(0 + '%');

    },
    /**
     * Generate new masterkey and regenrate data
     * @param {int} masterKeyGroupId Master key group's ID
     * @returns {Boolean}
     */
    regenMasterKey: function (masterKeyGroupId) {
        var self = window.DxCryptoRegen;

        self.resetRegenTempData();

        self.masterKeyGroupId = masterKeyGroupId;

        show_page_splash(1);

        if (!window.DxCrypto.certificate || !window.DxCrypto.certificate.publicKey || !window.DxCrypto.certificate.privateKey) {
            // Retrieves certificate and calls this function again
            window.DxCrypto.getCurrentUserCertificate(0, function () {
                self.regenMasterKey(self.masterKeyGroupId);
            });
            return false;
        }

        if (!(self.masterKeyGroupId in window.DxCrypto.masterKeyGroups)) {
            window.DxCrypto.catchError(null, Lang.get('crypto.e_missing_masterkey'));
            return false;
        }

        self.regenCancel = false;

        self.checkExistingRecrypt();
    },
    /**
     * Find if there already exist recryption process in progress
     * @returns {undefined}
     */
    checkExistingRecrypt: function () {
        var self = window.DxCryptoRegen;

        show_page_splash(1);

        $.ajax({
            url: DX_CORE.site_url + 'crypto/check_regen/' + self.masterKeyGroupId,
            type: "get",
            dataType: "json",
            success: function (res) {
                if (res && res.success && res.success == 1) {

                    if (res.process_id && res.process_id != 0) {
                        hide_page_splash(1);

                        var title = Lang.get('crypto.regen_process_exist');
                        var body = Lang.get('crypto.w_regen_process_exist');
                        var btn_yes = Lang.get('form.btn_yes');
                        var btn_no = Lang.get('form.btn_no');

                        var acceptFunc = function () {
                            show_page_splash(1);
                            self.continueReencryption(res.process_id);
                        };

                        var declineFunc = function () {
                            show_page_splash(1);
                            self.generateNewMasterKey();
                        };

                        PageMain.showConfirm(acceptFunc, null, title, body, btn_yes, btn_no, declineFunc);
                    } else {
                        self.generateNewMasterKey();
                    }
                } else {
                    window.DxCryptoRegen.catchError(null);
                }
            },
            error: function (err) {
                window.DxCryptoRegen.catchError(err);
            }
        });
    },
    /**
     * Continues existing reencryption process
     * @param {int} regenProcessId Data regeneration (reencryption) process' id 
     * @returns {undefined}
     */
    continueReencryption: function (regenProcessId) {
        var self = window.DxCryptoRegen;

        self.showRegenProgress();
        hide_page_splash(1);
        self.updateRegenStatus(Lang.get('crypto.i_gathering_data'));

        self.retrieveEncryptedData(regenProcessId, '');
    },
    /**
     * Generates new master key and starts new encryption process
     * @returns {undefined}
     */
    generateNewMasterKey: function () {
        var self = window.DxCryptoRegen;

        show_page_splash(1);

        // Generates new master key for current user
        window.DxCrypto.generateNewMasterKey(window.DxCrypto.certificate.publicKey, function (wrappedMasterKey, rawMasterKey) {
            self.newMasterKey = rawMasterKey;

            self.showRegenProgress();
            hide_page_splash(1);
            self.updateRegenStatus(Lang.get('crypto.i_gathering_data'));

            var masterKeyHex = window.DxCrypto.arrayBufferToHexString(wrappedMasterKey);

            self.retrieveEncryptedData(0, masterKeyHex);

        });
    },
    /**
     * Gets encrypted data from server
     * @param {int} regenProcessId Data regeneration (reencryption) process' id 
     * @param {CryptoKey} newWrappedMasterKey Master key's CryptoKey object
     * @returns {undefined}
     */
    retrieveEncryptedData: function (regenProcessId, newWrappedMasterKey) {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        var url = DX_CORE.site_url + 'crypto/pending_data/' + regenProcessId + '/' + self.masterKeyGroupId + '/' + (self.newMasterKey ? 0 : 1) + '/' + newWrappedMasterKey;

        $.ajax({
            url: url,
            type: "get",
            dataType: "json",
            success: function (res) {
                if (res && res.success && res.success == 1) {
                    self.regenRecordTotalProcessedCounter = res.cachedDataCount ? res.cachedDataCount : 0;
                    self.regenRecordTotalCount = res.totalDataCount ? res.totalDataCount : 0;
                    self.regenRecordProcessedCount = 0;
                    self.regenCache = new FormData();
                    self.regenProcessId = res.regenProcessId;

                    // If master key received unwraps it and then continues
                    if (res.masterKey && res.masterKey != '') {
                        var wrappedMasterKey = window.DxCrypto.hexStringToArrayBuffer(res.masterKey);

                        window.DxCrypto.unwrapMasterKeyByValue(wrappedMasterKey)
                                .then(function (masterKey) {
                                    self.newMasterKey = masterKey;

                                    self.recryptData(res.pendingData);
                                })
                                .catch(self.catchError);
                        ;
                    } else {
                        self.recryptData(res.pendingData);
                    }
                } else {
                    window.DxCryptoRegen.catchError(null);
                }
            },
            error: function (err) {
                window.DxCryptoRegen.catchError(err);
            }
        });
    },
    recryptData: function (pendingData) {
        var self = window.DxCryptoRegen;

        for (var i = 0; i < pendingData.length; i++) {
            self.recryptDataRow(pendingData, i);
        }

        // All already done
        if (pendingData.length <= 0) {
            // Starts to wrap new master key with user public keys. First must retrieve all public keys from data base
            self.getAllUserPublicKeys();
        }
    },
    onRecryptedDataRow: function (resBuffer, record, batchSize) {
        var self = window.DxCryptoRegen;
        var newValue;

        if (record.is_file == 1) {
            newValue = new Blob([new Uint8Array(resBuffer)], {type: "application/octet-stream"});
        } else {
            newValue = window.DxCrypto.arrayBufferToHexString(resBuffer);
        }

        ++self.regenRecordTotalProcessedCounter;
        ++self.regenRecordProcessedCount;

        self.updateRegenProgress();

        self.regenCache.append(record.id, newValue);

        if (self.regenRecordProcessedCount >= batchSize) {
            self.postCache();
        }
    },
    recryptDataRow: function (pendingData, rowNum) {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        var record = pendingData[rowNum];

        if (record.is_file == 1) {
            var xhr = new XMLHttpRequest();

            xhr.onload = function () {
                var reader = new FileReader();

                reader.readAsArrayBuffer(xhr.response);

                reader.onloadend = function () {

                    var oldValue = new Uint8Array(reader.result);

                    //callback(arrayBuffer, xhr.response.type);

                    self.encryptDecryptData(oldValue, function (resBuffer) {
                        self.onRecryptedDataRow(resBuffer, record, pendingData.length);
                    });
                };
            };
            xhr.open('GET', DX_CORE.site_url + 'download_by_field_' + record.old_value);
            xhr.responseType = 'blob';
            xhr.send();

        } else {
            var oldValue = window.DxCrypto.hexStringToArrayBuffer(record.old_value);

            self.encryptDecryptData(oldValue, function (resBuffer) {
                self.onRecryptedDataRow(resBuffer, record, pendingData.length);
            });
        }
    },
    encryptDecryptData: function (oldValue, callback) {
        var self = window.DxCryptoRegen;

        var counterBuffer = oldValue.subarray(0, 16);
        var resBuffer = oldValue.subarray(16, oldValue.length);

        if (!self.validateProgress()) {
            return false;
        }

        window.crypto.subtle.decrypt(
                {
                    name: "AES-CTR",
                    counter: counterBuffer, //The same counter you used to encrypt
                    length: 128, //The same length you used to encrypt
                },
                window.DxCrypto.masterKeyGroups[self.masterKeyGroupId], //from generateKey or importKey above
                resBuffer //ArrayBuffer of the data
                )
                .then(function (decryptedValue) {
                    if (!self.validateProgress()) {
                        return false;
                    }

                    // ENCRYPT DECRYPTED DATA WITH NEW MASTERKEY
                    var counterBuffer = new Uint8Array(16);

                    return window.crypto.subtle.encrypt(
                            {
                                name: "AES-CTR",
                                //Don't re-use counters!
                                //Always use a new counter every time your encrypt!
                                counter: counterBuffer,
                                length: 128, //can be 1-128
                            },
                            window.DxCryptoRegen.newMasterKey, //from generateKey or importKey above
                            decryptedValue //ArrayBuffer of the data
                            );
                })
                .then(function (encryptedValue) {
                    if (!self.validateProgress()) {
                        return false;
                    }

                    encryptedValue = new Uint8Array(encryptedValue);

                    var resBuffer = new Uint8Array(encryptedValue.length + counterBuffer.length);
                    resBuffer.set(counterBuffer);
                    resBuffer.set(encryptedValue, counterBuffer.length);

                    callback(resBuffer);
                })
                .catch(window.DxCryptoRegen.catchError);
    },
    postCache: function () {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        $.ajax({
            url: DX_CORE.site_url + 'crypto/save_regen_cache',
            data: self.regenCache,
            type: "post",
            processData: false,
            dataType: "json",
            contentType: false,
            success: function (res) {
                if (res && res.success) {
                    if (self.regenRecordTotalProcessedCounter >= self.regenRecordTotalCount) {
                        // Starts to wrap new master key with user public keys. First must retrieve all public keys from data base
                        self.getAllUserPublicKeys();
                    } else {
                        self.retrieveEncryptedData(self.regenProcessId, self.masterKeyGroupId, '');
                    }
                } else {
                    self.catchError(res);
                }
            },
            error: self.catchError
        });

        self.regenCache = new FormData();
    },
    /**
     * Sets regeneration modal progress window to successful state
     * @returns {undefined}
     */
    finishRegenProgress: function () {
        var modal = $('#dx-crypto-modal-regen-masterkey');

        modal.find('.dx-crypto-modal-regen-progress').hide();
        modal.find('.dx-crypto-modal-regen-succ').show();
        modal.find('.dx-crypto-modal-regen-btn-cancel').hide();
        modal.find('.dx-crypto-modal-regen-btn-close').show();

        var bar = modal.find('.dx-crypto-modal-regen-progress-bar');
        bar.attr('aria-valuenow', 0);
        bar.attr('aria-valuemax', 100);
        bar.width(0 + '%');
    },
    /**
     * Opens dialog to show progress of data reencryption
     * @returns {undefined}
     */
    showRegenProgress: function () {
        var modal = $('#dx-crypto-modal-regen-masterkey');

        modal.on('shown.bs.modal', function () {
            modal.find('.dx-crypto-modal-regen-progress').show();
            modal.find('.dx-crypto-modal-regen-succ').hide();
            modal.find('.dx-crypto-modal-regen-btn-cancel').show();
            modal.find('.dx-crypto-modal-regen-btn-close').hide();
        });

        modal.find('.dx-crypto-modal-regen-btn-cancel').click(window.DxCryptoRegen.cancelProcess);

        modal.on('hiden.bs.modal', function () {
            window.DxCryptoRegen.cancelProcess();
        });

        modal.modal('show');
    },
    cancelProcess: function () {
        var self = window.DxCryptoRegen;

        self.regenCancel = true;
        self.updateRegenStatus(Lang.get('crypto.i_cancel_regen_process'));
    },
    validateProgress: function () {
        if (window.DxCryptoRegen.regenCancel) {
            var modal = $('#dx-crypto-modal-regen-masterkey');

            modal.modal('hide');

            return false;
        }

        return true;
    },
    updateRegenProgress: function () {
        var self = window.DxCryptoRegen;

        var modal = $('#dx-crypto-modal-regen-masterkey');

        var current = self.regenRecordTotalProcessedCounter;
        var total = self.regenRecordTotalCount;

        var bar = modal.find('.dx-crypto-modal-regen-progress-bar');
        bar.attr('aria-valuenow', current);
        bar.attr('aria-valuemax', total);
        bar.width((current / total * 100) + '%');

        var label = modal.find('.dx-crypto-modal-regen-progress-label');
        label.html(Lang.get('crypto.regen_masterkey_records_label') + ': ' + current + '/' + total);
    },
    updateRegenStatus: function (text) {
        var modal = $('#dx-crypto-modal-regen-masterkey');

        modal.find('.dx-crypto-modal-regen-progress-label').html(text);
    },
    getAllUserPublicKeys: function () {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        self.updateRegenStatus(Lang.get('crypto.i_gathering_certs'));

        $.ajax({
            url: DX_CORE.site_url + 'crypto/get_user_public_keys',
            type: "get",
            success: function (res) {
                if (res && res.success && res.user_keys) {
                    self.updateRegenStatus(Lang.get('crypto.i_encrypting_master_keys'));

                    self.wrapMasterkeys(res.user_keys, 0);
                } else {
                    self.catchError(res);
                }
            },
            error: self.catchError
        });
    },
    wrapMasterkeys: function (userKeyData, i) {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        if (i >= userKeyData.length) {
            self.applyCache();
            return false;
        }

        var userKeyDataRow = userKeyData[i];

        var publicKeyBuffer = new Uint8Array(window.DxCrypto.base64ToArrayBuffer(userKeyDataRow.public_key));

        window.DxCrypto.importPublicKey(publicKeyBuffer)
                .then(function (publicKey) {
                    return window.DxCrypto.wrapMasterKey(publicKey, self.newMasterKey)
                })
                .then(function (wrappedMasterKey) {
                    self.wrappedMasterKeys[userKeyDataRow.user_id] = window.DxCrypto.arrayBufferToHexString(wrappedMasterKey);

                    /*{
                     master_key : window.DxCrypto.arrayBufferToHexString(wrappedMasterKey),
                     user_id: userKeyDataRow.user_id
                     };*/

                    self.wrapMasterkeys(userKeyData, ++i);
                })
                .catch(self.catchError);
    },
    applyCache: function () {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        self.updateRegenStatus(Lang.get('crypto.i_saving_regen_data'));

        $.ajax({
            url: DX_CORE.site_url + 'crypto/apply_regen_cache',
            data: {
                regen_process_id: self.regenProcessId,
                master_keys: self.wrappedMasterKeys
            },
            type: "post",
            success: function (res) {
                if (res && res.success) {
                    self.finishRegenProgress();

                    window.DxCrypto.masterKeyGroups[self.masterKeyGroupId] = self.newMasterKey;
                } else {
                    self.catchError(res);
                }
            },
            error: self.catchError
        });
    }
});

window.DxCryptoRegen = new window.DxCryptoRegenClass();
