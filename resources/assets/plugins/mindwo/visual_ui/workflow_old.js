/**
 * Workflows designer JavaScript functionality
 * 
 * @type _L4.Anonym$0|Function
 */
var UI_WorkFlow = function()
{         
    var is_script_loaded = false;
    
    var myDiagram = null;
    var myPalette = null;
    
    var lightText = 'whitesmoke';


    var saveWorkflow = function(frm){            
        var form_id = 'list_item_view_form_' + frm.data('frm-uniq-id');
        //myDiagram.model.toJson();

        var formData = getTaskSavingData(form_id, save_url);
        
        if (formData == null) {
            return;
        }
        
        var request = new FormAjaxRequest(save_url, form_id, frm.data('grid-htm-id'), formData);

        request.callback = function(data) {
            displaySavingInfo(frm, data);
        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    };
    
    var displaySavingInfo = function(frm, data) {
        
        if (data['success'] == 1){                  
            var form_id = 'list_item_view_form_' + frm.data('frm-uniq-id');

            var grid_htm_id = frm.data('grid-htm-id');
            if (grid_htm_id)
            {
                reload_grid(grid_htm_id);
            }

            notify_info(Lang.get('task_form.notify_saved'));
        } 
        else
        {             	
            notify_err(data['error']);
        }
        
    };
    
    /**
     * Uzstāda ritjoslu modālajam uzdevuma logam
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleModalScrollbar = function(frm) {
        frm.on('show.bs.modal', function () {
            frm.find('.modal-body').css('overflow-y', 'auto');
	});
    };
    
    /**
     * Apstrādā uzdevuma formas aizvēršanu - izņem pārlūkā ielādēto HTML
     * Ja forma bija atvērta no saraksta, tad iespējo saraksta funkcionalitāti
     * 
     * @param {object} frm Uzdevuma formas elements
     * @returns {undefined}
     */
    var handleModalHide = function(frm) {
        frm.on('hidden.bs.modal', function (e) {			
	
            var grid_id = frm.data('grid-htm-id');
            
            if (grid_id) {
                stop_executing(grid_id);
            }
             
            setTimeout(function() {                
                frm.remove();
            }, 200);
	});  
    };
    
    // helper definitions for node templates

    var nodeStyle = function() {
        
        return [
        // The Node.location comes from the "loc" property of the node data,
        // converted by the Point.parse static method.
        // If the Node.location is changed, it updates the "loc" property of the node data,
        // converting back using the Point.stringify static method.
        new go.Binding("location", "loc", go.Point.parse).makeTwoWay(go.Point.stringify),
        {
          // the Node.location is at the center of each node
          locationSpot: go.Spot.Center,
          //isShadowed: true,
          //shadowColor: "#888",
          // handle mouse enter/leave events to show/hide the ports
          mouseEnter: function (e, obj) { showPorts(obj.part, true); },
          mouseLeave: function (e, obj) { showPorts(obj.part, false); }
        }
      ];
    };

// Define a function for creating a "port" that is normally transparent.
    // The "name" is used as the GraphObject.portId, the "spot" is used to control how links connect
    // and where the port is positioned on the node, and the boolean "output" and "input" arguments
    // control whether the user can draw links from or to the port.
    var makePort = function(name, spot, output, input) {
      var gojs = go.GraphObject.make;  // for conciseness in defining templates
      // the port is basically just a small circle that has a white stroke when it is made visible
      return gojs(go.Shape, "Circle",
               {
                  fill: "transparent",
                  stroke: null,  // this is changed to "white" in the showPorts function
                  desiredSize: new go.Size(8, 8),
                  alignment: spot, alignmentFocus: spot,  // align the port on the main Shape
                  portId: name,  // declare this object to be a "port"
                  fromSpot: spot, toSpot: spot,  // declare where links may connect at this port
                  fromLinkable: output, toLinkable: input,  // declare whether the user may draw links to/from here
                  cursor: "pointer"  // show a different cursor to indicate potential link point
               });
    };

    // Make link labels visible if coming out of a "conditional" node.
    // This listener is called by the "LinkDrawn" and "LinkRelinked" DiagramEvents.
    var showLinkLabel = function(e) {
      var label = e.subject.findObject("LABEL");
      if (label !== null) label.visible = (e.subject.fromNode.data.figure === "Diamond");
    };
    
     // Make all ports on a node visible when the mouse is over the node
    var showPorts = function(node, show) {
        var diagram = node.diagram;
        if (!diagram || diagram.isReadOnly || !diagram.allowLink) return;
        node.ports.each(function(port) {
            port.stroke = (show ? "white" : null);
          });
    };


    var init_workflow = function() {        
        
        var gojs = go.GraphObject.make;  // for conciseness in defining templates
        
        myDiagram =
            gojs(go.Diagram, "dx-cms-wf-area",  // must name or refer to the DIV HTML element
            {
              initialContentAlignment: go.Spot.Center,
              allowDrop: true,  // must be true to accept drops from the Palette
              "LinkDrawn": showLinkLabel,  // this DiagramEvent listener is defined below
              "LinkRelinked": showLinkLabel,
              "animationManager.duration": 800, // slightly longer than default (600ms) animation
              "undoManager.isEnabled": true  // enable undo & redo
            });

        // when the document is modified, add a "*" to the title and enable the "Save" button
        myDiagram.addDiagramListener("Modified", function(e) {
          var button = document.getElementById("SaveButton");
          if (button) button.disabled = !myDiagram.isModified;
          var idx = document.title.indexOf("*");
          if (myDiagram.isModified) {
            if (idx < 0) document.title += "*";
          } else {
            if (idx >= 0) document.title = document.title.substr(0, idx);
          }
        });

        myDiagram.nodeTemplateMap.add("",  // the default category
        gojs(go.Node, "Spot", nodeStyle(),
          // the main object is a Panel that surrounds a TextBlock with a rectangular Shape
          gojs(go.Panel, "Auto",
            gojs(go.Shape, "Rectangle",
              { fill: "#00A9C9", stroke: null },
              new go.Binding("figure", "figure")),
            gojs(go.TextBlock,
              {
                font: "bold 11pt Helvetica, Arial, sans-serif",
                stroke: lightText,
                margin: 8,
                maxSize: new go.Size(160, NaN),
                wrap: go.TextBlock.WrapFit,
                editable: true
              },
              new go.Binding("text").makeTwoWay())
          ),
          // four named ports, one on each side:
          makePort("T", go.Spot.Top, false, true),
          makePort("L", go.Spot.Left, true, true),
          makePort("R", go.Spot.Right, true, true),
          makePort("B", go.Spot.Bottom, true, false)
        ));

        myDiagram.nodeTemplateMap.add("Start",
          gojs(go.Node, "Spot", nodeStyle(),
            gojs(go.Panel, "Auto",
              gojs(go.Shape, "Circle",
                { minSize: new go.Size(40, 40), fill: "#79C900", stroke: null }),
              gojs(go.TextBlock, "Start",
                { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
                new go.Binding("text"))
            ),
            // three named ports, one on each side except the top, all output only:
            makePort("L", go.Spot.Left, true, false),
            makePort("R", go.Spot.Right, true, false),
            makePort("B", go.Spot.Bottom, true, false)
          ));

        myDiagram.nodeTemplateMap.add("End",
          gojs(go.Node, "Spot", nodeStyle(),
            gojs(go.Panel, "Auto",
              gojs(go.Shape, "Circle",
                { minSize: new go.Size(40, 40), fill: "#DC3C00", stroke: null }),
              gojs(go.TextBlock, "End",
                { font: "bold 11pt Helvetica, Arial, sans-serif", stroke: lightText },
                new go.Binding("text"))
            ),
            // three named ports, one on each side except the bottom, all input only:
            makePort("T", go.Spot.Top, false, true),
            makePort("L", go.Spot.Left, false, true),
            makePort("R", go.Spot.Right, false, true)
          ));

        myDiagram.nodeTemplateMap.add("Comment",
          gojs(go.Node, "Auto", nodeStyle(),
            gojs(go.Shape, "File",
              { fill: "#EFFAB4", stroke: null }),
            gojs(go.TextBlock,
              {
                margin: 5,
                maxSize: new go.Size(200, NaN),
                wrap: go.TextBlock.WrapFit,
                textAlign: "center",
                editable: true,
                font: "bold 12pt Helvetica, Arial, sans-serif",
                stroke: '#454545'
              },
              new go.Binding("text").makeTwoWay())
            // no ports, because no links are allowed to connect with a comment
          ));


        // replace the default Link template in the linkTemplateMap
        myDiagram.linkTemplate =
          gojs(go.Link,  // the whole link panel
            {
              routing: go.Link.AvoidsNodes,
              curve: go.Link.JumpOver,
              corner: 5, toShortLength: 4,
              relinkableFrom: true,
              relinkableTo: true,
              reshapable: true,
              resegmentable: true,
              // mouse-overs subtly highlight links:
              mouseEnter: function(e, link) { link.findObject("HIGHLIGHT").stroke = "rgba(30,144,255,0.2)"; },
              mouseLeave: function(e, link) { link.findObject("HIGHLIGHT").stroke = "transparent"; }
            },
            new go.Binding("points").makeTwoWay(),
            gojs(go.Shape,  // the highlight shape, normally transparent
              { isPanelMain: true, strokeWidth: 8, stroke: "transparent", name: "HIGHLIGHT" }),
            gojs(go.Shape,  // the link path shape
              { isPanelMain: true, stroke: "gray", strokeWidth: 2 }),
            gojs(go.Shape,  // the arrowhead
              { toArrow: "standard", stroke: null, fill: "gray"}),
            gojs(go.Panel, "Auto",  // the link label, normally not visible
              { visible: false, name: "LABEL", segmentIndex: 2, segmentFraction: 0.5},
              new go.Binding("visible", "visible").makeTwoWay(),
              gojs(go.Shape, "RoundedRectangle",  // the label shape
                { fill: "#F8F8F8", stroke: null }),
              gojs(go.TextBlock, "Yes",  // the label
                {
                  textAlign: "center",
                  font: "10pt helvetica, arial, sans-serif",
                  stroke: "#333333",
                  editable: true
                },
                new go.Binding("text").makeTwoWay())
            )
          );

        // temporary links used by LinkingTool and RelinkingTool are also orthogonal:
        myDiagram.toolManager.linkingTool.temporaryLink.routing = go.Link.Orthogonal;
        myDiagram.toolManager.relinkingTool.temporaryLink.routing = go.Link.Orthogonal;
        
        // initialize the Palette that is on the left side of the page
        myPalette =
        gojs(go.Palette, "dx_cms-wf-palette",  // must name or refer to the DIV HTML element
          {
            "animationManager.duration": 800, // slightly longer than default (600ms) animation
            nodeTemplateMap: myDiagram.nodeTemplateMap,  // share the templates used by myDiagram
            model: new go.GraphLinksModel([  // specify the contents of the Palette
              { category: "Start", text: "Start" },
              { text: "Step" },
              { text: "???", figure: "Diamond" },
              { category: "End", text: "End" },
              { category: "Comment", text: "Comment" }
            ])
          });

    }
    
    var draw_workflow = function(json) {
        
        myDiagram.model = go.Model.fromJson(json);
    };
    
    /**
     * Apstrādā un inicializē vēl neinicializētās formas
     * 
     * @returns {undefined}
     */
    var initForm = function()
    { 

        $(".dx-cms-workflow-form[data-is-init='0']").each(function() {
            var json = $(this).data("json-model");
            
            if (!is_script_loaded) {
                $.getScript( DX_CORE.site_url + "gojs_workflow/release/go.js", function( data, textStatus, jqxhr ) {
                   is_script_loaded = true;
                   init_workflow();
                   draw_workflow(json);
                });
            }
            else {
                draw_workflow(json);
            }
        
            handleModalScrollbar($(this));
            handleModalHide($(this));
                        
            $(this).data('is-init', 1);            
            $(this).modal('show');
        });
        
    };

    return {
        init: function() {
            initForm();
        }
    };
}();

$(document).ajaxComplete(function(event, xhr, settings) {            
    UI_WorkFlow.init();           
});