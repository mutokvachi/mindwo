/**
 * Sistēmu lapas funkcionalitāte.
 */
var SystemsBlock = {
    /**
     * jQuery objekts ar visiem sistēmu paneļiem.
     * 
     * @type {object}
     */
    systemPanels: {},

    /**
     * Aiziet..
     * 
     * @return {void}
     */
    init: function() {
        this.systemPanels = $('.panel-system');
        this.registerEvents();
        
        var emptyFunc = function() {};
        EmployeesLinks.init($(".dx-systems-page"), emptyFunc);
    },

    /**
     * Piereģistrē nepieciešamos jQuery notikumus.
     * 
     * @return {void}
     */
    registerEvents: function () {
        var self = this;

        /**
         * Iespējo vai atspējo pogu "Parādīt visu" atkarībā no filtrēšanas kritērijiem
         */
        var btnAllEnableDissable = function() {
            if ($('#sy-name').val().length == 0 && $('#sy-source').val() == 0) {
                $('#sy-all').prop('disabled', true);
            }
            else {
                $('#sy-all').prop('disabled', false);
            }
        };
        
        /**
         * Veic meklēšanu datu rindās pēc nosaukuma un/vai datu avota
         * 
         * @param {string} title Nosaukuma meklēšanas frāze
         * @param {integer} source Datu avota ID (ja 0, tad visi datu avoti)
         * @returns {undefined}
         */
        var searchRows = function(title, source) {
            // self.systemPanels.each(function() {
            //     if (($(this).data('sy-src') == source || source == 0) && ($(this).data('sy-name').indexOf(title) > -1) || title.length == 0) {
            //         $(this).show();
            //     } else {
            //         $(this).hide();
            //     }
            // });

            self.systemPanels.each(function() {
                if (($(this).data('sy-name').indexOf(title) > -1) || title.length == 0) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            
            btnAllEnableDissable();
        };
        
        /**
         * Sistēmas meklēšana pēc nosaukuma.
         */
        $('#sy-name').on('keyup', function() {
            var val = $(this).val().toLowerCase();

            if (val.length < 2 && val.length != 0) {
                return;
            }
            
            // searchRows(val, $('#sy-source').val());
            searchRows(val);
        });
        
        /**
         * Sistēmas meklēšana pēc datu avota.
         */
        $('#sy-source').on('change', function() {
            searchRows($('#sy-name').val(), $(this).val());
        });

        /**
         * Incidentu atlasīšana [Euroscreen komentārs: Laikam netiek izmantots]
         */
        $('#sy-inc').on('click', function() {
            self.systemPanels.each(function() {
                if ($(this).data('sy-inc') == 1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            $(this).hide();
            $('#sy-all').prop('disabled', false);
            $('#sy-name').val('').focus();
        });

        /**
         * Visu elementu atrādīšana.
         */
        $('#sy-all').on('click', function() {
            self.systemPanels.each(function() {
                $(this).show();
            });
            
            $('#sy-inc').show();
            $('#sy-source').val(0);
            $('#sy-name').val('').focus();
            
            btnAllEnableDissable();
        });

        /**
         * Visu elementu paplašināšana.
         */
        $('#sy-exp').on('click', function() {
            self.systemPanels.parent().find('.panel-collapse').collapse('show');
        });

        /**
         * Visu elementu sašaurināšana.
         */
        $('#sy-col').on('click', function() {
            self.systemPanels.parent().find('.panel-collapse').collapse('hide');
        });
    },
};

$(document).ready(function() {
    SystemsBlock.init();
});