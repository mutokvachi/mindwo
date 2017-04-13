/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

var gulp = require('gulp');
var exec = require('child_process').exec;
var elixir = require('laravel-elixir');
var babel = require('laravel-elixir-babel');

gulp.task('langjs', function () {
    // cd command is needed to navigate to path where gulp was executed because on some environments there is problem with incorrect starting path 
    exec('cd "' + process.cwd() + '" & php artisan lang:js public/js/lang.js',
            function (err, stdout, stderr) {
                console.log(stdout);
                console.log(stderr);
                //gulp.start('mix_all');
            });
});

gulp.task('mix_all', function() {

    gulp.src('./resources/assets/plugins/mxgraph/**/*')
            .pipe(gulp.dest('./public/js/plugins/mxgraph'));

    elixir(function(mix) {

        // Prepare less styles for elix_view.css
        mix.less([
            '/pages/crypto/crypto.less'
        ], 'public/css/elix_view_less.css');

        // Core styles for main page - plugins
        mix.styles([
            'pace/themes/pace-theme-flash.css',
            'bootstrap-switch/css/bootstrap-switch.css',
            'morris/morris.css',
            'jqvmap/jqvmap/jqvmap.css',
            'fullcalendar/fullcalendar.min.css',
            'resources/assets/plugins/datetimepicker/jquery.datetimepicker.css',
            'metronic/css/faq.min.css',
            'metronic/css/components-md.css',
            'metronic/css/components.css',
            'metronic/css/profile-2.css',
            'bootstrap-modal/css/bootstrap-modal.css',
            'toastr/toastr.min.css',
            'tooltipster-master/css/tooltipster.css',
            'tooltipster-master/css/themes/tooltipster-light.css',
            'animate.css'
        ], 'public/css/elix_plugins.css', 'resources/assets/plugins');

        // Core styles for main page - custom made
        mix.styles([
            'mindwo/css/main.css',
            'mindwo/css/ie9_fix.css',
            'mindwo/css/search_top.css',
            'mindwo/css/splash.css',
            'mindwo/css/theme_fix.css'
        ], 'public/css/elix_mindwo.css', 'resources/assets/plugins');        

        // Styles for view page
        mix.styles([
            'datetimepicker/jquery.datetimepicker.css',
            'dropzone/dropzone.min.css',
            'dropzone/basic.min.css',
            'select2/select2-bootstrap.css',
            'codemirror/css/codemirror.css',
            'codemirror/css/ambiance.css',
            'jasny-bootstrap/css/jasny-bootstrap.min.css',
            'jquery-nestable/jquery.nestable.css',
            'mindwo/css/view.css',
            'datatables/plugins/bootstrap/datatables.bootstrap.css',
            'datatables/datatables.min.css',
            'bootstrap-daterangepicker/daterangepicker.min.css',
            '../../../public/css/elix_view_less.css',
                    //'../less/pages/visual_workflow.less'
        ], 'public/css/elix_view.css', 'resources/assets/plugins');

        // horizontal menu UI styles                
        mix.less([
            'horizontal_ui.less',
            'bootstrap_menu.less',
            'multilevel_menu.less',
            'empl_profile.less'
        ], 'public/css/elix_mindwo_horizontal.css');

        // Metronic theme UI styles                
        mix.less([
            'metronic_ui.less',
        ], 'public/css/elix_metronic.css');

        // Styles for articles search page
        mix.styles([
            'cubeportfolio/css/cubeportfolio.css',
            'mindwo/css/search_tools.css',
            'bootstrap-daterangepicker/daterangepicker.min.css'
        ], 'public/css/elix_articles.css', 'resources/assets/plugins');

        // Scripts for horizontal menu UI
        mix.scripts([
            'mindwo/pages/horizontal_menu.js',
            'mindwo/pages/horizontal_form_ui.js'
        ],
                'public/js/elix_mindwo_horizontal_menu.js', 'resources/assets/plugins');

        // Scripts for documents search page
        mix.scripts([
            'mindwo/pages/documents.js'
        ],
                'public/js/elix_documents.js', 'resources/assets/plugins');

        // Core scripts for main blade view - will be included in all pages
        mix.scripts([
            'jquery.min.js',
            'bootstrap/js/bootstrap.min.js',
            'js.cookie.min.js',
            'bootstrap-hover-dropdown/bootstrap-hover-dropdown.js',
            'jquery-slimscroll/jquery.slimscroll.js',
            'metronic/jquery.blockui.min.js',
            'bootstrap-switch/js/bootstrap-switch.js',
            'gritter/jquery.gritter.min.js',
            'toastr/toastr.min.js',
            'bootstrap-modal/js/bootstrap-modalmanager.js',
            'bootstrap-modal/js/bootstrap-modal.js',
            'jquery.cookie.js',
            'tooltipster-master/js/jquery.tooltipster.js',
            'mindwo/dx_core.js',
            'metronic/app.js',
            'jquery-ui/jquery-ui.min.js',
            'fullcalendar/moment.min.js',
            'fullcalendar/fullcalendar.min.js',
            'fullcalendar/lang-all.js',
            'mindwo/blocks/calendar.js',
            'resources/assets/plugins/datetimepicker/jquery.datetimepicker.js',
            'mindwo/blocks/employee_count.js',
            'metronic/layout.js',
            'metronic/demo.js',
            'metronic/quick-sidebar.js',
            'mindwo/pages/main.js',
            'mindwo/pages/employees_links.js',
            'mindwo/pages/search_top.js',
            'validator/validator.js',
            'mindwo/pages/re_login.js',
            'bootstrap-tabdrop/js/bootstrap-tabdrop.js'
        ],
                'public/js/elix_plugins.js', 'resources/assets/plugins');

        // Scripts for loged in users - will be included in main blade in case of authorizes user
        mix.scripts(['mindwo/pages/search_top.js', 'mindwo/pages/userlinks.js'], 'public/js/elix_userlinks.js', 'resources/assets/plugins');

        // Scripts for grids/forms functionality
        mix.scripts([
            'moment.min.js',
            'bootstrap-daterangepicker/daterangepicker.js',
            'bootstrap-colorpicker/js/bootstrap-colorpicker.js',
            'dropzone/dropzone.min.js',
            'jasny-bootstrap/js/jasny-bootstrap.js',
            'tree/jstree.min.js',
            'file_download.js',
            'codemirror/js/codemirror.js',
            'codemirror/js/mode/javascript/javascript.js',
            'datetimepicker/jquery.datetimepicker.js',
            'select2/select2.min.js',
            'select2/select2_locale_multi.js',
            'jquery-nestable/jquery.nestable.js',
            'mindwo/pages/date_range.js',
            'mindwo/dx_forms_core.js',
            'mindwo/pages/search_tools.js',
            'mindwo/dx_grids_core.js',
            'mindwo/blocks/view.js',
            'mindwo/pages/task_logic.js',
            'mindwo/pages/form_logic.js',
            'mindwo/fields/tree.js',
            'mindwo/fields/rel_id.js',
            'mindwo/fields/autocompleate.js',
            'mindwo/fields/datetime.js',
            'mindwo/fields/bool.js',
            'datatables/datatables.all.min.js',
            'datatables/plugins/bootstrap/datatables.bootstrap.js',
            'float-thead/dist/jquery.floatThead.js',
            'mindwo/crypto/crypto.js',
            'mindwo/crypto/crypto_field.js',
            'mindwo/crypto/crypto_user_panel.js',
                    //'mindwo/visual_ui/workflow.js',
                    //'mxgraph/src/js/mxClient.js',
        ], 'public/js/elix_view.js', 'resources/assets/plugins');

        mix.less([
            'blocks/report.less'
        ], 'public/css/elix_block_report_main.css');

        mix.styles([
            'bootstrap-daterangepicker/daterangepicker.min.css'
        ], 'public/css/elix_block_report_plugins.css', 'resources/assets/plugins');

        mix.styles([
            'elix_block_report_main.css',
            'elix_block_report_plugins.css'
        ], 'public/css/elix_block_report.css', 'public/css');

        mix.scripts([
            'moment.min.js',
            'bootstrap-daterangepicker/daterangepicker.js',
            'mindwo/blocks/report.js',
            'flot/jquery.flot.min.js',
            'flot/jquery.flot.orderBars.js',
            'flot/jquery.flot.resize.min.js',
            'flot/jquery.flot.axislabels.js'
        ], 'public/js/elix_block_report.js', 'resources/assets/plugins');

        // Scripts for employees search page functionality
        mix.scripts([
            'jquery.simulate.js',
            'mindwo/pages/search_tools.js',
            'mindwo/pages/employees.js'
        ], 'public/js/elix_employees.js', 'resources/assets/plugins');


        // Scripts for employee profile
        mix.scripts([
            'mindwo/pages/freeform.js',
            'mindwo/pages/inlineform.js',
            //'mindwo/pages/empl_links_fix.js',
            'mindwo/pages/employee/personal_docs.js',
            'mindwo/pages/employee/notes.js',
            'mindwo/pages/employee/timeoff.js',
            'counterup/jquery.counterup.min.js',
            'counterup/jquery.waypoints.min.js',
            'flot/jquery.flot.min.js',
            'flot/jquery.flot.resize.min.js',
            'flot/jquery.flot.axislabels.js'
        ], 'public/js/elix_profile.js', 'resources/assets/plugins');

        // LESS Styles for employee profile                
        mix.less([
            'pages/employee/personal_docs.less',
            'pages/employee/notes.less',
            'pages/employee/timeoff.less'
        ], 'public/css/elix_employee_profile.css');

        // script for birthdays searching widget
        //$this->addJSInclude('metronic/global/plugins/moment.min.js');
        //$this->addJSInclude('metronic/global/plugins/bootstrap-daterangepicker/daterangepicker.min.js');
        //$this->addJSInclude('plugins/tree/jstree.min.js');
        //$this->addJSInclude('js/pages/employees_links.js');
        //$this->addJSInclude('js/pages/search_tools.js');
        //$this->addJSInclude('js/pages/date_range.js');
        //$this->addJSInclude('js/blocks/emplbirth.js');

        mix.scripts([
            'moment.min.js',
            'bootstrap-daterangepicker/daterangepicker.js',
            'tree/jstree.min.js',
            'mindwo/pages/search_tools.js',
            'mindwo/pages/date_range.js',
            'mindwo/blocks/emplbirth.js'
        ], 'public/js/elix_birth.js', 'resources/assets/plugins');

        // Scripts for articles search page functionality
        mix.scripts([
            'cubeportfolio/js/jquery.cubeportfolio.js',
            'jscroll/jquery.jscroll.js',
            'moment.min.js',
            'bootstrap-daterangepicker/daterangepicker.js',
            'mindwo/pages/date_range.js',
            'mindwo/pages/search_tools.js',
            'mindwo/pages/articles.js'
        ], 'public/js/elix_articles.js', 'resources/assets/plugins');

        // Scripts for organization chart
        mix.scripts([
            'babel-polyfill/polyfill.js',
            'select2/select2.min.js',
            'select2/select2_locale_multi.js',
            'html2canvas/html2canvas.js',
            'orgchart/jquery.orgchart.js',
            'mindwo/pages/organization_chart.js'
        ], 'public/js/elix_orgchart.js', 'resources/assets/plugins');

        // Styles for organization chart
        mix.styles([
            'select2/select2.css',
            'select2/select2-bootstrap.css',
            'orgchart/jquery.orgchart.css',
            'mindwo/css/organization_chart.css'
        ], 'public/css/elix_orgchart.css', 'resources/assets/plugins');

        // Scripts for departments chart
        mix.scripts([
            'babel-polyfill/polyfill.js',
            'html2canvas/html2canvas.js',
            'orgchart/jquery.orgchart.js',
            'mindwo/pages/organization_departments.js'
        ], 'public/js/elix_orgdepartments.js', 'resources/assets/plugins');

        // Styles for departments chart
        mix.styles([
            'orgchart/jquery.orgchart.css',
            'select2/select2.css',
            'select2/select2-bootstrap.css',
            'mindwo/css/organization_chart.css',
            'mindwo/css/organization_departments.css'
        ], 'public/css/elix_orgdepartments.css', 'resources/assets/plugins');

        // Styles for mail interface
        mix.styles([
            'resources/assets/plugins/select2-4.0/css/select2.css',
            'resources/assets/plugins/datetimepicker/jquery.datetimepicker.css',
            'public/metronic/global/plugins/select2/css/select2-bootstrap.min.css',
            'public/metronic/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css',
            'public/metronic/apps/css/inbox.css',
            'resources/assets/plugins/mindwo/css/mail.css'
        ], 'public/css/elix_mail.css', './');

        // Scripts for mail interface
        mix.scripts([
            'resources/assets/plugins/select2-4.0/js/select2.js',
            'resources/assets/plugins/datetimepicker/jquery.datetimepicker.js',
            'resources/assets/plugins/mindwo/fields/datetime.js',
            'resources/assets/plugins/numeral-js/numeral.js',
            'public/metronic/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js',
            'public/metronic/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js',
            'resources/assets/plugins/mindwo/pages/mail.js'
        ], 'public/js/elix_mail.js', './');

        // Minify all scripts
        mix.version([
            'js/elix_userlinks.js',
            'js/elix_plugins.js',
            'js/elix_view.js',
            'js/elix_employees.js',
            'js/elix_profile.js',
            'css/elix_plugins.css',
            'css/elix_mindwo.css',
            'css/elix_view.css',
            'css/elix_mindwo_horizontal.css',
            'js/elix_mindwo_horizontal_menu.js',
            'js/elix_documents.js',
            'css/elix_metronic.css',
            'js/elix_articles.js',
            'css/elix_articles.css',
            'css/elix_employee_profile.css',
            'js/elix_orgchart.js',
            'css/elix_orgchart.css',
            'js/elix_orgdepartments.js',
            'css/elix_orgdepartments.css',
            'js/elix_block_report.js',
            'css/elix_block_report.css',
            'js/elix_mail.js',
            'css/elix_mail.css',
            'js/elix_birth.js'
        ]);
    });
});

gulp.task('default', ['langjs','mix_all']);
