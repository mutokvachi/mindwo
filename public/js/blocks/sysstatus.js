window.DxSystemStatus = window.DxSystemStatus || {
    /**
     * Bloka unikālais identifikators
     */
    block_guid: '',
    /**
     * Sistēmas dati JSON string formātā
     */
    sys_statuses: '',
    /**
     * Atver sistēmas kartiņu
     * @param {integer} item_id
     * @returns {undefined}
     */
    openDialog: function(item_id) {
        var formData = new FormData();
        formData.append("param", "OBJ=SYSSTATUS");
        formData.append("item_id", item_id);

        var ajax_url = '/block_ajax';

        var request = new FormAjaxRequest(ajax_url, "", "", formData);
        request.progress_info = "Ielādē datus. Lūdzu, uzgaidiet...";

        request.callback = function(data)
        {
            $('#popup_window .modal-title').html("Sistēmas statuss");
            $("#popup_body").html(data.html);
            $('#popup_window').modal('show');
        };

        request.err_callback = function(err_txt)
        {
            $('#popup_window .modal-title').html("Sistēmas statuss");
            $("#popup_body").html('<p>Kļūda ielādējot sistēmas statusu</p>');
            $('#popup_window').modal('show');
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    },
    /**
     * Uzstāda galeriju bloka parametrus
     * @param {object} block Galerijas bloka elements
     * @returns {undefined}
     */
    setParameters: function(block) {
        window.DxSystemStatus.block_guid = block.attr('dx_block_guid');

        window.DxSystemStatus.sys_statuses = JSON.parse(block.attr('dx_sys_statuses'));
    },
    /**
     * Inicializē sistēmas statusu komponentes komponenti
     * @returns {undefined}
     */
    init: function() {
        // Iegūst sistēmas statusu bloku
        $(".dx-block-container-sysstatus[dx_block_init='0']").each(function() {
            // Iegūst parametrus
            window.DxSystemStatus.setParameters($(this));

            // Apstrādā sistēmas ikonu peles klikšķā notikumus
            for (var key in window.DxSystemStatus.sys_statuses) {
                $("#sysstatus-" + window.DxSystemStatus.block_guid + "-" + window.DxSystemStatus.sys_statuses[key].id).click((function(id) {
                    return function() {
                        window.DxSystemStatus.openDialog(id);
                    }
                })(window.DxSystemStatus.sys_statuses[key].id));
            }

            // Uzstāda pazīmi ka bloks ir inicializēts
            $(this).attr('dx_block_init', 1);
        });
    }
}

$(document).ready(function() {
    window.DxSystemStatus.init();
});
