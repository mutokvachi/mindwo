/*
 * jquery.simulate - simulate browser mouse and keyboard events
 *
 * Copyright (c) 2009 Eduardo Lundgren (eduardolundgren@gmail.com)
 * and Richard D. Worth (rdworth@gmail.com)
 *
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php) 
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 */

;(function($) {

$.fn.extend({
	simulate: function(type, options) {
		return this.each(function() {
			var opt = $.extend({}, $.simulate.defaults, options || {});
			new $.simulate(this, type, opt);
		});
	}
});

$.simulate = function(el, type, options) {
	this.target = el;
	this.options = options;

	if (/^drag$/.test(type)) {
		this[type].apply(this, [this.target, options]);
	} else {
		this.simulateEvent(el, type, options);
	}
};

$.extend($.simulate.prototype, {
	simulateEvent: function(el, type, options) {
		var evt = this.createEvent(type, options);
		this.dispatchEvent(el, type, evt, options);
		return evt;
	},
	createEvent: function(type, options) {
		if (/^mouse(over|out|down|up|move)|(dbl)?click$/.test(type)) {
			return this.mouseEvent(type, options);
		} else if (/^key(up|down|press)$/.test(type)) {
			return this.keyboardEvent(type, options);
		}
	},
	mouseEvent: function(type, options) {
		var evt;
		var e = $.extend({
			bubbles: true, cancelable: (type != "mousemove"), view: window, detail: 0,
			screenX: 0, screenY: 0, clientX: 0, clientY: 0,
			ctrlKey: false, altKey: false, shiftKey: false, metaKey: false,
			button: 0, relatedTarget: undefined
		}, options);

		var relatedTarget = $(e.relatedTarget)[0];

		if ($.isFunction(document.createEvent)) {
			evt = document.createEvent("MouseEvents");
			evt.initMouseEvent(type, e.bubbles, e.cancelable, e.view, e.detail,
				e.screenX, e.screenY, e.clientX, e.clientY,
				e.ctrlKey, e.altKey, e.shiftKey, e.metaKey,
				e.button, e.relatedTarget || document.body.parentNode);
		} else if (document.createEventObject) {
			evt = document.createEventObject();
			$.extend(evt, e);
			evt.button = { 0:1, 1:4, 2:2 }[evt.button] || evt.button;
		}
		return evt;
	},
	keyboardEvent: function(type, options) {
		var evt;

		var e = $.extend({ bubbles: true, cancelable: true, view: window,
			ctrlKey: false, altKey: false, shiftKey: false, metaKey: false,
			keyCode: 0, charCode: 0
		}, options);

		if ($.isFunction(document.createEvent)) {
			try {
				evt = document.createEvent("KeyEvents");
				evt.initKeyEvent(type, e.bubbles, e.cancelable, e.view,
					e.ctrlKey, e.altKey, e.shiftKey, e.metaKey,
					e.keyCode, e.charCode);
			} catch(err) {
				evt = document.createEvent("Events");
				evt.initEvent(type, e.bubbles, e.cancelable);
				$.extend(evt, { view: e.view,
					ctrlKey: e.ctrlKey, altKey: e.altKey, shiftKey: e.shiftKey, metaKey: e.metaKey,
					keyCode: e.keyCode, charCode: e.charCode
				});
			}
		} else if (document.createEventObject) {
			evt = document.createEventObject();
			$.extend(evt, e);
		}
		if (($.browser !== undefined) && ($.browser.msie || $.browser.opera)) {
			evt.keyCode = (e.charCode > 0) ? e.charCode : e.keyCode;
			evt.charCode = undefined;
		}
		return evt;
	},

	dispatchEvent: function(el, type, evt) {
		if (el.dispatchEvent) {
			el.dispatchEvent(evt);
		} else if (el.fireEvent) {
			el.fireEvent('on' + type, evt);
		}
		return evt;
	},

	drag: function(el) {
		var self = this, center = this.findCenter(this.target), 
			options = this.options,	x = Math.floor(center.x), y = Math.floor(center.y), 
			dx = options.dx || 0, dy = options.dy || 0, target = this.target;
		var coord = { clientX: x, clientY: y };
		this.simulateEvent(target, "mousedown", coord);
		coord = { clientX: x + 1, clientY: y + 1 };
		this.simulateEvent(document, "mousemove", coord);
		coord = { clientX: x + dx, clientY: y + dy };
		this.simulateEvent(document, "mousemove", coord);
		this.simulateEvent(document, "mousemove", coord);
		this.simulateEvent(target, "mouseup", coord);
	},
	findCenter: function(el) {
		var el = $(this.target), o = el.offset();
		return {
			x: o.left + el.outerWidth() / 2,
			y: o.top + el.outerHeight() / 2
		};
	}
});

$.extend($.simulate, {
	defaults: {
		speed: 'sync'
	},
	VK_TAB: 9,
	VK_ENTER: 13,
	VK_ESC: 27,
	VK_PGUP: 33,
	VK_PGDN: 34,
	VK_END: 35,
	VK_HOME: 36,
	VK_LEFT: 37,
	VK_UP: 38,
	VK_RIGHT: 39,
	VK_DOWN: 40
});

})(jQuery);
/**
 * Meklēšanas rīku pogas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var SearchTools = function()
{    
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Funkcija, kas parāda vai paslēp saiti "Notīrīt"
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearLinkShowHide = null;
    
    /**
     * Funkcija, kas notīra meklēšanas lauku vērtības.
     * Funkcija tiek uzstādīta pie meklēšanas rīku inicializēšanas
     * 
     * @type Function
     */
    var clearFields = null;
    
    /**
     * Parāda vai paslēp meklēšanas rīkus
     * 
     * @returns {undefined}
     */
    var showHideTools = function() {

        var tools_btn = form_elem.find('.search-tools-btn');

        if (tools_btn.find('i').hasClass('fa-caret-down'))
        {
            tools_btn.find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
            tools_btn.removeClass('btn-primary').addClass('btn-default');
            form_elem.removeClass('search-tools-hiden').addClass('search-tools-shown');
        }
        else
        {
            tools_btn.find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
            tools_btn.removeClass('btn-default').addClass('btn-primary');

            form_elem.removeClass('search-tools-shown').addClass('search-tools-hiden');

            var phrase = form_elem.find('input[name=criteria]').val();
            
            clearFields();
            
            form_elem.find('input[name=criteria]').val(phrase);
            
            clearLinkShowHide();
        }
    };

    /**
     * Nodrošina meklēšanas pogas nospiešanas funkcionalitāti - veic meklēšanu
     * 
     * @returns {undefined}
     */
    var handleBtnSearch = function() {
        form_elem.find(".search-simple-btn").click(function(e) {
            
            show_page_splash();
            form_elem.submit();
        });
    };

    /**
     * Nodrošina meklēšanas rīku pogas nospiešanas funkcionalitāti - parāda vai paslēpj meklēšanas rīkus
     * 
     * @returns {undefined}
     */
    var handleBtnTools = function() {
        form_elem.find('.search-tools-btn').click(function(e) {
            showHideTools();
        });
    }; 
    
    /**
     * Inicializē meklēšanas rīku JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initTools = function(callback_clearLinkShowHide, callback_clearFields) {        

        form_elem = $('.search-tools-form');
        
        clearLinkShowHide = callback_clearLinkShowHide;
        clearFields = callback_clearFields;
        
        handleBtnSearch();
        handleBtnTools();
    };

    return {
        init: function(callback_clearLinkShowHide, callback_clearFields) {
            initTools(callback_clearLinkShowHide, callback_clearFields);
        },
        showHideTools: function() {
            showHideTools();
        }
    };
}();

/**
 * Darbinieku meklēšanas lapas JavaScript funkcionalitāte
 * 
 * @type _L4.Anonym$0|Function
 */
