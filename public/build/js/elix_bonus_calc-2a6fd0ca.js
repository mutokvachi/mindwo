var bonusCalc = window.bonusCalc = {
    chartOpt: {
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            x: 'left',
            y: '40',
            data: ['Darba alga un piemaksas', 'Atvaļinājuma pabalsts', 'Prēmija', 'Iemaksas pensiju fondā', 'Uzkrājošā veselības apdrošināšana']
        },
        calculable: true
    },
    calculate: function () {
        var salary = bonusCalc.prepareValue('#salary'),
                dependents = bonusCalc.prepareValue('#dependents'),
                bonus = bonusCalc.prepareValue('#bonus') / 100,
                regular_bonus = bonusCalc.prepareValue('#regular_bonus');

        var pension_fund_k = bonusCalc.getCheckboxVal('#pension_fund', 1),
                union_member_k = bonusCalc.getCheckboxVal('#union_member', 0),
                shift_night_work_k = bonusCalc.getCheckboxVal('#shift_night_work', 0);

        //
        // Data for first table
        //
        var salary_and_deposit_month_b = (salary + (salary * shift_night_work_k) + regular_bonus).toFixed(2);
        var salary_and_deposit_month_n = (salary_and_deposit_month_b - (salary_and_deposit_month_b * 0.105) - ((salary_and_deposit_month_b - (salary_and_deposit_month_b * 0.105) - 75 - (dependents * 175)) * 0.23) - (salary_and_deposit_month_b * union_member_k)).toFixed(2);
        var salary_and_deposit_year_b = (salary_and_deposit_month_b * 12).toFixed(2);
        var salary_and_deposit_year_n = (salary_and_deposit_month_n * 12).toFixed(2);

        var holiday_year_b = (salary * 0.5).toFixed(2);
        var holiday_month_b = (holiday_year_b / 12).toFixed(2);
        var holiday_year_n = (holiday_year_b - (holiday_year_b * 0.105) - ((holiday_year_b - (holiday_year_b * 0.105)) * 0.23) - (holiday_year_b * union_member_k)).toFixed(2);
        var holiday_month_n = (holiday_year_n / 12).toFixed(2);

        var bonus_year_b = ((salary * 12) * bonus).toFixed(2);
        var bonus_year_n = (bonus_year_b - (bonus_year_b * 0.105) - ((bonus_year_b - (bonus_year_b * 0.105)) * 0.23) - (bonus_year_b * union_member_k)).toFixed(2);
        var bonus_month_b = (bonus_year_b / 12).toFixed(2);
        var bonus_month_n = (bonus_month_b - (bonus_month_b * 0.105) - ((bonus_month_b - (bonus_month_b * 0.105)) * 0.23) - (bonus_month_b * union_member_k)).toFixed(2);

        var total_salary_year_b = parseFloat(salary_and_deposit_year_b) + parseFloat(holiday_year_b) + parseFloat(bonus_year_b);
        var total_salary_year_n = parseFloat(salary_and_deposit_year_n) + parseFloat(holiday_year_n) + parseFloat(bonus_year_n);
        var total_salary_month_b = parseFloat(salary_and_deposit_month_b) + parseFloat(holiday_month_b) + parseFloat(bonus_month_b);
        var total_salary_month_n = parseFloat(salary_and_deposit_month_n) + parseFloat(holiday_month_n) + parseFloat(bonus_month_n);

        $("#salary_and_deposit_month_b").html(salary_and_deposit_month_b);
        $("#salary_and_deposit_month_n").html(salary_and_deposit_month_n);
        $("#salary_and_deposit_year_b").html(salary_and_deposit_year_b);
        $("#salary_and_deposit_year_n").html(salary_and_deposit_year_n);

        $("#holiday_month_b").html(holiday_month_b);
        $("#holiday_month_n").html(holiday_month_n);
        $("#holiday_year_b").html(holiday_year_b);
        $("#holiday_year_n").html(holiday_year_n);

        $("#bonus_month_b").html(bonus_month_b);
        $("#bonus_month_n").html(bonus_month_n);
        $("#bonus_year_b").html(bonus_year_b);
        $("#bonus_year_n").html(bonus_year_n);

        $("#total_salary_month_b").html(total_salary_month_b.toFixed(2));
        $("#total_salary_month_n").html(total_salary_month_n.toFixed(2));
        $("#total_salary_year_b").html(total_salary_year_b.toFixed(2));
        $("#total_salary_year_n").html(total_salary_year_n.toFixed(2));

        //
        // End of first table
        //

        // Data for second table
        // 
        var k = 0.05;
        var insurance = 300;

        var pension_contribution_month = (total_salary_month_b * k * pension_fund_k).toFixed(2);
        var pension_contribution_year = (total_salary_year_b * k * pension_fund_k).toFixed(2);

        $("#pension_contribution_month").html(pension_contribution_month);
        $("#pension_contribution_year").html(pension_contribution_year);

        var acucumulating_insurance_year = insurance;
        var acucumulating_insurance_month = acucumulating_insurance_year / 12;

        $("#acucumulating_insurance_month").html(acucumulating_insurance_month);
        $("#acucumulating_insurance_year").html(acucumulating_insurance_year);

        var goods_month = parseFloat(pension_contribution_month) + parseFloat(acucumulating_insurance_month);
        var goods_year = parseFloat(pension_contribution_year) + parseFloat(acucumulating_insurance_year);

        $("#goods_month").html(goods_month.toFixed(2));
        $("#goods_year").html(goods_year.toFixed(2));

        /* ***** */

        var total_income_month = (goods_month + total_salary_month_b).toFixed(2);
        var total_income_year = (goods_year + total_salary_year_b).toFixed(2);

        $("#total_income_month").html(total_income_month);
        //$("#total_income_year").html(total_income_year);

        var paid_taxes_month = (total_salary_month_b - total_salary_month_n).toFixed(2);
        var paid_taxes_year = (total_salary_year_b - total_salary_year_n).toFixed(2);

        $("#paid_taxes_month").html(paid_taxes_month);
        $("#paid_taxes_year").html(paid_taxes_year);

        var chartData1 = [
            {value: salary_and_deposit_year_b, name: 'Darba alga un piemaksas'},
            {value: holiday_year_b, name: 'Atvaļinājuma pabalsts'},
            {value: bonus_year_b, name: 'Prēmija'},
            {value: pension_contribution_year, name: 'Iemaksas pensiju fondā'},
            {value: insurance, name: 'Uzkrājošā veselības apdrošināšana'}
        ];

        var chartData2 = [
            {value: salary_and_deposit_year_n, name: 'Darba alga un piemaksas'},
            {value: holiday_year_n, name: 'Atvaļinājuma pabalsts'},
            {value: bonus_year_n, name: 'Prēmija'},
            {value: pension_contribution_year, name: 'Iemaksas pensiju fondā'},
            {value: insurance, name: 'Uzkrājošā veselības apdrošināšana'}
        ];

        bonusCalc.drawChart(chartData1, 'chart1', 'Bruto ienākumi');
        bonusCalc.drawChart(chartData2, 'chart2', 'Ienākumi pēc nodokļu nomaksas');
    },
    prepareValue: function (element) {
        var elementValue = $(element).val();
        if (elementValue.length > 0) {
            return parseFloat(elementValue);
        } else {
            return 0;
        }

    },
    getCheckboxVal: function (element, def_val) {
        if ($(element).prop('checked') === true) {
            return $(element).val();
        } else {
            return def_val;
        }
    },
    drawChart: function (chartData, chartHolderId, name) {

        $(".display-none").show();

        var myChart = echarts.init(document.getElementById(chartHolderId));
        var opt = bonusCalc.chartOpt;

        opt.title = {
            text: name
        };

        opt.series = [
            {
                name: name,
                type: 'pie',
                radius: '40%',
                center: ['50%', '75%'],
                itemStyle: {
                    normal: {
                        label: {
//                            position: 'inner',
                            color: '#1e90ff',
                            formatter: function (params) {
                                return (params.percent - 0).toFixed(0) + '%'
                            }
                        },
//                        labelLine: {
//                            show: false
//                        }
                    },
                },
                data: chartData
            }
        ];

        myChart.setOption(opt);
    }
};

(function () {

    $("#calculate_btn").on('click', function () {
        bonusCalc.calculate();

        $('html, body').animate({
            scrollTop: $("#chartsHolder").offset().top - 80
        }, 2000);

    });


})();
//# sourceMappingURL=elix_bonus_calc.js.map
