mxBasePath = '/js/plugins/mxgraph/src';

(function ($)
{
    /**
     * Creates jQuery plugin for workflow form
     * @returns DxWorkflow
     */
    $.fn.DxWorkflow = function ()
    {
        return this.each(function ()
        {
            new $.DxWorkflow($(this));
        });
    };

    /**
     * Class for managing workflow form
     * @type Window.DxWorkflow 
     */
    $.DxWorkflow = function (domObject) {
        /**
         * Worflow control's DOM object which is related to this class
         */
        this.domObject = domObject;

        /**
         * Graphs model
         */
        this.model = null;

        /**
         * Graph object
         */
        this.graph = null;

        /**
         * Workflow steps' list ID
         */
        this.wfStepsListId = 0;

        /**
         * Workflow ID which is loaded
         */
        this.workflowId = 0;

        /**
         * Workflow associated register list ID
         */
        this.wfRegisterListId = 0;

        /**
         * Task type classifier list with all registered task types from the system.
         */
        this.wfTaskTypes = [];

        /**
         * Parameter if ajax request is on the go
         */
        this.isSending = false;

        this.isGraphInit = false;

        this.dateFormat = '';

        this.locale = 'en';

        // Initializes class
        this.init();
    }

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxWorkflow.prototype, {
        vertexStyle: {
            ellipse: {
                style: 'shape=ellipse;editable=0;html=1;whiteSpace=wrap;fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;',
                width: 20,
                height: 20
            },
            rounded: {
                style: 'shape=rounded;html=1;whiteSpace=wrap;fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;',
                width: 100,
                height: 60
            },
            rhombus: {
                style: 'shape=rhombus;html=1;whiteSpace=wrap;fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;',
                width: 100,
                height: 100
            }
        },
        mxIconSet: function (self, state)
        {
            this.images = [];
            var graph = state.view.graph;

            var isEditable = state.cell.style.indexOf('shape=ellipse') == -1;

            if (isEditable) {
                // Edit Icon
                var img = mxUtils.createImage(mxBasePath + '/images/gear.png');
                img.setAttribute('title', 'Edit');
                img.style.position = 'absolute';
                img.style.cursor = 'pointer';
                img.style.width = '16px';
                img.style.height = '16px';
                img.style.left = (state.x + state.width + 4) + 'px';
                img.style.top = (state.y + (state.height / 2) - 20) + 'px';

                mxEvent.addGestureListeners(img,
                        mxUtils.bind(this, function (evt)
                        {
                            self.editWorkflowStep(self, state.cell.workflow_step_id, state.cell);

                            mxEvent.consume(evt);
                            this.destroy();
                        })
                        );

                state.view.graph.container.appendChild(img);
                this.images.push(img);
            }

            // Delete Icon
            var img = mxUtils.createImage(mxBasePath + '/images/delete2.png');
            img.setAttribute('title', 'Delete');
            img.style.position = 'absolute';
            img.style.cursor = 'pointer';
            img.style.width = '16px';
            img.style.height = '16px';
            img.style.left = (state.x + state.width + 4) + 'px';

            if (isEditable) {
                img.style.top = (state.y + (state.height / 2) + 4) + 'px';
            } else {
                img.style.top = (state.y + (state.height / 2) - 8) + 'px';
            }

            mxEvent.addGestureListeners(img,
                    mxUtils.bind(this, function (evt)
                    {
                        // Disables dragging the image
                        mxEvent.consume(evt);
                    })
                    );

            mxEvent.addListener(img, 'click',
                    mxUtils.bind(this, function (evt)
                    {
                        graph.removeCells([state.cell]);
                        mxEvent.consume(evt);
                        this.destroy();
                    })
                    );

            state.view.graph.container.appendChild(img);
            this.images.push(img);

            this.destroy = function ()
            {
                if (this.images != null)
                {
                    for (var i = 0; i < this.images.length; i++)
                    {
                        var img = this.images[i];
                        img.parentNode.removeChild(img);
                    }
                }

                this.images = null;
            };
        },
        editWorkflowStep: function (self, stepId, vertex) {
            if (typeof stepId === 'undefined' || stepId < 0) {
                stepId = 0;
            }

            open_form('form', stepId, self.wfStepsListId, 0, 0, '', 1, '', {
                before_show: function (form) {
                    self.onBeforeFormShow(form, self, stepId);
                },
                after_save: function (form) {
                    form.modal("hide");
                    self.onAfterFormClose(form, self, vertex);

                }
            });

        },
        init: function () {
            var self = this;

            // Exit if component already initialized
            if (self.domObject.data('is_init') == 1) {
                return;
            }

            // Sets parameters
            self.workflowId = self.domObject.data('wf_id');
            self.wfRegisterListId = self.domObject.data('wf_register_id');
            self.wfStepsListId = self.domObject.data('wf_steps_list_id');
            self.dateFormat = self.domObject.data('data-date-format');
            self.locale = self.domObject.data('data-locale');

            self.initDatePickers(this, '.dx-cms-workflow-form-input-valid_from');
            self.initDatePickers(this, '.dx-cms-workflow-form-input-valid_to');

            $('#dx-cms-workflow-form-btn-save').click(function () {
                self.save({self: self, initGraph: false});
            });

            $('.dx-cms-workflow-form-tab-steps-btn', self.domObject).click(function () {
                self.onStepsTabClick(self);
            });
        },
        onStepsTabClick: function (self) {
            if (!self.isGraphInit) {
                if (self.workflowId > 0) {
                    self.initGraph(self);
                } else {
                    //callback, callbackParameters, title, bodyText, acceptText, declineText
                    PageMain.showConfirm(self.save, {self: self, initGraph: true}, Lang.get('workflow.must_save_title'), Lang.get('workflow.must_save_text'));
                }
            }
        },
        initDatePickers: function (self, picker_name) {
            var picker = $(picker_name, self.domObject);

            picker.datetimepicker({
                lang: self.locale,
                format: self.dateFormat,
                timepicker: 0,
                dayOfWeekStart: 1,
                closeOnDateSelect: true
            });

            $(picker_name + '-calc', self.domObject).click(function (e) {
                $(picker_name, self.domObject).datetimepicker('show');
            });
        },
        initGraph: function () {
            var self = this;

            self.isGraphInit = true;

            var wfTaskTypesObj = self.domObject.data('wf_task_types');

            for (var i = 0; i < wfTaskTypesObj.length; i++) {
                self.wfTaskTypes[wfTaskTypesObj[i].id] = wfTaskTypesObj[i].code;
            }

            $('#set_xml').click(function () {
                var xm = document.getElementById('txt_xml');
                self.setXML(self, xm.value);
            });
            $('#get_xml').click(function () {
                var xm = document.getElementById('txt_xml');
                xm.value = self.getXML(self);
            });

            var container = self.domObject.find('.dx-wf-graph')[0];
            var tbContainer = self.domObject.find('.dx-wf-toolbar')[0];

            // Checks if browser is supported
            if (!mxClient.isBrowserSupported())
            {
                // Displays an error message if the browser is
                // not supported.
                mxUtils.error('Browser is not supported!', 200, false);
            } else
            {
                // Defines an icon for creating new connections in the connection handler.
                // This will automatically disable the highlighting of the source vertex.
                mxConnectionHandler.prototype.connectImage = new mxImage(mxBasePath + '/images/connector.gif', 16, 16);
                /*
                 // Creates the div for the toolbar
                 var tbContainer = document.createElement('div');
                 tbContainer.style.position = 'absolute';
                 tbContainer.style.overflow = 'hidden';
                 tbContainer.style.padding = '2px';
                 tbContainer.style.left = '0px';
                 tbContainer.style.top = '0px';
                 tbContainer.style.width = '24px';
                 tbContainer.style.bottom = '0px';
                 
                 document.body.appendChild(tbContainer);
                 */

                // Creates new toolbar without event processing

                var toolbar = new mxToolbar(tbContainer);
                toolbar.enabled = false;

                /*
                 // Creates the div for the graph
                 var container = document.createElement('div');
                 container.style.position = 'absolute';
                 container.style.overflow = 'hidden';
                 container.style.left = '24px';
                 container.style.top = '0px';
                 container.style.right = '0px';
                 container.style.bottom = '0px';
                 container.style.background = 'url("editors/images/grid.gif")';
                 
                 document.body.appendChild(container);
                 */

                // Workaround for Internet Explorer ignoring certain styles
                if (mxClient.IS_QUIRKS)
                {
                    document.body.style.overflow = 'hidden';
                    new mxDivResizer(tbContainer);
                    new mxDivResizer(container);
                }

                // Creates the model and the graph inside the container
                // using the fastest rendering available on the browser
                self.model = new mxGraphModel();
                self.graph = new mxGraph(container, self.model);

                // Sets parameter that allow text wrap
                self.graph.setHtmlLabels(true);

                // Enables new connections in the graph
                self.graph.setConnectable(false);
                self.graph.setMultigraph(false);

                // Changes the default edge style
                // self.graph.getStylesheet().getDefaultEdgeStyle()['edgeStyle'] = 'orthogonalEdgeStyle';
                // Sets default edge style 
                var defaultEdgeStyle = self.graph.getStylesheet().getDefaultEdgeStyle();
                defaultEdgeStyle[mxConstants.STYLE_FILLCOLOR] = '#E1E5EC';
                defaultEdgeStyle[mxConstants.STYLE_STROKECOLOR] = '#4B77BE';
                defaultEdgeStyle[mxConstants.STYLE_FONTCOLOR] = 'black';
                defaultEdgeStyle[mxConstants.STYLE_LABEL_POSITION] = 'right';
                defaultEdgeStyle[mxConstants.STYLE_ALIGN] = 'left';

                // Defines the tolerance before removing the icons
                var iconTolerance = 20;

                // Sets yes or no arrrows
                self.graph.connectionHandler.addListener(mxEvent.CONNECT, function (event, sender) {
                    // Counts arrows which has value "yes"
                    var edgeCount = self.countOutgoingEdges(sender.properties.cell.source, true);

                    // If no arrows with value "yes" has been found then set current edge as yes
                    if (edgeCount == 0) {
                        sender.properties.cell.is_yes = 1;

                        if (sender.properties.cell.source.has_arrow_labels == 1) {
                            sender.properties.cell.value = Lang.get('workflow.yes');
                        }
                    } else if (edgeCount == 1) {
                        // If one "yes" arrow has been found then set current edge as "no" arrow
                        sender.properties.cell.is_yes = 0;
                        if (sender.properties.cell.source.has_arrow_labels == 1) {
                            sender.properties.cell.value = Lang.get('workflow.no');
                        }
                    }
                });

                // Shows icons if the mouse is over a cell
                self.graph.addMouseListener(
                        {
                            currentState: null,
                            currentIconSet: null,
                            mouseDown: function (sender, me)
                            {
                                // Hides icons on mouse down
                                if (this.currentState != null)
                                {
                                    this.dragLeave(me.getEvent(), this.currentState);
                                    this.currentState = null;
                                }
                            },
                            mouseMove: function (sender, me)
                            {
                                if (this.currentState != null && (me.getState() == this.currentState ||
                                        me.getState() == null))
                                {
                                    var tol = iconTolerance;
                                    var tmp = new mxRectangle(me.getGraphX() - tol,
                                            me.getGraphY() - tol, 2 * tol, 2 * tol);

                                    if (mxUtils.intersects(tmp, this.currentState))
                                    {
                                        return;
                                    }
                                }

                                var tmp = self.graph.view.getState(me.getCell());

                                // Ignores everything but vertices
                                if (self.graph.isMouseDown || (tmp != null && !self.graph.getModel().isVertex(tmp.cell)))
                                {
                                    tmp = null;
                                }

                                if (tmp != this.currentState)
                                {
                                    if (this.currentState != null)
                                    {
                                        this.dragLeave(me.getEvent(), this.currentState);
                                    }

                                    this.currentState = tmp;

                                    if (this.currentState != null)
                                    {
                                        this.dragEnter(me.getEvent(), this.currentState);
                                    }
                                }
                            },
                            mouseUp: function (sender, me) {
                            },
                            dragEnter: function (evt, state)
                            {
                                var outEdgesCount = self.countOutgoingEdges(state.cell, false);

                                // Allow new edges if limit has not yet been reached
                                if (outEdgesCount < state.cell.arrow_count) {
                                    self.graph.setConnectable(true);
                                }

                                if (this.currentIconSet == null)
                                {
                                    this.currentIconSet = new self.mxIconSet(self, state);
                                }
                            },
                            dragLeave: function (evt, state)
                            {
                                self.graph.setConnectable(false);
                                if (this.currentIconSet != null)
                                {
                                    this.currentIconSet.destroy();
                                    this.currentIconSet = null;
                                }
                            }
                        });

                // Stops editing on enter or escape keypress
                var keyHandler = new mxKeyHandler(self.graph);
                var rubberband = new mxRubberband(self.graph);

                var addVertex = function (icon, is_endpoint)
                {
                    var style, w, h;

                    if (is_endpoint) {
                        style = self.vertexStyle.ellipse.style;
                        w = self.vertexStyle.ellipse.width;
                        h = self.vertexStyle.ellipse.height;
                    } else {
                        style = self.vertexStyle.rounded.style;
                        w = self.vertexStyle.rounded.width;
                        h = self.vertexStyle.rounded.height;
                    }

                    var vertex = new mxCell(null, new mxGeometry(0, 0, w, h), style);
                    vertex.setVertex(true);

                    var img = self.addToolbarItem(self, self.graph, toolbar, vertex, icon, is_endpoint);
                    img.enabled = true;

                    self.graph.getSelectionModel().addListener(mxEvent.CHANGE, function ()
                    {
                        var tmp = self.graph.isSelectionEmpty();
                        mxUtils.setOpacity(img, (tmp) ? 100 : 20);
                        img.enabled = tmp;
                    });
                };


                addVertex(mxBasePath + '/images/rounded.gif', false);
                addVertex(mxBasePath + '/images/ellipse.gif', true);

                var keyHandler = new mxKeyHandler(self.graph);
                keyHandler.bindKey(46, function (evt)
                {
                    if (self.graph.isEnabled())
                    {
                        self.graph.removeCells();
                    }
                });

                self.loadData();
            }

            var modal = $('.dx-wf-container').closest('.modal');

            if (modal) {
                self.handleModalScrollbar(modal);
                self.handleModalHide(modal);

                self.domObject.data('is_init', 1);
                modal.modal('show');
            }
        },
        onBeforeFormShow: function (form, self) {
            form.find('div[dx_fld_name_form=id]').hide();
            form.find('div[dx_fld_name_form=step_nr]').hide();
            form.find('div[dx_fld_name_form=yes_step_nr]').hide();
            form.find('div[dx_fld_name_form=no_step_nr]').hide();
            form.find('div[dx_fld_name_form=workflow_def_id]').hide();
            form.find('div[dx_fld_name_form=list_id]').hide();

            form.find('select[dx_fld_name=workflow_def_id]').val(self.workflowId);
            form.find('select[dx_fld_name=list_id]').val(self.wfRegisterListId);

            form.find('select[dx_fld_name=task_type_id]').on('change', function (e, o) {
                form.find('div[dx_fld_name_form=no_step_nr]').hide();
            });
        },
        onAfterFormClose: function (form, self, vertex) {
            var stepId = form.find('input[name=id]').val();
            var taskTypeId = form.find('input[name=task_type_id]').val();
            var stepNr = form.find('input[name=step_nr]').val();
            var stepTitle = form.find('input[name=step_title]').val();

            //var taskTypeCode = self.

            self.setCellProperties(self, vertex, stepId, taskTypeId, stepNr, stepTitle);

        },
        setCellProperties: function (self, vertex, stepId, taskTypeId, stepNr, stepTitle) {
            var hasArrowLabels = 0;
            var typeCode = self.wfTaskTypes[taskTypeId];

            var geometry = vertex.getGeometry();
            var style = '', arrow_count = 0;
            ;

            if (typeCode == 'CRIT' || typeCode == 'CRITM') {
                hasArrowLabels = 1;
                arrow_count = 2;
                style = self.vertexStyle.rhombus.style;
                geometry.width = self.vertexStyle.rhombus.width;
                geometry.height = self.vertexStyle.rhombus.height;
            } else if (typeCode == 'ENDPOINT') {
                arrow_count = 1;
                style = self.vertexStyle.ellipse.style;
                geometry.width = self.vertexStyle.ellipse.width;
                geometry.height = self.vertexStyle.ellipse.height;
            } else if (typeCode == 'SET') {
                arrow_count = 1;
                style = self.vertexStyle.rounded.style;
                geometry.width = self.vertexStyle.rounded.width;
                geometry.height = self.vertexStyle.rounded.height;
            } else {
                arrow_count = 2;
                style = self.vertexStyle.rounded.style;
                geometry.width = self.vertexStyle.rounded.width;
                geometry.height = self.vertexStyle.rounded.height;
            }

            vertex.setGeometry(geometry);
            vertex.setId('s' + stepNr);
            vertex.workflow_step_id = stepId;
            vertex.arrow_count = arrow_count;
            vertex.type_code = typeCode;
            vertex.has_arrow_labels = hasArrowLabels;
            vertex.setValue(stepTitle);

            self.graph.getModel().setStyle(vertex, style);

            //   var model = self.graph.getModel();



            self.graph.refresh();
        },
        /**
         * Count out going edges for cell
         * @param {object} cell Cell under mouse cursor 
         * @returns {int} Count of outgoing edges
         */
        countOutgoingEdges: function (cell, count_only_yes) {
            var edges = cell.edges;
            var edgesCount = (edges == null ? 0 : edges.length);


            var outEdgesCount = 0;
            for (var i = 0; i < edgesCount; i++) {
                var edge = edges[i];

                if (edge.target === null || edge.target.id !== cell.id) {
                    // If counts only "yes" arrows and current arrow is "no" arrow then go to next edge
                    if (count_only_yes && (edge.is_yes == null || typeof edge.is_yes === 'undefined' || edge.is_yes == 0)) {
                        continue;
                    }

                    outEdgesCount++;
                }
            }

            return outEdgesCount;
        },
        /**
         * Uzstāda ritjoslu modālajam uzdevuma logam
         * 
         * @param {object} frm Uzdevuma formas elements
         * @returns {undefined}
         */
        handleModalScrollbar: function (frm) {
            frm.on('show.bs.modal', function () {
                frm.find('.modal-body').css('overflow-y', 'auto');
                frm.find('.modal-body').css('max-height', 'none');
            });
        },
        /**
         * Apstrādā uzdevuma formas aizvēršanu - izņem pārlūkā ielādēto HTML
         * Ja forma bija atvērta no saraksta, tad iespējo saraksta funkcionalitāti
         * 
         * @param {object} frm Uzdevuma formas elements
         * @returns {undefined}
         */
        handleModalHide: function (frm) {
            frm.on('hidden.bs.modal', function (e) {

                var grid_id = frm.data('grid-htm-id');

                if (grid_id) {
                    stop_executing(grid_id);
                }

                setTimeout(function () {
                    frm.remove();
                }, 200);
            });
        },
        addToolbarItem: function (self, graph, toolbar, prototype, image, is_endpoint)
        {
            // Function that is executed when the image is dropped on
            // the graph. The cell argument points to the cell under
            // the mousepointer if there is one.
            var funct = function (graph, evt, cell, x, y)
            {
                graph.stopEditing(false);

                var vertex = graph.getModel().cloneCell(prototype);
                vertex.geometry.x = x;
                vertex.geometry.y = y;

                graph.addCell(vertex);
                graph.setSelectionCell(vertex);

                if (!is_endpoint) {
                    self.editWorkflowStep(self, 0, vertex);
                }
            };

            // Creates the image which is used as the drag icon (preview)
            var img = toolbar.addMode(null, image, function (evt, cell)
            {
                var pt = this.graph.getPointForEvent(evt);
                funct(graph, evt, cell, pt.x, pt.y);
            });

            // Disables dragging if element is disabled. This is a workaround
            // for wrong event order in IE. Following is a dummy listener that
            // is invoked as the last listener in IE.
            mxEvent.addListener(img, 'mousedown', function (evt)
            {
                // do nothing
            });

            // This listener is always called first before any other listener
            // in all browsers.
            mxEvent.addListener(img, 'mousedown', function (evt)
            {
                if (img.enabled == false)
                {
                    mxEvent.consume(evt);
                }
            });

            mxUtils.makeDraggable(img, graph, funct);

            return img;
        },
        /**
         * Loads XML data into graph
         */
        loadData: function () {
            var xml = this.domObject.data('xml_data');

            this.setXML(this, xml);
        },
        /**
         * Retrieve XML from graph
         */
        getXML: function (self) {
            var encoder = new mxCodec();
            var node = encoder.encode(self.graph.getModel());

            return  mxUtils.getPrettyXml(node);
        },
        save: function (data) {
            var self = data.self;
            var initGraph = data.initGraph;

            if (self.isSending) {
                return;
            }

            self.isSending = true;
            show_page_splash(1);


            var xml = '';

            if (self.initGraph && self.workflowId > 0) {
                xml = self.getXML(self);
            }

            var data = {
                workflow_id: self.workflowId,
                xml_data: xml,
                list_id: $('.dx-cms-workflow-form-input-list_id').val(),
                title: $('.dx-cms-workflow-form-input-title').val(),
                description: $('.dx-cms-workflow-form-input-description').val(),
                is_custom_approve: $('.dx-cms-workflow-form-input-is_custom_approve').val(),
                valid_from: $('.dx-cms-workflow-form-input-valid_from').val(),
                valid_to: $('.dx-cms-workflow-form-input-valid_to').val()
            };

            $.ajax({
                url: DX_CORE.site_url + 'workflow/visual/save',
                type: "post",
                data: data,
                dataType: "json",
                context: self,
                success: function (data) {
                    self.onSaveSuccess(data, initGraph);
                },
                error: self.onSaveError
            });
        },
        onSaveSuccess: function (data, initGraph) {
            var self = this;

            if (data && data.success == 1) {
                notify_info(Lang.get('workflow.success'));
            } else {
                self.showError(data);
            }

            self.isSending = false;

            if (initGraph && !self.isGraphInit) {
                self.initGraph();
            }

            hide_page_splash(1);

        },
        onSaveError: function (data) {
            var self = this;

            self.showError(data);

            self.isSending = false;
            hide_page_splash(1);
        },
        /**
         * Shows error
         * @param {JSON} data
         * @returns {undefined}
         */
        showError: function (data) {
            if (data && data.errors) {
                var errMsg = '<ul>';

                for (var i = 0; i < data.errors.length; i++) {
                    errMsg += '<li>' + data.errors[i] + '</li>';
                }

                errMsg += '</ul>';

                toastr.error(Lang.get('errors.workflow.not_saved') + ': ' + errMsg);
            }
        },
        setXML: function (self, xml) {
            // Gets the default parent for inserting new cells. This
            // is normally the first child of the root (ie. layer 0).
            var parent = self.graph.getDefaultParent();

            self.graph.getModel().beginUpdate();
            try
            {
                var doc = mxUtils.parseXml(xml);
                var dec = new mxCodec(doc);
                var model = dec.decode(doc.documentElement);

                self.graph.getModel().mergeChildren(model.getRoot().getChildAt(0), parent);
            } finally
            {
                // Updates the display
                self.graph.getModel().endUpdate();
            }
        }
    });
})(jQuery);

// ajaxComplete ready
$(document).ready(function () {
    // Initializes all found worklfow containers
    $('.dx-wf-container').DxWorkflow();
});

$(document).ajaxComplete(function () {
    // Initializes all found worklfow containers
    $('.dx-wf-container').DxWorkflow();
});
