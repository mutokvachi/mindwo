var btns_div = form_object.find(".dx_form_btns_left");

var make_button = function () {

    if ($("#dx-btn-wf-designer" + form_object.attr('id')).length != 0) {
        return; // poga jau ir pievienota
    }

    btns_div.append("<button id='dx-btn-wf-designer" + form_object.attr('id') + "' type='button' class='btn btn-white dropdown-toggle' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'><i class='fa fa-eye'></i> " + Lang.get('workflow.open_designer') + " </button>");

    $("#dx-btn-wf-designer" + form_object.attr('id')).click(function () {
        var item_id = form_object.find("input[name=id]").val();
        var item_url = '/workflow/visual/form';
        var item_title = Lang.get('workflow.form_title');

        get_popup_item_by_id(item_id, item_url, item_title);
    });
};

if (btns_div)
{
    make_button();
}