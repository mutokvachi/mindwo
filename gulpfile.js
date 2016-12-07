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

    elixir(function(mix) {

        // Core styles for main page - plugins
        mix.styles([
            'pace/themes/pace-theme-flash.css',
            'bootstrap-switch/css/bootstrap-switch.css', 
            'morris/morris.css', 
            'jqvmap/jqvmap/jqvmap.css',
            'fullcalendar/fullcalendar.min.css', 
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
            'bootstrap-daterangepicker/daterangepicker.min.css'
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
            'toastr/toastr.js',
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
            'datatables/plugins/bootstrap/datatables.bootstrap.js'
        ], 'public/js/elix_view.js', 'resources/assets/plugins');

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
           'mindwo/pages/empl_links_fix.js',
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
            'css/elix_employee_profile.css'
        ]);
    });
});

gulp.task('default', ['langjs','mix_all']);