/**
 * Datumu intervāla uzstādīšanas komponentes JavaScript funkcionalitāte
 * Lapā pieļaujama tikai 1 datumu intervāla uzstādīšanas komponente
 * 
 * @type _L4.Anonym$0|Function
 */
var DateRange = function()
{ 
    /**
     * Meklēšanas rīku HTML formas elements
     * 
     * @type Object
     */
    var form_elem = null;
    
    /**
     *  Masīvs ar mēneša nosaukumiem latviski
     * @type Array
     */
    var arr_month = ['Janvāris', 'Februāris', 'Marts', 'Aprīlis', 'Maijs', 'Jūnijs', 'Jūlijs', 'Augusts', 'Septembris', 'Oktobris', 'Novembris', 'Decembris'];
    
    /**
     * Masīvs ar komonentes uzstādījumiem
     * 
     * @type Array
     */
    var arr_params = [];
    
    /**
     * Funkcija, kas parāda vai paslēpj meklešanas rīku blokā saiti "Notīrīt"
     * @type type
     */
    var clearLinkShowHide = null;
    
    /**
     * Formatē datumu no yyyy-mm-dd uz formātu dd.mm.yyyy
     * 
     * @param {string} dat Formatējamais datums
     * @returns {String} Datums formatēts dd.mm.yyyy
     */
    var getDateLatvian = function(dat) {
        
        if (dat.length == 0) {
            return '';
        }
        
        var arr_parts = dat.split('-');
        return arr_parts[2] + "." + arr_parts[1] + "." + arr_parts[0];
    };
    
    /**
     * Formatē datumu no yyyy-mm-dd uz formātu 24 Jūlijs, 2016
     * 
     * @param {String} dat Formatējamais datums
     * @returns {String} Datums formatēts 24 Jūlijs, 2016
     */
    var getDateLatvianLong = function(dat) {
        if (dat.length == 0) {
            return '';
        }
        
        var arr_parts = dat.split('-');
        
        return parseInt(arr_parts[2]) + " " + arr_month[parseInt(arr_parts[1])-1] + ", " + arr_parts[0];
    };
    
    /**
     * Izgūst datuma intervālu latviešu valodā, piemēram, 24 Jūlijs, 2016 - 28 Jūlijs, 2016
     * @param {String} start_date Sākuma datums formātā yyyy-mm-dd
     * @param {String} end_date Beigu datums formātā yyyy-mm-dd
     * @returns {String} Datuma intervāls
     */
    var getDateIntervalLV = function (start_date, end_date) {
        
        if (start_date.length == 0 || end_date.length == 0) {
            return '';
        }
        
        return getDateLatvianLong(start_date) + " - " + getDateLatvianLong(end_date);
        
    }
    
    /**
     * Uzstāda datuma intervāla izveles funkcionalitāti
     * 
     * @returns {undefined}
     */
    var handleDateRangePickers = function () {
        if (!jQuery().daterangepicker) {
            return;
        }
        
        $('#' + arr_params['range_id']).daterangepicker({
                opens: (App.isRTL() ? 'left' : 'right'),
                locale: {
                    "format": "DD.MM.YYYY",
                    "separator": " - ",
                    "applyLabel": "Labi",
                    "cancelLabel": "Atcelt",
                    "fromLabel": "No",
                    "toLabel": "Līdz",
                    "customRangeLabel": "Intervāls",
                    "daysOfWeek": [
                        "Sv",
                        "P",
                        "O",
                        "T",
                        "C",
                        "P",
                        "S"
                    ],
                    "monthNames": arr_month,
                    "firstDay": 1
                },
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                ranges: arr_params['arr_ranges'],
                autoUpdateInput: true,
                alwaysShowCalendars: true,
            },
            function (start, end) {                
                $('#' + arr_params['range_id'] + ' input').val(getDateIntervalLV(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD')));
            }
        );
        
        $('#' + arr_params['range_id']).on('apply.daterangepicker', function(ev, picker) {
            form_elem.find('[name=' + arr_params['el_date_from'] + ']').val(picker.startDate.format('YYYY-MM-DD'));
            form_elem.find('[name=' + arr_params['el_date_to'] + ']').val(picker.endDate.format('YYYY-MM-DD'));
            clearLinkShowHide();
        });
        
        var start_date = form_elem.find('[name=' + arr_params['el_date_from'] + ']').val();
        var end_date = form_elem.find('[name=' + arr_params['el_date_to'] + ']').val();
        
        if (start_date && end_date)
        {
            $('#' + arr_params['range_id']).data('daterangepicker').setStartDate(getDateLatvian(start_date));
            $('#' + arr_params['range_id']).data('daterangepicker').setEndDate(getDateLatvian(end_date));
            $('#' + arr_params['range_id'] + ' input').val(getDateIntervalLV(start_date, end_date));
        }

    };
    
    /**
     * Inicializē komponentes JavaScript funkcionalitāti
     * 
     * @returns {undefined}
     */
    var initComponent = function(arr, clearLinkShowHide_callback) {               
        
        arr_params = arr;
        clearLinkShowHide = clearLinkShowHide_callback;
        
        var page_elem = $(arr_params["page_selector"]);
        form_elem = page_elem.find(arr_params["form_selector"]);
        
        handleDateRangePickers();
       
    };
    

    return {
        init: function(arr, clearLinkShowHide_callback) {
            initComponent(arr, clearLinkShowHide_callback);
        }
    };
}();