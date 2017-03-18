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
			data: orgchartData.source,
			nodeContent: 'title',
			verticalDepth: 3,
			depth: orgchartData.displayLevels,
			toggleSiblingsResp: true,
			pan: true,
			exportButton: true,
			// customize node creation process
			createNode: function(node, data)
			{
				var content = $(node).children('.content');
				content.wrapInner('<div class="text"></div>');
				
				//content.prepend('<a class="link" href="' + data.href + '"><img src="' + data.avatar + '" alt=""></a>');
				
				if(data.subordinates > 0)
					content.append('<div class="subordinates" title="' + Lang.get('organization.hint_subord') + '">' + data.subordinates + '</div>');
				
				// add up arrow button to top node
				if(data.top && (typeof data.parentUrl !== 'undefined'))
				{
					console.log(data.name + ' has parent, ' + data.parentUrl);
					$('<i class="edge verticalEdge topEdge fa"></i>')
						.appendTo(node)
						.click(function(e)
						{
							e.preventDefault();
							e.stopPropagation();
							location.href = data.parentUrl;
						});
				}
				
				$('.title', node).wrapInner('<a href="' + data.href + '"></a>');
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