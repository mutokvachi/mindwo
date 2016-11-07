!function(t){t.fn.FreeForm=function(e){return this.each(function(){new t.FreeForm(this)})},t.FreeForm=function(e){t.data(e,"FreeForm",this);var i=this;this.root=t(e),this.fields=t("[data-name]",this.root),this.originalData={},this.editButton=t(".dx-edit-general",this.root),this.saveButton=t(".dx-save-general",this.root),this.cancelButton=t(".dx-cancel-general",this.root),this.editButton.click(function(){i.edit()}),this.saveButton.click(function(){i.save()}),this.cancelButton.click(function(){i.cancel()})},t.extend(t.FreeForm.prototype,{edit:function(){var e=this,i={model:this.root.data("model"),item_id:this.root.data("item_id"),list_id:this.root.data("list_id"),fields:[]};this.fields.each(function(){e.originalData[t(this).data("name")]=t(this).html(),i.fields.push({name:t(this).data("name")})}),show_page_splash(1),t.ajax({type:"POST",url:DX_CORE.site_url+"freeform/"+i.item_id+"/edit",dataType:"json",data:i,success:function(i){if("undefined"!=typeof i.success&&0==i.success)return notify_err(i.error),void hide_page_splash(1);e.editButton.hide(),e.saveButton.show(),e.cancelButton.show();for(var s=0;s<i.fields.length;s++){var o=i.fields[s].name,a=i.fields[s].input,n=t('[data-name="'+o+'"]',e.root);n.length&&n.html(a)}hide_page_splash(1)},error:function(t,e,i){console.log(e),console.log(t),hide_page_splash(1)}})},save:function(){var e=this,i={model:this.root.data("model"),item_id:this.root.data("item_id"),list_id:this.root.data("list_id"),fields:[]};this.fields.each(function(){i.fields.push({name:t(this).data("name"),data:t(this).find("[name]").val()})}),show_page_splash(1),t.ajax({type:"POST",url:DX_CORE.site_url+"freeform/"+i.item_id+"?_method=PUT",dataType:"json",data:i,success:function(i){if("undefined"!=typeof i.success&&0==i.success)return notify_err(i.error),void hide_page_splash(1);e.editButton.show(),e.saveButton.hide(),e.cancelButton.hide();for(var s=0;s<i.fields.length;s++){var o=i.fields[s].name,a=i.fields[s].html,n=t('[data-name="'+o+'"]',e.root);n.length&&n.html(a)}hide_page_splash(1)},error:function(t,e,i){console.log(e),console.log(t),hide_page_splash(1)}})},cancel:function(){var e=this;this.editButton.show(),this.saveButton.hide(),this.cancelButton.hide(),this.fields.each(function(){t(this).html(e.originalData[t(this).data("name")])})}})}(jQuery),function(t){t.fn.InlineForm=function(e){var i=t.extend({},t.fn.InlineForm.defaults,e);return this.each(function(){new t.InlineForm(this,i)})},t.fn.InlineForm.defaults={beforeSave:null,afterSave:null,empl_search_page_url:"/search"},t.InlineForm=function(e,i){t.data(e,"InlineForm",this);var s=this;this.options=i,this.root=t(e),this.tabs=t(".tab-pane",this.root),this.originalTabs={},this.editButton=t(".dx-edit-profile",this.root),this.saveButton=t(".dx-save-profile",this.root),this.cancelButton=t(".dx-cancel-profile",this.root),this.deleteButton=t(".dx-delete-profile",this.root),this.requests,this.onRequestSuccess,this.onRequestFailed,this.editButton.click(function(){s.edit()}),this.saveButton.click(function(){s.save()}),this.cancelButton.click(function(){s.cancel()}),this.deleteButton.click(function(){s.destroy()})},t.extend(t.InlineForm.prototype,{initRequest:function(t){this.requests={total:t,succeeded:0,failed:0},this.onRequestSuccess=[],this.onRequestFailed=[]},setRequestStatus:function(t){if(t?this.requests.succeeded++:this.requests.failed++,this.requests.total===this.requests.succeeded+this.requests.failed){if(0===this.requests.failed)for(var e=0;e<this.onRequestSuccess.length;e++)this.onRequestSuccess[e].func(this.onRequestSuccess[e].args);else for(var e=0;e<this.onRequestFailed.length;e++)this.onRequestFailed[e].func(this.onRequestFailed[e].args);hide_page_splash(1)}},edit:function(){var e=this,i={list_id:this.root.data("list_id"),tab_list:[]};this.tabs.each(function(){e.originalTabs[t(this).data("tabTitle")]=t(this).html()}),show_page_splash(1),t.ajax({type:"POST",url:DX_CORE.site_url+"inlineform/"+this.root.data("item_id")+"/edit",dataType:"json",data:i,success:function(i){if("undefined"!=typeof i.success&&0==i.success)return notify_err(i.error),void hide_page_splash(1);e.editButton.hide();for(var s=t(t.parseHTML("<div>"+i.tabs+"</div>")).find(".tab-pane"),o=0;o<s.length;o++){var a=t(s[o]),n=t('[data-tab-title="'+a.data("tabTitle")+'"]',e.root);n.length&&n.html(a.html())}hide_page_splash(1),t(".dx-stick-footer").show()},error:function(t,e,i){console.log(e),console.log(t),hide_page_splash(1)}}),window.DxEmpPersDocs.toggleDisable(!1)},save:function(){var e=this,i=process_data_fields(this.root.attr("id"));i.append("item_id",this.root.data("item_id")),i.append("list_id",this.root.data("list_id")),i.append("edit_form_id",this.root.data("form_id")),i.append("redirect_url",this.root.data("redirect_url"));var s=DX_CORE.site_url+"inlineform";"create"!=this.root.data("mode")&&(s+="/"+this.root.data("item_id")+"?_method=PUT"),show_page_splash(1),this.initRequest(2),t.ajax({type:"POST",url:s,dataType:"json",processData:!1,contentType:!1,data:i,success:function(i){e.onRequestSuccess.push({func:function(i){if("undefined"!=typeof i.success&&0==i.success)return notify_err(i.error),void hide_page_splash(1);if("create"==e.root.data("mode"))return void(window.location=i.redirect);e.editButton.show();for(var s=t(t.parseHTML("<div>"+i.tabs+"</div>")).find(".tab-pane"),o=0;o<s.length;o++){var a=t(s[o]),n=t('[data-tab-title="'+a.data("tabTitle")+'"]',e.root);n.length&&n.html(a.html())}e.options.afterSave&&e.options.afterSave(),hide_page_splash(1),t(".dx-stick-footer").hide()},args:i}),e.setRequestStatus(!0)},error:function(t,i,s){console.log(i),console.log(t),e.setRequestStatus(!1)}}),window.DxEmpPersDocs.onClickSaveDocs(function(){e.onRequestSuccess.push({func:function(){window.DxEmpPersDocs.toggleDisable(!0)},args:null}),e.setRequestStatus(!0)})},cancel:function(){if("create"==this.root.data("mode"))return show_page_splash(1),void(window.location=this.options.empl_search_page_url);this.editButton.show();for(var e in this.originalTabs)this.tabs.filter('[data-tab-title="'+e+'"]').html(this.originalTabs[e]);t(".dx-stick-footer").hide(),window.DxEmpPersDocs.cancelEditMode()},destroy:function(){if(confirm(Lang.get("frame.confirm_delete"))){var e={edit_form_id:this.root.data("form_id"),item_id:this.root.data("item_id")};show_page_splash(1),t.ajax({type:"POST",url:DX_CORE.site_url+"inlineform/"+this.root.data("item_id")+"?_method=DELETE",dataType:"json",data:e,success:function(t){return"undefined"!=typeof t.success&&0==t.success?(notify_err(t.error),void hide_page_splash(1)):void(window.location=t.redirect)},error:function(t,e,i){console.log(e),console.log(t),hide_page_splash(1)}})}}})}(jQuery),function(t){t.fn.EmplLinksFix=function(e){var i=t.extend({},t.fn.EmplLinksFix.defaults,e);return this.each(function(){new t.EmplLinksFix(this,i)})},t.fn.EmplLinksFix.defaults={profile_url:"/employee/profile/"},t.EmplLinksFix=function(e,i){t.data(e,"EmplLinksFix",this);var s=this;this.options=i,this.root=t(e),this.flds_managers=t(".dx-autocompleate-field[data-item-field=manager_id], .dx-autocompleate-field[data-item-field=reporting_manager_id]",this.root),this.tiles_managers=t(".employee-manager-tile",this.root),this.tiles_managers.each(function(){t(this).data("is-fix-init")||(t(this).click(function(){s.show_tile_manager(t(this).data("empl-id"))}),t(this).data("is-fix-init",1))}),this.flds_managers.each(function(){if(!t(this).data("is-fix-init")){var e=t(this);e.find(".dx-rel-id-add-btn").off("click"),e.find(".dx-rel-id-add-btn").click(function(){s.show_manager(e)}),e.find(".dx-rel-id-add-btn").tooltipster("destroy"),e.find(".dx-rel-id-add-btn").attr("title",Lang.get("empl_profile.hint_view_profile")),e.find(".dx-rel-id-add-btn").tooltipster({theme:"tooltipster-light",animation:"grow",maxWidth:300}),t(this).data("is-fix-init",1)}})},t.extend(t.EmplLinksFix.prototype,{show_manager:function(t){var e=t.find(".dx-auto-input-id").val();0!=e&&(show_page_splash(1),window.location=this.options.profile_url+e)},show_tile_manager:function(t){show_page_splash(1),window.location=this.options.profile_url+t}})}(jQuery),$(document).ajaxComplete(function(){$(".dx-employee-profile.freeform").EmplLinksFix()}),$(document).ready(function(){$(".dx-employee-profile.freeform").EmplLinksFix()});