window.DailyQuest = window.DailyQuest || {
    /**
     * Bloka unikālais identifikators
     */
    block_guid: '',
    /**
     * Datu avota ID
     */
    source_id: '',
    /**
     * Atbilžu variantu saraksts
     */
    option_list: '',
    /**
     * Pazīme vai jautājuma ir vairākas atbildes
     */
    is_multi_answer: '',
    /**
     * Pazīme vai jautājums ir atbildēts
     */
    is_answered: '',
    /**
     * Pazīme vai jautājumam ir vairāki atbilžu varianti
     */
    has_legend: '',
    /**
     * Diagrammas krāsu palete
     */
    chart_colors: '',
    /**
     * Saglabā iesniegto atbildi
     * @returns {undefined}
     */
    saveAnswer: function() {
        var answers = [];
        $('#dailyquest-' + window.DailyQuest.block_guid + '-options input:checked').each(function()
        {
            answers.push($(this).attr('value'));
        });

        var postData = {
            param: "OBJ=DAILYQUEST|SOURCE=" + window.DailyQuest.source_id,
            answers: JSON.stringify(answers)
        };

        var ajax_url = '/block_ajax';

        var request = new FormAjaxRequestIE9(ajax_url, "", "", postData);
        request.progress_info = "Saglabā datus. Lūdzu, uzgaidiet...";

        request.callback = function(data)
        {
            window.DailyQuest.drawResults(data.data);
        };

        request.err_callback = function(err_txt)
        {

        };

        // izpildam AJAX pieprasījumu
        request.doRequest();
    },
    /**
     * Iegūst leģendas incializācijas datus
     * 
     * @returns {object} Leģendas dati
     */
    initiChartLegend: function() {
        if (1 == window.DailyQuest.has_legend) {
            return {
                container: $('#dailyquest-' + window.DailyQuest.block_guid + '-chart-legend')
            };
        } else {
            return {
                show: false
            };
        }
    },
    /**
     * Uzzīmē pīrāga diagrammu
     * @param {type} rawData Aptaujas atbilžu dati
     * @returns {undefined}
     */
    initPie: function(rawData) {
        var chartData = [];

        $.each(rawData, function(k, v) {
            //display the key and value pair
            chartData[k] = {
                label: v["option_text"],
                data: v["answer_count"]
            };

            if (k in window.DailyQuest.chart_colors) {
                chartData[k].color = window.DailyQuest.chart_colors[k];
            }
        });

        var placeholder = $('#dailyquest-' + window.DailyQuest.block_guid + '-chart');

        placeholder.height('250px');
        placeholder.width('100%');

        placeholder.bind("plothover", function(event, pos, item) {
            if (item) {
                $("#dx-chart-tooltip").html(item.series.label + " (" + item.datapoint[1][0][1] + ") - " + item.series.percent.toFixed(2) + "%")
                        .css({top: pos.pageY + 20, left: pos.pageX + 5})
                        .fadeIn(200);
            } else {
                $("#dx-chart-tooltip").hide();
            }
        });

        var legend_options = window.DailyQuest.initiChartLegend();

        var options = {
            series: {
                pie: {
                    show: true,
                    radius: 3 / 4,
                    label: {
                        show: true,
                        formatter: function(label, series) {
                            return '<div style="color:white;">' + series.percent.toFixed(2) + '%</div>';
                        },
                        threshold: 0.01,
                        radius: 3 / 4,
                        background: {
                            opacity: 0.5,
                            color: '#000'
                        }
                    }
                }
            },
            legend: legend_options,
            grid: {
                hoverable: true
            }
        };

        $.plot(placeholder, chartData, options);
    },
    /**
     * Uzzīmē stabiņu diagrammu
     * @param {type} rawData Aptaujas atbilžu dati
     * @returns {undefined}
     */
    initColumns: function(rawData) {
        var chartData = [];
        var labelsData = [];

        $.each(rawData, function(k, v) {
            //display the key and value pair
            chartData[k] = {
                data: [[k, v["answer_count"]]]
            };

            if (k in window.DailyQuest.chart_colors) {
                chartData[k].color = window.DailyQuest.chart_colors[k];
            }

            labelsData[k] = [k, v["option_text"]];
        });

        var placeholder = $('#dailyquest-' + window.DailyQuest.block_guid + '-chart');

        placeholder.height('300px');
        placeholder.width('100%');

        placeholder.bind("plothover", function(event, pos, item) {
            if (item) {
                $("#dx-chart-tooltip").html(labelsData[item.datapoint[0]][1] + " (" + item.datapoint[1] + ")")
                        .css({top: pos.pageY + 20, left: pos.pageX + 5})
                        .fadeIn(200);
            } else {
                $("#dx-chart-tooltip").hide();
            }
        });

        var legend_options = window.DailyQuest.initiChartLegend();

        var options = {
            series: {
                bars: {
                    show: true,
                    barWidth: 0.6,
                    align: "center"
                }
            },
            xaxis: {
                ticks: labelsData,
                tickLength: 0,
                labelWidth: 50
            },
            yaxis: {
                tickDecimals: 0
            },
            grid: {
                hoverable: true
            },
            legend: legend_options
        };

        // options.legend.show = true;


        $.plot(placeholder, chartData, options);
    },
    /**
     * Uzzīmē diagrammu
     * @param {string} rawDataString Aptaujas dati
     * @returns {undefined}
     */
    drawResults: function(rawDataString) {
        $('#dailyquest-' + window.DailyQuest.block_guid + '-answer-panel').hide();
        $('#dailyquest-' + window.DailyQuest.block_guid + '-answer-label').show();

        $("<div id='dx-chart-tooltip'></div>").css({
            position: "absolute",
            display: "none",
            border: "1px solid #fdd",
            padding: "2px",
            "background-color": "#fee",
            opacity: 0.80
        }).appendTo("body");

        window.DailyQuest.drawChart(rawDataString);
    },
    drawChart: function(rawDataString) {
        var rawData = $.parseJSON(rawDataString);

        if (1 == window.DailyQuest.is_multi_answer) {
            window.DailyQuest.initColumns(rawData);
        }
        else {
            window.DailyQuest.initPie(rawData);
        }
    },
    /**
     * Uzstāda galeriju bloka parametrus
     * @param {object} block Galerijas bloka elements
     * @returns {undefined}
     */
    setParameters: function(block) {
        window.DailyQuest.block_guid = block.attr('dx_block_guid');
        window.DailyQuest.source_id = block.attr('dx_source_id');
        window.DailyQuest.option_list = block.attr('dx_option_list');
        window.DailyQuest.is_multi_answer = block.attr('dx_is_multi_answer');
        window.DailyQuest.is_answered = block.attr('dx_is_answered');
        window.DailyQuest.has_legend = block.attr('dx_has_legend');

        window.DailyQuest.chart_colors = [];
        if (block.attr('dx_chart_colors')) {
            var colors = block.attr('dx_chart_colors').split(',');
            if (colors.constructor === Array) {
                window.DailyQuest.chart_colors = colors;
            }
        }
    },
    /**
     * Inicializē dienas jautājuma komponenti
     * @returns {undefined}
     */
    init: function() {
        // Iegūst bloka elementu
        var block = $(".dx-block-container-dailyquest[dx_block_init='0']").first();

        // Iegūst parametrus
        window.DailyQuest.setParameters(block);

        if (1 == window.DailyQuest.is_answered) {
            window.DailyQuest.drawResults(window.DailyQuest.option_list);
        }
        else {
            $('#dailyquest-' + window.DailyQuest.block_guid + '-btnSave').click(window.DailyQuest.saveAnswer);
        }

        // Uzstāda pazīmi ka bloks ir inicializēts
        block.attr('dx_block_init', 1);

    }
}

$(document).ready(function() {
    window.DailyQuest.init();
});

