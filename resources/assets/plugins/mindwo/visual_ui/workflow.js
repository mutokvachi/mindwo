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
         * Graph
         */
        this.graph = null;

        // Initializes class
        this.init();
    }

    /**
     * Initializes component
     * @returns {undefined}
     */
    $.extend($.DxWorkflow.prototype, {
        init: function () {
            var self = this;

            $('#set_xml').click(function () {
                var xm = document.getElementById('txt_xml');
                self.setXML(self, xm.value)
            });
            $('#get_xml').click(function () {
                self.getXML(self)
            });

            var container = self.domObject.find('.dx-wf-graph')[0];
            var tbContainer = self.domObject.find('.dx-wf-toolbar')[0];

            // Checks if browser is supported
            if (!mxClient.isBrowserSupported())
            {
                // Displays an error message if the browser is
                // not supported.
                mxUtils.error('Browser is not supported!', 200, false);
            }
            else
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
                toolbar.enabled = false

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
                self.graph.setConnectable(true);
                self.graph.setMultigraph(false);

                // Stops editing on enter or escape keypress
                var keyHandler = new mxKeyHandler(self.graph);
                var rubberband = new mxRubberband(self.graph);

                var addVertex = function (icon, w, h, style)
                {
                    var vertex = new mxCell(null, new mxGeometry(0, 0, w, h), style);
                    vertex.setVertex(true);

                    var img = self.addToolbarItem(self.graph, toolbar, vertex, icon);
                    img.enabled = true;

                    self.graph.getSelectionModel().addListener(mxEvent.CHANGE, function ()
                    {
                        var tmp = self.graph.isSelectionEmpty();
                        mxUtils.setOpacity(img, (tmp) ? 100 : 20);
                        img.enabled = tmp;
                    });
                };


                addVertex(mxBasePath + '/images/rounded.gif', 100, 40, 'shape=rounded');
                addVertex(mxBasePath + '/images/ellipse.gif', 40, 40, 'shape=ellipse');
                addVertex(mxBasePath + '/images/rhombus.gif', 40, 40, 'shape=rhombus');

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

            /**
             * Uzstāda ritjoslu modālajam uzdevuma logam
             * 
             * @param {object} frm Uzdevuma formas elements
             * @returns {undefined}
             */
            var handleModalScrollbar = function (frm) {
                frm.on('show.bs.modal', function () {
                    frm.find('.modal-body').css('overflow-y', 'auto');
                    frm.find('.modal-body').css('max-height', 'none');
                });
            };

            /**
             * Apstrādā uzdevuma formas aizvēršanu - izņem pārlūkā ielādēto HTML
             * Ja forma bija atvērta no saraksta, tad iespējo saraksta funkcionalitāti
             * 
             * @param {object} frm Uzdevuma formas elements
             * @returns {undefined}
             */
            var handleModalHide = function (frm) {
                frm.on('hidden.bs.modal', function (e) {

                    var grid_id = frm.data('grid-htm-id');

                    if (grid_id) {
                        stop_executing(grid_id);
                    }

                    setTimeout(function () {
                        frm.remove();
                    }, 200);
                });
            };

            if (modal) {
                handleModalScrollbar(modal);
                handleModalHide(modal);

                modal.data('is-init', 1);
                modal.modal('show');
            }
        },
        addToolbarItem: function (graph, toolbar, prototype, image)
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
            }

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
        loadData: function () {
            var xml = this.domObject.data('xml_data');

            this.setXML(this, xml);
        },
        getXML: function (self) {
            var encoder = new mxCodec();
            var node = encoder.encode(self.graph.getModel());
            xm = document.getElementById('txt_xml');
            xm.value = mxUtils.getPrettyXml(node);
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
            }
            finally
            {
                // Updates the display
                self.graph.getModel().endUpdate();
            }

        }
    });
})(jQuery);

$(document).ajaxComplete(function () {
    // Initializes all found worklfow containers
    $('.dx-wf-container').DxWorkflow();
});
