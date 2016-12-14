var sel = form_object.find("[name=timeoff_type_id]");
var dat_from = form_object.find("[name=from_date]");
var dat_to = form_object.find("[name=to_date]");
var details = form_object.find("[name=request_details]");

var concatDetails = function()
{
    var type = sel.find("option:selected").text();
    
    details.val(type + ': ' + dat_from.val() + ' - ' + dat_to.val());		
}

sel.on('change', concatDetails);

dat_from.datetimepicker({
  onChangeDateTime:function(dp,$input){
    concatDetails();
  }
});

dat_to.datetimepicker({
  onChangeDateTime:function(dp,$input){
    concatDetails();
  }
});