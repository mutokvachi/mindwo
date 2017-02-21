!function(e){e.fn.extend({simulate:function(t,n){return this.each(function(){var i=e.extend({},e.simulate.defaults,n||{});new e.simulate(this,t,i)})}}),e.simulate=function(e,t,n){this.target=e,this.options=n,/^drag$/.test(t)?this[t].apply(this,[this.target,n]):this.simulateEvent(e,t,n)},e.extend(e.simulate.prototype,{simulateEvent:function(e,t,n){var i=this.createEvent(t,n);return this.dispatchEvent(e,t,i,n),i},createEvent:function(e,t){return/^mouse(over|out|down|up|move)|(dbl)?click$/.test(e)?this.mouseEvent(e,t):/^key(up|down|press)$/.test(e)?this.keyboardEvent(e,t):void 0},mouseEvent:function(t,n){var i,a=e.extend({bubbles:!0,cancelable:"mousemove"!=t,view:window,detail:0,screenX:0,screenY:0,clientX:0,clientY:0,ctrlKey:!1,altKey:!1,shiftKey:!1,metaKey:!1,button:0,relatedTarget:void 0},n);e(a.relatedTarget)[0];return e.isFunction(document.createEvent)?(i=document.createEvent("MouseEvents"),i.initMouseEvent(t,a.bubbles,a.cancelable,a.view,a.detail,a.screenX,a.screenY,a.clientX,a.clientY,a.ctrlKey,a.altKey,a.shiftKey,a.metaKey,a.button,a.relatedTarget||document.body.parentNode)):document.createEventObject&&(i=document.createEventObject(),e.extend(i,a),i.button={0:1,1:4,2:2}[i.button]||i.button),i},keyboardEvent:function(t,n){var i,a=e.extend({bubbles:!0,cancelable:!0,view:window,ctrlKey:!1,altKey:!1,shiftKey:!1,metaKey:!1,keyCode:0,charCode:0},n);if(e.isFunction(document.createEvent))try{i=document.createEvent("KeyEvents"),i.initKeyEvent(t,a.bubbles,a.cancelable,a.view,a.ctrlKey,a.altKey,a.shiftKey,a.metaKey,a.keyCode,a.charCode)}catch(o){i=document.createEvent("Events"),i.initEvent(t,a.bubbles,a.cancelable),e.extend(i,{view:a.view,ctrlKey:a.ctrlKey,altKey:a.altKey,shiftKey:a.shiftKey,metaKey:a.metaKey,keyCode:a.keyCode,charCode:a.charCode})}else document.createEventObject&&(i=document.createEventObject(),e.extend(i,a));return void 0!==e.browser&&(e.browser.msie||e.browser.opera)&&(i.keyCode=a.charCode>0?a.charCode:a.keyCode,i.charCode=void 0),i},dispatchEvent:function(e,t,n){return e.dispatchEvent?e.dispatchEvent(n):e.fireEvent&&e.fireEvent("on"+t,n),n},drag:function(e){var t=this.findCenter(this.target),n=this.options,i=Math.floor(t.x),a=Math.floor(t.y),o=n.dx||0,s=n.dy||0,c=this.target,r={clientX:i,clientY:a};this.simulateEvent(c,"mousedown",r),r={clientX:i+1,clientY:a+1},this.simulateEvent(document,"mousemove",r),r={clientX:i+o,clientY:a+s},this.simulateEvent(document,"mousemove",r),this.simulateEvent(document,"mousemove",r),this.simulateEvent(c,"mouseup",r)},findCenter:function(t){var t=e(this.target),n=t.offset();return{x:n.left+t.outerWidth()/2,y:n.top+t.outerHeight()/2}}}),e.extend(e.simulate,{defaults:{speed:"sync"},VK_TAB:9,VK_ENTER:13,VK_ESC:27,VK_PGUP:33,VK_PGDN:34,VK_END:35,VK_HOME:36,VK_LEFT:37,VK_UP:38,VK_RIGHT:39,VK_DOWN:40})}(jQuery);var SearchTools=function(){var e=null,t=null,n=null,i=function(){var i=e.find(".search-tools-btn");if(i.find("i").hasClass("fa-caret-down"))i.find("i").removeClass("fa-caret-down").addClass("fa-caret-up"),i.removeClass("btn-primary").addClass("btn-default"),e.removeClass("search-tools-hiden").addClass("search-tools-shown");else{i.find("i").removeClass("fa-caret-up").addClass("fa-caret-down"),i.removeClass("btn-default").addClass("btn-primary"),e.removeClass("search-tools-shown").addClass("search-tools-hiden");var a=e.find("input[name=criteria]").val();n(),e.find("input[name=criteria]").val(a),t()}},a=function(){e.find(".search-simple-btn").click(function(t){show_page_splash(),e.submit()})},o=function(){e.find(".search-tools-btn").click(function(e){i()})},s=function(i,s){e=$(".search-tools-form"),t=i,n=s,a(),o()};return{init:function(e,t){s(e,t)},showHideTools:function(){i()}}}(),PageEmployees=function(){var e=null,t=null,n=function(e,t,n){var i=e.find(t);return i.length?i.val():n},i=function(){var e=t.find(".dx-tree-value-choose-btn");n(t,"select[name=source_id]",0)>0?e.hasClass("tooltipstered")&&e.tooltipster("disable"):e.hasClass("tooltipstered")?e.tooltipster("enable"):e.tooltipster({theme:"tooltipster-light",animation:"grow",content:t.attr("trans_unit_hint")})},a=function(){n(t,"select[name=source_id]",0)>0||n(t,"input[name=department]","")||n(t,"input[name=criteria]","")||n(t,"input[name=phone]","")||n(t,"input[name=position]","")?t.find(".dx-clear-link").show():t.find(".dx-clear-link").hide()},o=function(){e.find("input[name=criteria]").val(""),e.find("select[name=source_id]").val(0),e.find("input[name=department]").val(""),e.find("input[name=position]").val(""),e.find("input[name=phone]").val(""),e.find("input[name=department_id]").val(0),i()},s=function(){t.find(".dx-tree-value-choose-btn").click(function(e){var n=t.find("select[name=source_id]").val();return 0==n?(t.find("select[name=source_id]").focus(),void t.find("select[name=source_id]").simulate("mousedown")):void get_popup_item_by_id_ie9(n,"ajax/departments",t.find("select[name=source_id] option:selected").text()+" - "+t.attr("trans_choosing_unit"),p)}),t.find("input[name=department]").change(function(e){t.find("input[name=department_id]").val(0)})},c=function(){t.find(".dx-tree-value-clear-btn").click(function(e){t.find("input[name=department]").val(""),t.find("input[name=department_id]").val(0),a(t)})},r=function(){t.find(".dx-clear-link").click(function(){o(),a()})},d=function(){e.find("input").keyup(function(){a()}),e.find("select").change(function(){a()})},l=function(){e.find("select[name=source_id]").change(function(){i()})},u=function(){t.find(".dx-employee-new-add-btn").click(function(){new_list_item(t.attr("dx_empl_list_id"),0,0,"","")})},m=function(){(n(t,"select[name=source_id]",0)>0||n(t,"input[name=department]","")||n(t,"input[name=phone]","")||n(t,"input[name=position]"))&&SearchTools.showHideTools()},f=function(){t=$(".dx-employees-page"),e=t.find(".search-tools-form"),SearchTools.init(a,o),s(),c(),EmployeesLinks.init(t,o),d(),r(),l(),a(),m(),i(),u()},p=function(){$(".dx-department-tree-container").on("select_node.jstree",function(e,t){var n,i;for(n=0,i=t.selected.length;n<i;n++){var a=t.instance.get_node(t.selected[n],!0);$(".dx-employees-page input[name=department]").val(a.text()),$(".dx-employees-page input[name=department_id]").val(a.attr("data-id")),$("#popup_window").modal("hide");break}}).jstree({core:{multiple:!1}})};return{init:function(){f()},initTree:function(){p()}}}();$(document).ready(function(){PageEmployees.init()}),$(document).ajaxComplete(function(e,t,n){PageEmployees.initTree()});