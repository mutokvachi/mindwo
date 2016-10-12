/**
 * Daudzlīmeņa lauka apstrāde (koka ielāde)
 * 
 * @type _L4.Anonym$0|Function
 */
var TreeField = function()
{   
    /**
     * Pašreiz ielādētā lauka elements (lapā var būt ielādēti vairāki daudzlīmeņu lauki vienlaicīgi)
     */
    var current_fld = null;
    
    /**
     * Kokā izvēlētā ieraksta ID
     */
    var selected_id = 0;
    
    /**
     * Pazīme, vai ir tikusi inicializēta koka forma (tā jāinicializē tikai vienu reizi)
     */
    var is_tree_form_set = 0;
    
    /**
     * Pašreiz ielādētā lauka identifikators
     */
    var current_fld_id = "";
    
    /**
     * Nodrošina kokā nospiestā ieraksta izvēli (uzstāda kā izvēlēto ierakstu)
     */
    var handleNodeSelect = function() {
        $('#tree_form .dx-tree-content').on("changed.jstree", function (e, data) {
            $('#tree_form_txt').html(data.instance.get_node(data.selected[0], true).attr("data-full"));
            selected_id = data.instance.get_node(data.selected[0], true).attr("data-id");
        });
    };
    
    /**
     * Notīra lauka vērtības - pēc attiecīgās pogas nospiešanas
     * @param {Object} fld_elem Lauka elements
     * @returns {undefined}
     */
    var handleBtnDel = function(fld_elem) {
        fld_elem.find(".dx-tree-btn-del").click(function() {
            fld_elem.find(".dx-tree-txt-visible").val('');
            fld_elem.find(".dx-tree-txt-hidden").val('');  
        });
    };
    
    /**
     * Apstrādā koka formas atvēršanas pogas nospiešanu
     */
    var handleBtnAdd = function(fld_elem) {
        fld_elem.find(".dx-tree-btn-add").click(function() {
            
            if (current_fld_id != fld_elem.attr('dx_tree_id')) {
                
                $('#tree_form .dx-tree-frame').html("<div class='dx-tree-content'></div>");
                
                $('#tree_form .dx-tree-content').html(fld_elem.attr('dx_tree_content'));
                $('#tree_form .modal-title').html(fld_elem.attr('dx_tree_title'));
                $("#tree_form_txt").html(fld_elem.attr('dx_tree_default_node_txt'));
                selected_id = fld_elem.attr('dx_tree_item_value');            
                current_fld = fld_elem;
            
                $('#tree_form .dx-tree-content').jstree({
                    "core" : {"multiple" : false }
                });
                
                handleNodeSelect();
                
                current_fld_id = fld_elem.attr('dx_tree_id');
                
            }
            
            $('#tree_form').modal('show'); 
        });
    };
    
    /**
     * Apstrādā kokā izvēlēta ieraksta piešķiršanu formas laukam.
     * Uzstāda formā redzamo tekstuālo vērtību un neredzamo ID vērtību
     */
    var handleBtnChoose = function() {
        $('#tree_form .dx-tree-btn-choose').click(function() {
            current_fld.find(".dx-tree-txt-visible").val($('#tree_form_txt').text());
            current_fld.find(".dx-tree-txt-hidden").val(selected_id);
            $('#tree_form').modal('hide'); 
        });
    };
    
    /**
     * Izveido koka formu no HTML.
     * Šajā funkcijā izmantota iespēja rakstīt tekstu vairākās līnijās: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Template_literals
     */
    var initTreeHtml = function() {
        var treeHtml="";
        treeHtml += "<div class='modal fade' aria-hidden='true' id='tree_form' role='dialog' data-backdrop='static'>";
        treeHtml += "	<div class='modal-dialog modal-md'>";
        treeHtml += "		<div class='modal-content' style='margin-top: -20px;'>";
        treeHtml += "";
        treeHtml += "			<div class='modal-header' style='background-color: #31708f;'>";
        treeHtml += "				<button type='button' class='close' data-dismiss='modal' title='" + DX_CORE.trans_tree_close + "'><i class='fa fa-times' style='color: white'><\/i><\/button>";
        treeHtml += "				<h4 class='modal-title' style='color: white;'>";
        treeHtml += "				<\/h4>";
        treeHtml += "			<\/div>";
        treeHtml += "";
        treeHtml += "			<div class='modal-body'>";
        treeHtml += "				<div style='margin-left: 20px;'>";
        treeHtml += "					<div class='dx-tree-frame' style='height: 350px; overflow-y: auto; border-width: 1px; border-color: lightgray; border-style: solid; margin-bottom: 20px;'>";
        treeHtml += "						<div class='dx-tree-content'>";
        treeHtml += "						<\/div>";
        treeHtml += "					<\/div>";
        treeHtml += "					<div class='alert alert-info'>";
        treeHtml += "						<p><b>" + DX_CORE.trans_tree_chosen + "<\/b><\/p>";
        treeHtml += "						<p id='tree_form_txt'><\/p>";
        treeHtml += "					<\/div>";
        treeHtml += "				<\/div>";
        treeHtml += "			<\/div>";
        treeHtml += "			<div class='modal-footer'>";
        treeHtml += "				<button type='button' class='btn btn-primary dx-tree-btn-choose'><i class='fa fa-check'><\/i>&nbsp;" + DX_CORE.trans_tree_choose + "<\/button>";
        treeHtml += "				<button type='button' class='btn btn-white' data-dismiss='modal'><i class='fa fa-undo'><\/i>&nbsp;" + DX_CORE.trans_tree_cancel + "<\/button>";
        treeHtml += "			<\/div>";
        treeHtml += "		<\/div>";
        treeHtml += "	<\/div>";
        treeHtml += "<\/div>";
        
        $('body').append(treeHtml);
    };
    
    /**
     * Inicializē daudzlīmeņa lauka JavaScript funkcionalitāti laukiem, kam tā vēl nav inicializēta
     * 
     * @returns {undefined}
     */
    var initField = function() {
        
        // Koka formu inicializējam tikai vienreiz
        if (is_tree_form_set == 0) {
            initTreeHtml();
            handleBtnChoose();
            is_tree_form_set = 1;
        }
        
        $(".dx-tree-field[dx_is_init=0]").each(function() {
            handleBtnDel($(this));
            handleBtnAdd($(this));
            $(this).attr('dx_is_init', 1);
        });       
    };

    return {
        init: function() {
            initField();
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {
    TreeField.init();
});