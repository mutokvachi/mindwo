var SearchTop=function(){var e="",t="Nav atrasts neviens atbilstošs ieraksts.",i="Lūdzu, ievadiet vismaz trīs burtus, un tiks veikta dinamiskā darbinieku meklēšana.",a="Darbinieki",n="Meklē....",s=0,r=1,o=$("#quick-search-status"),c=function(){var e=$("#search_criteria").val().trim();if(""==e)return $(".dx-employees-quick-results").html(""),void o.html(i);if(e.length<3)return $(".dx-employees-quick-results").html(""),void o.html(i);var a="criteria="+e,s="ajax/employees";$(".dx-employees-quick-results").html(""),o.html(n);var r=new FormAjaxRequestIE9(s,"","",a);r.progress_info="",r.callback=function(e){e.html.indexOf("employee-list")!==-1?($(".dx-employees-quick-results").html(e.html),EmployeesLinks.init($("#dx-top-search-div").find(".employee-list"),l),o.html("")):o.html(t)},r.doRequest()},l=function(){$("#search_criteria").val("")},u=function(){$("#top_search a").each(function(){$(this).click(function(t){$("#search_title").html($(this).text()),$("#searchType").val($(this).text()),e=$(this).text(),$("#search_criteria").focus()})})},d=function(){$("#top_search a").each(function(){$(this).click(function(t){var i=$("span",this);$("#search_title").html(i.text()),$("#search_dropd").find("span").children().replaceWith($("i",this).clone()),$("#searchType").val(i.text()),e=i.text(),$("#search_criteria").attr("placeholder",i.data("placeholder")).focus()})})},p=function(){$("#search_btn").click(function(){show_page_splash()})},f=function(){$("body").toggleClass("page-quick-sidebar-open"),s=0,$(".dx-employees-quick-results").html(""),o.html(i)},h=function(){$("#search_criteria").on("keyup",function(t){e==a?0==s&&1==r&&($("body").toggleClass("page-quick-sidebar-open"),s=1,""!=$("#search_criteria").val().trim()&&c()):1==s&&f()})},m=function(){$("a.close-quick-sidebar").click(function(){1==s&&($("body").toggleClass("page-quick-sidebar-open"),s=0)})},_=function(){$("#search_criteria").keyup(function(){s&&c()})},x=function(){$("#search_criteria").bind("paste",function(){e==a&&setTimeout(function(){c()},100)})},k=function(){var e=760;0==$("#employee_quick_css").length&&$("head").append('<style type="text/css" id="employee_quick_css"></style>');var t=$("#employee_quick_css");t.html(".page-quick-sidebar-wrapper{width:"+e+"px; right: -"+e+"px;}"),$.isFunction($.fn.slimScroll)&&($(".dx-employees-quick .slimScrollDiv").length&&$(".dx-employees-quick-results").slimScroll({destroy:!0}),$(".dx-employees-quick-results").slimScroll({height:$(".dx-employees-quick").height()+"px",position:"left",color:"#f1f4f7"}))},v=function(){if($(document).width()>990){if(r)return;var e=$("#dx-search-box-in-page").html();$("#dx-search-box-in-page").html(""),$("#dx-search-box-top-li").html(e),r=1,g()}else{if(!r)return;var e=$("#dx-top-search-form").parent().html();$("#dx-top-search-form").remove(),$("#dx-search-box-in-page").html(e),r=0,g(),1==s&&f()}},g=function(){dx_is_cssonly?d():u(),p(),k(),h(),m(),_(),x()},y=function(){var e=$("#dx-top-search-div");t=e.attr("trans_nothing_found"),i=e.attr("trans_default_info"),a=e.attr("trans_employees"),n=e.attr("trans_searching")},b=function(){y(),g();var t=$("#dx-top-search-div");$("#searchType").val(t.attr("trans_default")),e=t.attr("trans_default"),dx_is_cssonly||v(),PageMain.addResizeCallback(k),dx_is_cssonly||PageMain.addResizeCallback(v)};return{init:function(){b()}}}();$(document).ready(function(){$("[autofocus]:not(:focus)").eq(0).focus(),SearchTop.init()});var UserLinks=function(){var e=null,t=function(){var t=new FormData;t.append("pass_old",e.find("input[name='pass_old']").val()),t.append("pass_new1",e.find("input[name='pass_new1']").val()),t.append("pass_new2",e.find("input[name='pass_new2']").val());var i="/ajax/change_password",a=new FormAjaxRequest(i,"","",t);a.progress_info=DX_CORE.trans_data_processing,a.callback=function(e){notify_info(DX_CORE.trans_data_saved)},a.doRequest()},i=function(){e.validator({errors:{auto:"Nav norādīta vērtība!"},feedback:{success:"glyphicon-ok",error:"glyphicon-alert"}})},a=function(){$("a.dx-user-change-passw-link").click(function(){get_popup_item_by_id(0,"ajax/form_password",DX_CORE.trans_passw_form_title)})},n=function(){e.find(".dx-user-change-passw-btn").click(function(){t()})},s=function(){$.isFunction($.fn.validator)?i():$.getScript(getBaseUrl()+"plugins/validator/validator.js",i)},r=function(){a()},o=function(){0!=$(".dx-user-change-passw-form[dx_block_init='0']").length&&(e=$(".dx-user-change-passw-form"),n(),s(),e.attr("dx_block_init",1),setTimeout(function(){e.find("input[name='pass_old']").focus()},500))};return{init:function(){r()},initPasswForm:function(){o()}}}();$(document).ready(function(){UserLinks.init()}),$(document).ajaxComplete(function(e,t,i){UserLinks.initPasswForm()});