(function (form_object) {
    var btnContainer = form_object.find(".dx_form_btns_left");

    if (btnContainer && $("#dx-crypto-masterkey-regen-" + form_object.attr('id')).length == 0) {
        btnContainer.append("<button id='dx-crypto-masterkey-regen-" + form_object.attr('id') + "' type='button' class='btn btn-white'><i class='fa fa-exclamation-circle'></i> " + Lang.get('crypto.btn_regen_masterkey') + "</button>");
    }

    var btnRegen = $("#dx-crypto-masterkey-regen-" + form_object.attr('id'));

    btnRegen.click(function () {
        var masterkey_group_id = form_object.find("input[name=id]").val();

        window.DxCrypto.regenMasterKey(masterkey_group_id);
    });
})(form_object);