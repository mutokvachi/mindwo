var el_sal_type = form_object.find("[name=salary_type_id]");
var el_sal = form_object.find("[name=salary]");
var el_sal_annual = form_object.find("[name=annual_salary]");

if (el_sal_type && el_sal && el_sal_annual)
{
	el_sal_annual.prop("readonly", true);
        
        function round2Fixed(value) {
            value = +value;

            if (isNaN(value))
              return NaN;

            // Shift
            value = value.toString().split('e');
            value = Math.round(+(value[0] + 'e' + (value[1] ? (+value[1] + 2) : 2)));

            // Shift back
            value = value.toString().split('e');
            return (+(value[0] + 'e' + (value[1] ? (+value[1] - 2) : -2))).toFixed(2);
        };

	var calculate = function()
	{
            var sal = el_sal.val().replace(",", ".");
            if (!$.isNumeric(sal)) {
                sal = 0;
            }
            
            var annual = 0;
            if (parseInt(el_sal_type.val()) == 1) {
                annual = round2Fixed(12 * sal);
            }
            else {
                annual = round2Fixed(12 * sal * 160);
            }
            el_sal_annual.val(annual);            
	};
	
	el_sal_type.on('change', calculate);
        el_sal.on('change', calculate);
        el_sal.on('keyup', calculate);        
}