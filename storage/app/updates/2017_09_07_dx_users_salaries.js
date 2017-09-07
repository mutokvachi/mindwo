var item_id = form_object.find("[name=item_id]");
var el_sal_type = form_object.find("[name=salary_type_id]");
var el_sal = form_object.find("[name=salary]");
var el_sal_annual = form_object.find("[name=annual_salary]");
var el_sal_annual_prob = form_object.find("[name=probation_salary_annual]");
var el_sal_prob = form_object.find("[name=probation_salary]");

if (el_sal_type && el_sal && el_sal_annual && el_sal_annual_prob && el_sal_prob)
{    
    if (parseInt(item_id.val())) {
        form_object.find(".dx-form-field-line[dx_fld_name_form=probation_salary_annual]").hide();
        form_object.find(".dx-form-field-line[dx_fld_name_form=probation_salary]").hide();
        form_object.find(".dx-form-field-line[dx_fld_name_form=probation_months]").hide();
    }

    el_sal_annual.prop("readonly", true);
    el_sal_annual_prob.prop("readonly", true);
        
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

	var calculate = function(el_salary, el_annual)
	{
            var sal = el_salary.val().replace(",", ".");
            if (!$.isNumeric(sal)) {
                sal = 0;
            }
            
            var annual = 0;
            if (parseInt(el_sal_type.val()) == 1) {
                annual = round2Fixed(12 * sal);
            }
            else if (parseInt(el_sal_type.val()) == 2) {
                annual = round2Fixed(12 * sal * 160);
            }
            else {
                annual = sal;
            }
            el_annual.val(annual);            
	};
    
    var calculate_regular = function() {
        calculate(el_sal, el_sal_annual);
    };

    var calculate_probation = function() {
        calculate(el_sal_prob, el_sal_annual_prob);
    };

	el_sal_type.on('change', function() {
        calculate_regular();
        calculate_probation();
    });

    el_sal.on('change', calculate_regular);
    el_sal.on('keyup', calculate_regular); 
    
    el_sal_prob.on('change', calculate_probation);
    el_sal_prob.on('keyup', calculate_probation);  
}