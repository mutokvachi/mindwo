(function (form_object) {
    var btnSave = $('.dx-btn-save-form', form_object);

    btnSave.click(function (event) {
        window.DxCrypto.onMasterKeysSave(event, form_object, false);
    });
})(form_object);