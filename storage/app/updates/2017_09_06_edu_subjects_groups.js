form_object.find(".dx-form-field-line[dx_fld_name_form=is_inner_group]").css({
    "border": "1px solid gray", 
    "padding": "4px 10px 10px 10px",
    "background-color": "#e2b3b8",
    "margin-top": "-6px"
});

var sel = form_object.find("[name=is_inner_group]");
if (sel){
	var cur_val = 0;
	if (sel.is(':checked')){
		cur_val = 1;
	}
	
	var show_or_hide_rel_fields = function(val){	
        var tab = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_edu_subjects_groups.tab_orgs') + "')" ).closest("li");
        
        if (tab.hasClass("active")) {
            var tab_main = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_edu_subjects_groups.tab_main') + "')" );
            tab_main.tab('show');
        }

		if (val){
            tab.show();
		}
		else{
            tab.hide();
		}		
	};
	
	show_or_hide_rel_fields(cur_val);
  
    sel.on('switchChange.bootstrapSwitch', function(event, state) {   
        show_or_hide_rel_fields(state);        
    });
}

var pub = form_object.find("[name=is_published]");
var com = form_object.find("[name=is_complecting]");
var canc = form_object.find(".dx-form-field-line[dx_fld_name_form=canceled_time]").find("input");
var pub_dat = form_object.find("[name=first_publish]");

if (pub && com && canc) {

    // default status - Preparation
    var stat_text = Lang.get('db_edu_subjects_groups.status.prepare');
    var stat_color_bg = "#dcdcdc";
    var stat_color_fg = "black";
    var show_canc = false;

	if (parseInt(pub.val())){
        // Published
        stat_text = Lang.get('db_edu_subjects_groups.status.publish');
        stat_color_bg = "#cb4654";
        stat_color_fg = "white";
        show_canc = true;
    }

    if (parseInt(com.val())){
        // Complecting
        stat_text = Lang.get('db_edu_subjects_groups.status.complect');
        stat_color_bg = "#8ebae7";
        stat_color_fg = "black";
        show_canc = true;
    }

    if (canc.val()) {
        // Canceled
        stat_text = Lang.get('db_edu_subjects_groups.status.cancel');
        stat_color_bg = "black";
        stat_color_fg = "white";
        show_canc = true;
    }
    else {
        if (pub_dat.val() && !parseInt(pub.val())) {
            // correction
            stat_text = Lang.get('db_edu_subjects_groups.status.corect');
            stat_color_bg = "#d6df32";
            stat_color_fg = "black";
            show_canc = true;
        }
    }

    var tab_canc = form_object.find(".nav-tabs").find( ".dx-tab-link:contains('" + Lang.get('db_edu_subjects_groups.tab_cancel') + "')" ).closest("li");
    if (show_canc) {
        tab_canc.show();        
    }
    else {
        tab_canc.hide();
    }    
    
    form_object.find(".modal-body .dx-status-div").remove();

    $("<div>").text(stat_text).addClass("dx-status-div").css({
        "position": "absolute",
        "top": "5px",
        "right": "0",
        "padding": "5px",
        "margin-right": "35px",
        "border": "1px solid gray",
        "background-color": stat_color_bg,
        "color": stat_color_fg,
        "font-size": "11px"
    }).appendTo(form_object.find(".modal-body"));


}