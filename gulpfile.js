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
var shell = require('gulp-shell');
var elixir = require('laravel-elixir');

gulp.task('langjs', function() {
    gulp.src('').pipe(shell('php artisan lang:js ' + 'public/js/lang.js'));
});
    
gulp.task('mix_all', function() {

    elixir(function(mix) {

        // Core styles for main page - plugins
        mix.styles([
            'pace/themes/pace-theme-flash.css', // ok
            //'simple-line-icons/simple-line-icons.css', // url fonts/
            //'uniform/css/uniform.default.css', // url ../images/
            'bootstrap-switch/css/bootstrap-switch.css', // ok
            'morris/morris.css', //ok
            'jqvmap/jqvmap/jqvmap.css', // ok
            //'icheck/skins/all.css', // @import ... seems must remove this css 
            'fullcalendar/fullcalendar.min.css', // ok
            'metronic/css/faq.min.css', // ok
            'bootstrap-modal/css/bootstrap-modal.css', //ok
            'toastr/toastr.min.css', //ok
            'tooltipster-master/css/tooltipster.css', // ok
            'tooltipster-master/css/themes/tooltipster-light.css' // ok        
        ], 'public/css/elix_plugins.css', 'resources/assets/plugins');

        // Core styles for main page - custom made
        mix.styles([
            'mindwo/css/main.css', // ok
            'mindwo/css/ie9_fix.css', //ok
            'mindwo/css/search_top.css', // ok
            'mindwo/css/splash.css', // ok
            'mindwo/css/theme_fix.css' // ok
        ], 'public/css/elix_mindwo.css', 'resources/assets/plugins');

        // Stypes for view page
        mix.styles([
            'datetimepicker/jquery.datetimepicker.css', // ok
            'dropzone/dropzone.min.css', //ok
            'dropzone/basic.min.css', // ok
            'select2/select2-bootstrap.css', // ok
            'codemirror/css/codemirror.css', // ok
            'codemirror/css/ambiance.css', // ok
            'jasny-bootstrap/css/jasny-bootstrap.min.css', // ok
            'jquery-nestable/jquery.nestable.css', // ok
            'mindwo/css/view.css' // ok
        ], 'public/css/elix_view.css', 'resources/assets/plugins');
        
        // horizontal menu UI styles                
        mix.less([
            'horizontal_ui.less',
            'bootstrap_menu.less',
            'multilevel_menu.less'
        ], 'public/css/elix_mindwo_horizontal.css');
        
        // Metronic theme UI styles                
        mix.less([
            'metronic_ui.less',
        ], 'public/css/elix_metronic.css');

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
            'mindwo/pages/re_login.js'
        ],
        'public/js/elix_plugins.js', 'resources/assets/plugins');

        // Scripts for loged in users - will be included in main blade in case of authorizes user
        mix.scripts(['mindwo/pages/search_top.js', 'mindwo/pages/userlinks.js'], 'public/js/elix_userlinks.js', 'resources/assets/plugins');

        // Scripts for grids/forms functionality
        mix.scripts([
            'bootstrap-daterangepicker/daterangepicker.min.js',
            'bootstrap-colorpicker/js/bootstrap-colorpicker.js',
            'dropzone/dropzone.min.js',
            'jasny-bootstrap/js/jasny-bootstrap.js',
            'tree/jstree.min.js',
            'file_download.js',
            'codemirror/js/codemirror.js',
            'codemirror/js/mode/javascript/javascript.js',        
            'datetimepicker/jquery.datetimepicker.js',
            'select2/select2.min.js',
            //'select2/select2_locale_lv.js', ToDo: implement universal approach to set lang
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
            'mindwo/fields/datetime.js'
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
           'mindwo/pages/sticky.js',
           'mindwo/pages/empl_links_fix.js'
        ], 'public/js/elix_profile.js', 'resources/assets/plugins');
        /*
    */
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
            'css/elix_metronic.css'
        ]);
    });
});

gulp.task('default', ['langjs','mix_all']);