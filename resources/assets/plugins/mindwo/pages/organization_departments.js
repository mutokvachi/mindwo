/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 14.12.16, 22:43
 */

(function($)
{
	$(document).ready(function()
	{
		// init orgchart plugin
		var orgchartConfig = {
			data: orgchartData.source,
			nodeContent: 'title',
			depth: orgchartData.displayLevels,
			toggleSiblingsResp: true,
			pan: true,
			exportButton: true,
			// customize node creation process
			createNode: function(node, data)
			{
				var content = $(node).children('.content');
				//content.prepend('<div class="main-icon"><i class="fa fa-sitemap"></i></div>');
				
				if(data.id > 0 && data.count > 0)
					content.append('<div class="pull-left"><a href="' + data.search + '">' + '<i class="fa fa-users"></i> ' + data.count + '</a></div>');
				
				if(data.subordinates > 0)
					content.append('<div class="subordinates" title="' + Lang.get('organization.hint_subdeps') + '">' + data.subordinates + '</div>');
			}
		};
		
		var orgchart = $('#dx-orgchart-container').orgchart(orgchartConfig);
		
		$("#dx-org-zoom-in").click(function()
		{
			orgchart.orgchart('zoom', 1.2);
		});
		$("#dx-org-zoom-out").click(function()
		{
			orgchart.orgchart('zoom', 0.8);
		});
		$("#dx-org-export").click(function()
		{
			$('.oc-export-btn', orgchart).click();
		});
	});
})(jQuery);