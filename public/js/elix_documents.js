var PageDocuments=function(){var e=null,t=null,n=function(){getElementVal(t,"select[name=source_id]",0)>0||getElementVal(t,"select[name=kind_id]",0)>0||getElementVal(t,"input[name=criteria]","")||getElementVal(t,"input[name=pick_date_from]","")||getElementVal(t,"input[name=pick_date_to]","")?t.find(".dx-clear-link").show():t.find(".dx-clear-link").hide()},a=function(){e.find("input[name=pick_date_from]").val(""),e.find("input[name=pick_date_to]").val(""),e.find("input[name=criteria]").val(""),e.find("select[name=source_id]").val(0),e.find("select[name=kind_id]").val(0),e.find("#defaultrange input").val("")},i=function(){t.find(".dx-clear-link").click(function(){a(),n()})},o=function(){e.find("input").keyup(function(){n()}),e.find("select").change(function(){n()})},r=function(){$("a.dx-lotus-btn").click(function(){view_list_item("form",$(this).attr("item_id"),$(this).attr("list_id"),0,0,"","")})},m=function(){(getElementVal(t,"select[name=source_id]",0)>0||getElementVal(t,"select[name=kind_id]",0)>0||getElementVal(t,"input[name=pick_date_from]","")||getElementVal(t,"input[name=pick_date_to]",""))&&SearchTools.showHideTools()},l=function(){var e={range_id:"defaultrange",el_date_from:"pick_date_from",el_date_to:"pick_date_to",page_selector:".dx-documents-page",form_selector:".search-tools-form",arr_ranges:{}};e.arr_ranges[Lang.get("date_range.flt_today")]=[moment(),moment()],e.arr_ranges[Lang.get("date_range.flt_yesterday")]=[moment().subtract("days",1),moment().subtract("days",1)],e.arr_ranges[Lang.get("date_range.flt_7")]=[moment().subtract("days",6),moment()],e.arr_ranges[Lang.get("date_range.flt_thism")]=[moment().startOf("month"),moment().endOf("month")],e.arr_ranges[Lang.get("date_range.flt_prevm")]=[moment().subtract("month",1).startOf("month"),moment().subtract("month",1).endOf("month")],DateRange.init(e,n)},d=function(){t=$(".dx-documents-page"),e=t.find(".search-tools-form"),SearchTools.init(n,a),o(),i(),n(),m(),l(),r()};return{init:function(){d()}}}();$(document).ready(function(){PageDocuments.init()});