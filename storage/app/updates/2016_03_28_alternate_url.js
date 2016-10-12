var tit = form_object.find("input[name=title]");
var altern = form_object.find("input[name=alternate_url]");
var sel = form_object.find("[name=content_id]");

if (tit.length && altern.length && sel.length)
{	
	var slug = function(str) {
	  str = str.replace(/^\s+|\s+$/g, ''); // trim
	  str = str.toLowerCase();

	  // remove accents, swap ñ for n, etc
	  var from = "āčēģīķļņšūž·/_,:;";
	  var to   = "acegiklnsuz------";
	  for (var i=0, l=from.length ; i<l ; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	  }

	  str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		.replace(/\s+/g, '-') // collapse whitespace and replace by -
		.replace(/-+/g, '-'); // collapse dashes

	  return str;
	};
	
	var change_event = function(e)
	{
		if (e && altern.is(":visible"))
		{
			altern.val(slug($(this).val()));
		}
		else
		{
			altern.val('');
		}
	}
	
	var change_type = function(e)
	{
		if (e)
		{
			if (sel.val() != 1)
			{
				altern.val('');
			}
                        else
                        {
                            altern.val(slug(tit.val()));
                        }
		}
	}
	
	tit.on('keyup', change_event);
	tit.on('change', change_event);
	
	sel.on('change', change_type);
}