var PageEmployees = function()
{ 
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     * Darbinieku meklēšanas bloka HTML elements
     * 
     * @type type
     */
    var page_elem = null;
    
    /**
     * Atgriež elementa vērtību. Ja elements nav atrasts, tad noklusēto vērtību
     * 
     * @param {Object} parent_elem Vecākais elements, kurā tiks veikta meklēšana
     * @param {string} selector    Meklējamais elements
     * @param {mixed} default_val  Noklusētā vērtība
     * @returns {mixed}
     */
    var getElementVal = function(parent_elem, selector, default_val) {
        var elem = parent_elem.find(selector);

        if (!elem.length)
        {
            return default_val;
        }

        return elem.val();
    };

    /**
     * Parāda vai paslēpj brīdinājumu, ka jānorāda uzņēmums, lai izvēlētos sturktūrvienību
     * 
     * @param {Object} page_elem Lapas elements
     * @returns {undefined}
     */
    var showHideWarning = function(){
        var btn_choose = page_elem.find(".dx-tree-value-choose-btn");
        
        if (getElementVal(page_elem, 'select[name=source_id]', 0) > 0){
            // noņemam brīdinājumu, ja bija uzstādīts            
            if (btn_choose.hasClass('tooltipstered')) {
                btn_choose.tooltipster('disable');
            }            
        }
        else{             
            if (!btn_choose.hasClass('tooltipstered')) {
                // uzstādam brīdinājumu pirmo reizi
                btn_choose.tooltipster({
                    theme: 'tooltipster-light',
                    animation: 'grow',
                    content: page_elem.attr("trans_unit_hint")
                });
            }
            else
            {
                // iespjojam jau uzstādītu brīdinājumu
                btn_choose.tooltipster('enable');
            }
        }
    };

    /**
     * Parāda vai paslēpj saiti "Notīrīt kritērijus" atkarība ir vai nav norādīts vismaz viens kritērijis
     * 
     * @returns {undefined}
     */
    var clearLinkShowHide = function() {
        if (
                getElementVal(page_elem, 'select[name=source_id]', 0) > 0 ||
                getElementVal(page_elem, 'input[name=department]', "") ||
                getElementVal(page_elem, 'input[name=criteria]', "") ||
                getElementVal(page_elem, 'input[name=phone]', "") ||
                getElementVal(page_elem, 'input[name=position]', "")
                )
        {
            page_elem.find(".dx-clear-link").show();
        }
        else
        {
            page_elem.find(".dx-clear-link").hide();
        }
    };

    /**
     * Notīra meklēšanas kritēriju lauku vērtības
     * 
     * @returns {undefined}
     */
    var clearFields = function() {
        form_elem.find('input[name=criteria]').val('');
        form_elem.find('select[name=source_id]').val(0);
        form_elem.find('input[name=department]').val('');
        form_elem.find('input[name=position]').val('');
        form_elem.find('input[name=phone]').val('');
        form_elem.find('input[name=department_id]').val(0);
        
        // uzstāda brdīdinājumu, ka nav norādīts uzņēmums (nevar izvēlēties struktūrvienību)
        showHideWarning();
    };

    /**
     * Nodrošina struktūrvienību izvēlnes loga popup atvēršanu
     * 
     * @returns {undefined}
     */
    var handleDepartmentChoose = function() {
        page_elem.find(".dx-tree-value-choose-btn").click(function(e) {

            var company_id = page_elem.find("select[name=source_id]").val();

            if (company_id == 0)
            {
                page_elem.find("select[name=source_id]").focus();
                page_elem.find("select[name=source_id]").simulate('mousedown');
                return;
            }

            get_popup_item_by_id_ie9(company_id, 'ajax/departments', page_elem.find("select[name=source_id] option:selected").text() + ' - ' + page_elem.attr("trans_choosing_unit"), initTree);
        });
        
        // uzstāda deparamenta id uz 0, gadījumā ja manuāli rediģē departamenta teksta lauku
        page_elem.find("input[name=department]").change(function(e) {
           page_elem.find('input[name=department_id]').val(0); 
        });
    };

    /**
     * Nodrošina struktūrvienības lauka notīrīšanu
     * 
     * @returns {undefined}
     */
    var handleDepartmentClear = function() {
        page_elem.find(".dx-tree-value-clear-btn").click(function(e) {
            page_elem.find("input[name=department]").val('');
            page_elem.find("input[name=department_id]").val(0);
            clearLinkShowHide(page_elem);
        });
    };
   
    /**
     * Nodrošina saites "Notīrīt" funkcionalitāti - dzēš formas lauku vērtības
     *
     * @returns {undefined}
     */
    var handleLinkClear = function() {
        page_elem.find(".dx-clear-link").click(function() {
            clearFields();
            clearLinkShowHide();
        });
    };

    /**
     * Inicializē vērtību notīrīšanas saites parādīšanu/paslēpšanu, mainoties formas lauku vērtībām
     * 
     * @returns {undefined}
     */
    var handleValueChange = function() {
        form_elem.find("input").keyup(function() {
            clearLinkShowHide();
        });

        form_elem.find("select").change(function() {
            clearLinkShowHide();
        });
    };
    
    /**
     * Nodrošina brīdinajuma uzstādīšanu/paslēpšanu, atkarībā vai norādīts uzņēmums
     * 
     * @returns {undefined}
     */
    var handleSourceChange = function(){
        form_elem.find("select[name=source_id]").change(function() {
            showHideWarning();
        });       
    };
    
    /**
     * Handles new employee button click - opens form for new employee entering
     * 
     * @returns {undefined}
     */
    var handleNewEmployeeBtn = function() {
        page_elem.find(".dx-employee-new-add-btn").click(function() {
            new_list_item(page_elem.attr("dx_empl_list_id"), 0, 0, "", "");
        });
    }

    /**
     * Inicializē meklēšnas rīku parādīšanu pie lapas ielādes - parāda, ja bija meklēšanas pēc kāda no redzamajiem formas laukiem
     * 
     * @returns {undefined}
     */
    var initSearchTools = function()
    {
        if (
                getElementVal(page_elem, 'select[name=source_id]', 0) > 0 ||
                getElementVal(page_elem, 'input[name=department]', "") ||
                getElementVal(page_elem, 'input[name=phone]', "") ||
                getElementVal(page_elem, 'input[name=position]')
                )
        {
            SearchTools.showHideTools();
        }
    };
    
    /**
     * Inicializē darbinieku lapas JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initPage = function() {        
        
        page_elem = $(".dx-employees-page");
        form_elem = page_elem.find('.search-tools-form');
        
        SearchTools.init(clearLinkShowHide, clearFields);
                
        handleDepartmentChoose();
        handleDepartmentClear();

        EmployeesLinks.init(page_elem, clearFields);

        handleValueChange();
        handleLinkClear();
        handleSourceChange();

        clearLinkShowHide();
        initSearchTools();
        showHideWarning();
        handleNewEmployeeBtn();
    };

    /**
     * Inicializē struktūrvienību koku
     * 
     * @returns {undefined}
     */
    var initTree = function() {        
        $(".dx-department-tree-container").on('select_node.jstree', function (e, data) {
            var i, j;
            for(i = 0, j = data.selected.length; i < j; i++) {
                var node = data.instance.get_node(data.selected[i], true);
                
                $(".dx-employees-page input[name=department]").val(node.text());
                $(".dx-employees-page input[name=department_id]").val(node.attr("data-id"));
                $( '#popup_window' ).modal('hide');
                break;
            }
            
        }).jstree({
            "core" : {"multiple" : false },
            
        });
    };

    return {
        init: function() {
            initPage();
        },
        initTree: function() {
            initTree();
        }
    };
}();

$(document).ready(function() {
    PageEmployees.init();
});

$(document).ajaxComplete(function(event, xhr, settings) {
    PageEmployees.initTree();
});
//# sourceMappingURL=elix_employees.js.map
