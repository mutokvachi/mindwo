(function (form_object) {
    var btnSave = form_object.find('.dx-btn-save-form');

    window.DxCryptoRegen.saveCryptoBtnState(form_object);

    btnSave.click(function (event) {
        window.DxCryptoRegen.onSaveField(event, form_object);
    });
})(form_object);