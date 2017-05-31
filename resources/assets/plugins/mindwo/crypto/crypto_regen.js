/**
 * Crypto master key regeneration library
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
    this.masterKeyGroupUsers = [];
};

/**
 * Extends crypto master key regeneration library prototype
 * @param {object} param1 Crypto library
 * @param {function} param2 Extended functionality
 */
$.extend(window.DxCryptoRegenClass.prototype, {
    /**
     * Catch error and stops process
     * @param {object} err Error object
     * @param {string} msg Error message if specified will be shown
     * @returns {undefined}
     */
    catchError: function (err, msg) {
        var self = window.DxCryptoRegen;

        self.resetRegenTempData();

        self.cancelProcess();

        $('#dx-crypto-modal-regen-masterkey').modal('hide');

        if (msg && msg != undefined) {
            window.DxCrypto.catchError(err, msg);
        } else {
            window.DxCrypto.catchError(err);
        }
    },
    /**
     * Resets all data to initialization state. Resets modal window data
     * @returns {undefined}
     */
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
     * @returns {Boolean} Return false if function failed or has been stopped
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
                    if (res.msg) {
                        window.DxCryptoRegen.catchError(null, res.msg);
                    } else {
                        window.DxCryptoRegen.catchError(null);
                    }
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
                    if (res.msg) {
                        window.DxCryptoRegen.catchError(null, res.msg);
                    } else {
                        window.DxCryptoRegen.catchError(null);
                    }
                }
            },
            error: function (err) {
                window.DxCryptoRegen.catchError(err);
            }
        });
    },
    /**
     * Iterates data and reencrypt
     * @param {Array} pendingData Data which will be encrypted
     * @returns {undefined}
     */
    recryptData: function (pendingData) {
        var self = window.DxCryptoRegen;
        
        self.recryptDataRow(pendingData, 0);

        // All already done
        if (pendingData.length <= 0) {
            // Starts to wrap new master key with user public keys. First must retrieve all public keys from data base
            self.getAllUserPublicKeys();
        }
    },
    /**
     * Event after data row has been reencrypted. Proceeeds to next row or search for an other batch of data
     * @param {ArrayBuffer} resBuffer Encrypted value
     * @param {object} record Encryption cache row
     * @param {int} batchSize Size of batch (data which is encrypted in this iteration)
     * @returns {undefined}
     */
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
    /**
     * Recrypt value depending if ti is file or text
     * @param {Array} pendingData All data in current batch which is reencrypted
     * @param {int} rowNum Number for current row
     * @returns {Boolean} Return false if function failed or has been stopped
     */
    recryptDataRow: function (pendingData, rowNum) {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        if (!(rowNum in pendingData)) {
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
                            self.recryptDataRow(pendingData, ++rowNum);
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
                self.recryptDataRow(pendingData, ++rowNum);
            });
        }
    },
    /**
     * Decryptes value with old master key and encryptes it with new master key
     * @param {ArrayBuffer} oldValue Value which will be reencrypted
     * @param {function} callback Called after value is reencrypted
     * @returns {Boolean} Return false if function failed or has been stopped
     */
    encryptDecryptData: function (oldValue, callback) {
        var self = window.DxCryptoRegen;

        var counterBuffer = oldValue.subarray(0, 16);
        var resBuffer = oldValue.subarray(16, oldValue.length);

        if (!self.validateProgress()) {
            return false;
        }

        var newCounterBuffer = new Uint8Array(16);
        window.crypto.getRandomValues(newCounterBuffer);

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
                    return window.crypto.subtle.encrypt(
                            {
                                name: "AES-CTR",
                                //Don't re-use counters!
                                //Always use a new counter every time your encrypt!
                                counter: newCounterBuffer,
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

                    var resBuffer = new Uint8Array(encryptedValue.length + newCounterBuffer.length);
                    resBuffer.set(newCounterBuffer);
                    resBuffer.set(encryptedValue, newCounterBuffer.length);

                    callback(resBuffer);
                })
                .catch(window.DxCryptoRegen.catchError);
    },
    /**
     * Post proceessed data to server. If there is more data to reencrypt then gets next batch else finished process by applying reencrypted data from cache
     * @returns {Boolean} Return false if function failed or has been stopped
     */
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
    /**
     * Start to cancel process because it is async we need to wait until last async operation finishes
     * @returns {undefined}
     */
    cancelProcess: function () {
        var self = window.DxCryptoRegen;

        self.regenCancel = true;
        self.updateRegenStatus(Lang.get('crypto.i_cancel_regen_process'));
    },
    /**
     * Validate if process can continue. If process has been canceled then close modal progress window.
     * @returns {Boolean} Return false if function failed or has been stopped
     */
    validateProgress: function () {
        if (window.DxCryptoRegen.regenCancel) {
            var modal = $('#dx-crypto-modal-regen-masterkey');

            modal.modal('hide');

            return false;
        }

        return true;
    },
    /**
     * Updates progress bar in modal window.
     * @returns {undefined}
     */
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
    /**
     * Change statuss text for modal window
     * @param {string} text Text to display in modal window
     * @returns {undefined}
     */
    updateRegenStatus: function (text) {
        var modal = $('#dx-crypto-modal-regen-masterkey');

        modal.find('.dx-crypto-modal-regen-progress-label').html(text);
    },
    /**
     * Retrieves all public keys from server
     * @returns {Boolean} Return false if function failed or has been stopped
     */
    getAllUserPublicKeys: function () {
        var self = window.DxCryptoRegen;

        if (!self.validateProgress()) {
            return false;
        }

        self.updateRegenStatus(Lang.get('crypto.i_gathering_certs'));

        $.ajax({
            url: DX_CORE.site_url + 'crypto/get_user_public_keys/' + self.masterKeyGroupId,
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
    /**
     * Wrapps new master key with each user's public key
     * @param {Array} userKeyData Public keys for each user
     * @param {int} i Iteration number
     * @returns {Boolean} Return false if function failed or has been stopped
     */
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
    /**
     * Sends wrapped master keys to server and applies reencrption cache
     * @returns {Boolean} Return false if function failed or has been stopped
     */
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
    },
    /**
     * Catch dom changes and fires event
     * @param {DOM} obj DOM object to watch over
     * @param {function} callback Function what will be called on changes
     * @returns {undefined}
     */
    observeDOM: function (obj, callback) {
        var MutationObserver = window.MutationObserver || window.WebKitMutationObserver,
                eventListenerSupported = window.addEventListener;

        if (MutationObserver) {
            // define a new observer
            var obs = new MutationObserver(function (mutations, observer) {
                if (mutations[0].addedNodes.length || mutations[0].removedNodes.length)
                    callback();
            });
            // have the observer observe foo for changes in children
            obs.observe(obj, {childList: true, subtree: true});
        } else if (eventListenerSupported) {
            obj.addEventListener('DOMNodeInserted', callback, false);
            obj.addEventListener('DOMNodeRemoved', callback, false);
        }
    },
    /**
     * Checks if there has been removed user from table
     * @param {DOM} form_object Form object
     * @param {int} masterkey_group_id Master key group ID
     * @returns {undefined}
     */
    checkForRemovedUsers: function (form_object, masterkey_group_id) {
        var self = window.DxCryptoRegen;

        var newMasterKeyUser = self.retrieveUsersFromMasterKeyGroupTable(form_object);

        if (newMasterKeyUser.length > 1) {
            for (var i = 0; i < self.masterKeyGroupUsers.length; i++) {
                var itemValue = self.masterKeyGroupUsers[i];

                if ($.inArray(itemValue, newMasterKeyUser) == -1) {
                    self.masterKeyGroupUsers = newMasterKeyUser;
                    setTimeout(function () {
                        self.checkForRemovedUsers(form_object, masterkey_group_id);
                    }, 1000);

                    window.DxCryptoRegen.regenMasterKey(masterkey_group_id);

                    return false;
                }
            }
        }

        self.masterKeyGroupUsers = newMasterKeyUser;
        if (form_object.is(':visible')) {
            setTimeout(function () {
                if (form_object.is(':visible')) {
                    self.checkForRemovedUsers(form_object, masterkey_group_id);
                }
            }, 1000);
        }
    },
    /**
     * Retrieves users records from table
     * @param {DOM} form_object Form object
     * @returns {Array} Retrieves master key 
     */
    retrieveUsersFromMasterKeyGroupTable: function (form_object) {
        var userItems = [];

        var table = form_object.find('table[data-list_id]');

        table.find('tr').each(function () {
            var itemId = $(this).find('[dx_item_id]:first').attr('dx_item_id');
            userItems.push(itemId);
        });

        return userItems;
    },
    /**
     * Initializes master key group managing modal
     * @param {DOM} form_object Form object
     * @returns {undefined}
     */
    initGroupManageView: function (form_object) {
        var self = window.DxCryptoRegen;

        var masterkey_group_id = form_object.find("input[name=id]").val();

        var table = form_object.find('table[data-list_id]');

        if (table.length == 0) {
            setTimeout(function () {
                self.initGroupManageView(form_object);
            }, 1000);
            return;
        }

        self.masterKeyGroupUsers = self.retrieveUsersFromMasterKeyGroupTable(form_object);

        setTimeout(function () {
            self.checkForRemovedUsers(form_object, masterkey_group_id);
        }, 1000);

    }
});

window.DxCryptoRegen = new window.DxCryptoRegenClass();
