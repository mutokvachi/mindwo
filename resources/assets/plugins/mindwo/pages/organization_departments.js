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
		var orgchart = new OrgChart({
			chartContainer: '#dx-orgchart-container',
			data: orgchartData.source,
			nodeContent: 'title',
			depth: orgchartData.displayLevels,
			toggleSiblingsResp: true,
			pan: true,
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
		});
		$("#dx-org-zoom-in").click(function()
		{
			orgchart.set_zoom(-1);
		});
		$("#dx-org-zoom-out").click(function()
		{
			orgchart.set_zoom(1);
		});
		$("#dx-org-export").click(function()
		{
			orgchart._clickExportButton();
		});
	});
})(jQuery);