(function ($) {
    /**
     * Creates jQuery plugin for crypto fields
     * @returns DxCryptoFileField
     */
    $.fn.DxCryptoFileField = function () {
        return this.each(function () {
            var self = $(this);

            if (self.data('dx_is_init') == 1) {
                return;
            }

            self.data('dx_is_init', 1);

            this.crypto = new $.DxCryptoFileField(self);
        });
    };

    /**
     * Class for managing crypto fields
     * @type DxCryptoFileField 
     */
    $.DxCryptoFileField = function (domObject) {
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
    $.extend($.DxCryptoFileField.prototype, {
        /**
         * Initializes field
         * @returns {undefined}
         */
        init: function () {
            var self = this;

            if (this.domObject.is('input')) {
                this.inputInit();
            } else if (this.domObject.is('a')) {
                this.linkInit();
            }
        },
        /**
         * Intializes input field
         * @returns {undefined}
         */
        inputInit: function () {

        },
        /**
         * Intializes link field
         * @returns {undefined}
         */
        linkInit: function () {
            // Overrides element click event
            this.domObject.click(this.onLinkClick);
        },
        /**
         * Calls decryption function
         * @param {object} event Link click event
         * @returns {undefined}
         */
        onLinkClick: function (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            show_page_splash(1);

            window.DxCrypto.decryptFields($(this));
        },
        /**
         * Sets access denied error for dom object
         * @returns {undefined}
         */
        setAccessError: function () {
            if (this.domObject.is('input')) {
                var parent = this.domObject.parent().parent();

                if (parent.hasClass('fileinput')) {
                    parent.fileinput('clear');
                } else {
                    this.domObject.val('');
                }
            }

            window.DxCrypto.catchError(null, Lang.get('crypto.e_no_access'));
        },
        /**
         * Gets value of current element. It can be input or other container (e.g. div, span)
         * @param {function} callback Callback function which will be called after value is retrieved
         * @returns {undefined}
         */
        getValue: function (callback) {
            if (this.domObject.is('input')) {
                this.getFileValue(callback);
            } else if (this.domObject.is('a')) {
                this.getLinkValue(callback);
            }
        },
        /**
         * Retrieve file which is input by user
         * @param {function} callback Callback function which will be called after file is retrieved
         * @returns {undefined}
         */
        getFileValue: function (callback) {
            if (this.domObject[0].files.length === 0) {
                return new ArrayBuffer(0);
            }

            var fr = new FileReader();
            fr.onload = function () {
                var data = fr.result;
                var dataArray = new Uint8Array(data);

                callback(dataArray);
            };
            fr.onerror = function () {
                hide_page_splash(1);
            }

            fr.readAsArrayBuffer(this.domObject[0].files[0]);
        },
        /**
         * Retrieves encrypted file which will be decrypted
         * @param {function} callback Callback function which will be called after file is retrieved
         * @returns {undefined}
         */
        getLinkValue: function (callback) {
            var xhr = new XMLHttpRequest();

            xhr.onload = function () {
                var reader = new FileReader();

                reader.readAsArrayBuffer(xhr.response);

                reader.onloadend = function () {

                    var arrayBuffer = new Uint8Array(reader.result);

                    callback(arrayBuffer, xhr.response.type);
                };
                reader.onerror = function () {
                    hide_page_splash(1);
                }

            };
            xhr.open('GET', this.domObject.attr("href"));
            xhr.responseType = 'blob';
            xhr.send();
        },
        /**
         * Sets value of current element. It can be input or other container (e.g. div, span)
         * @param {string} value It will be set to element
         * @returns {undefined}
         */
        setValue: function (value, fileType) {
            if (this.domObject.is('input')) {
                return this.setFileValue(value);
            } else if (this.domObject.is('a')) {
                return this.setLinkValue(value, fileType);
            }
        },
        /**
         * Sets encrypted file value which will be sent to server
         * @param {string} value Contains file blob
         * @returns {undefined}
         */
        setFileValue: function (value) {
            var valueBlob = new Blob([new Uint8Array(value)], { type: "application/octet-stream" });

            this.domObject.data('crypto-value', valueBlob);
        },
        /**
         * Sets link value blob and clicks on link
         * @param {string} value Contains file blob
         * @param {string} fileType File type
         * @returns {undefined}
         */
        setLinkValue: function (value, fileType) {
            var blob = new Blob([value], { type: fileType });
            var newUrl = URL.createObjectURL(blob);

            var a = document.createElement("a");
            a.style.display = "none";
            a.href = newUrl;
            a.download = this.domObject.text().trim();
            $("body").append($(a));
            a.click();
            window.URL.revokeObjectURL(newUrl);
            a.remove();
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes all found worklfow containers
    $('.dx-crypto-field-file').DxCryptoFileField();
});

$(document).ajaxComplete(function () {
    // Initializes all found worklfow containers
    $('.dx-crypto-field-file').DxCryptoFileField();
});
