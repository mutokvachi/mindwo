/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 13.12.16, 18:59
 */

(function($)
{
	$(document).ready(function()
	{
		$('.dx-orgchart-select').select2();
		$('.dx-orgchart-select').change(function(e)
		{
			var count = $(this).children(':selected').data('levels');
			var levels = $('.dx-orgchart-levels');
			levels.children().remove();
			for(var i = 1; i <= count; i++)
			{
				levels.append('<option' + (i == 2 ? ' selected' : '') + '>' + i + '</option>');
			}
		});
		$('.dx-orgchart-filter').click(function(e)
		{
			window.location = orgchartData.route + '/' + $('.dx-orgchart-select :selected').attr('value')
				+ '?displayLevels=' + $('.dx-orgchart-levels').val();
		});
		
		if(!orgchartData.displayLevels)
			orgchartData.displayLevels = 999;
		
		var orgchartConfig = {
			chartContainer: '#dx-orgchart-container',
			data: orgchartData.source,
			nodeContent: 'title',
			verticalDepth: 3,
			depth: orgchartData.displayLevels,
			toggleSiblingsResp: true,
			pan: true,
			// customize node creation process
			createNode: function(node, data)
			{
				var content = $(node).children('.content');
				content.wrapInner('<div class="text"></div>');
				content.prepend('<a class="link" href="' + data.href + '"><img src="' + data.avatar + '" alt=""></a>');
				
				if(data.subordinates > 0)
					content.append('<div class="subordinates" title="' + Lang.get('organization.hint_subord') + '">' + data.subordinates + '</div>');
				
				// add up arrow button to top node
				if(data.hasParent)
					$(node).append('<i class="edge verticalEdge topEdge fa"></i>');
			}
		};
		
		// init orgchart plugin
		var orgchart = new OrgChart(orgchartConfig);
		
		// save original handler of click event of up arrow button
		orgchart._clickTopEdgeOld = orgchart._clickTopEdge;
		// override event handler of up arrow button
		orgchart._clickTopEdge = function(event)
		{
			var node = $(event.target).parents('.node').first();
			var data = node.data('source');
			
			if(data.top)
				location.href = data.parentUrl;
			
			else
				this._clickTopEdgeOld(event);
		};
		$("#dx-org-zoom-in").click(function()
		{
			orgchart._setChartScale(orgchart.chart, 1.2);
		});
		$("#dx-org-zoom-out").click(function()
		{
			orgchart._setChartScale(orgchart.chart, 0.8);
		});
		$("#dx-org-export").click(function()
		{
			orgchart._clickExportButton();
		});
	});
})(jQuery);