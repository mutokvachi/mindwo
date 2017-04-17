<?php

/**
 *
 * Maršruti
 *
 * Šeit tiek definēti portāla maršruti (routes)
 */
// Datu bāzes SQL pieprasīju auditācija, ja ir ieslēgts konfigurācijas parametrs
if (Config::get('database.log', false)) {

    DB::listen(function ($query) {
        // $query->sql
        // $query->bindings
        // $query->time
        $data = compact('bindings', 'time', 'name');

        // Format binding data for sql insertion
        foreach ($query->bindings as $i => $binding) {
            if ($binding instanceof \DateTime) {
                $query->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
            } else if (is_string($binding)) {
                $query->bindings[$i] = "'$binding'";
            }
        }

        // Insert bindings into query
        $sql = str_replace(array('%', '?'), array('%%', '%s'), $query->sql);
        $sql = vsprintf($sql, $query->bindings);

        Log::info("URL: " . Request::url() . " | SQL: " . $sql . " | TIME: " . $query->time);
    });
}

Route::get('/download_by_guid_{guid}', array('as' => 'download_file_guid', 'middleware' => 'public_file', 'uses' => 'FileController@getFileByGuid'));
Route::post('/public/save_file_by_guid', array('as' => 'save_file_by_guid', 'middleware' => 'public_file', 'uses' => 'FormController@saveFile'));
//Route::get('/public/save_file_by_guid', array('as' => 'save_file_by_guid_get', 'middleware' => 'public_file', 'uses'=>'FormController@saveFileGet'));

/**
 * Failu pārlūks - satura redaktora komponente
 */
Route::group(array('middleware' => 'auth'), function() {
    Route::controller('filemanager', 'FilemanagerLaravelController');
});

// Speciālie PHP skripti, kas pievienoti SVS
Route::post('/custom_php/{url}', array('as' => 'custom_php', 'middleware' => 'auth_ajax', 'uses' => 'CustomPHPController@executePHP'));
Route::get('/custom_php/{url}', array('as' => 'custom_php', 'middleware' => 'auth', 'uses' => 'CustomPHPController@executePHP'));

// Sistēmas struktūras objektu ģenerēšana un konfigurēšana
Route::post('/structure/method/{method_name}', array('as' => 'structure_method', 'middleware' => 'auth_ajax', 'uses' => 'StructureController@doMethod'));
Route::post('/structure/form/{method_name}', array('as' => 'structure_form', 'middleware' => 'auth_ajax', 'uses' => 'StructureController@getForm'));
Route::get('/structure/doc_manual', array('as' => 'structure_manual', 'middleware' => 'auth', 'uses' => 'StructureController@generateManual'));
Route::get('/structure/changes_sql', array('as' => 'structure_sql', 'middleware' => 'auth', 'uses' => 'StructureController@generateChangesSQL'));
Route::get('/structure/doc_ppa', array('as' => 'structure_ppa', 'middleware' => 'auth', 'uses' => 'StructureController@generatePPA'));
Route::get('/structure/doc_ppa_html', array('as' => 'structure_ppa_html', 'middleware' => 'auth', 'uses' => 'StructureController@generatePPAHtml'));

// Meklēšana (darbinieku, dokumentu, rakstu)
Route::get('/search', array('as' => 'search', 'middleware' => 'auth', 'uses' => 'SearchController@search'));
Route::post('/search', array('as' => 'search', 'middleware' => 'auth', 'uses' => 'SearchController@search'));
Route::post('/ajax/departments', array('as' => 'get_departments', 'middleware' => 'auth_ajax', 'uses' => 'DepartmentsController@getDepartments'));
Route::post('/ajax/employees', array('as' => 'get_employees', 'middleware' => 'auth_ajax', 'uses' => 'EmployeeController@searchAjaxEmployee'));

Route::get('/emp_docs_test', array('as' => 'search', 'middleware' => 'auth', 'uses' => 'Employee\EmployeePersonalDocController@testView'));

// Grids
Route::post('/grid', array('as' => 'grid', 'middleware' => 'auth_ajax', 'uses' => 'GridController@getGrid'));
Route::post('/delete_grid_items', array('as' => 'grid', 'middleware' => 'auth_ajax', 'uses' => 'GridController@deleteItems'));
Route::get('/skats_{id}', array('as' => 'view', 'middleware' => 'auth', 'uses' => 'GridController@showViewPage'));
Route::post('/excel', array('as' => 'excel', 'middleware' => 'auth_ajax', 'uses' => 'GridController@downloadExcel'));
Route::post('/import_excel', array('as' => 'import_data', 'middleware' => 'auth_ajax', 'uses' => 'ImportController@importData'));

// SVS formas
Route::get('/form/unlock_item/{list_id}/{item_id}', array('as' => 'form_unlock',  'middleware' => 'auth_ajax', 'uses'=>'FormController@unlockItem'));
Route::get('/form/lock_item/{list_id}/{item_id}', array('as' => 'form_lock',  'middleware' => 'auth_ajax', 'uses'=>'FormController@lockItem'));

Route::post('/form', array('as' => 'form',  'middleware' => 'auth_ajax', 'uses'=>'FormController@getForm'));
Route::post('/refresh_form', array('as' => 'refresh_form',  'middleware' => 'auth_ajax', 'uses'=>'FormController@refreshFormFields'));
Route::post('/fill_autocompleate', array('as' => 'fill_autocompleate',  'middleware' => 'auth_ajax', 'uses'=>'FormController@getAutocompleateData'));
Route::post('/load_binded_field', array('as' => 'load_binded_field',  'middleware' => 'auth_ajax', 'uses'=>'FormController@getBindedFieldData'));
Route::post('/save_form', array('as' => 'save_form',  'middleware' => 'auth_ajax', 'uses'=>'FormController@saveForm'));
Route::post('/delete_item', array('as' => 'delete_item',  'middleware' => 'auth_ajax', 'uses'=>'FormController@deleteItem'));
Route::post('/generate_word', array('as' => 'generate_word',  'middleware' => 'auth_ajax', 'uses'=>'WordController@generateWord'));
route::post('/register_document', array('as' => 'register_item',  'middleware' => 'auth_ajax', 'uses'=>'RegisterController@registerDocument'));
Route::post('/get_tasks_history', array('as' => 'get_tasks_history',  'middleware' => 'auth_ajax', 'uses'=>'TasksController@getTasksHistory'));
Route::post('/cancel_workflow', array('as' => 'cancel_workflow',  'middleware' => 'auth_ajax', 'uses'=>'TasksController@cancelWorkflow'));
Route::get('/get_form_pdf_{item_id}_{list_id}.pdf', array('as' => 'form_get_pdf',  'middleware' => 'auth_ajax', 'uses'=>'FormPDFController@getPDF'));
Route::post('/get_item_history', array('as' => 'get_item_history',  'middleware' => 'auth_ajax', 'uses'=>'FormController@getItemHistory'));

Route::group(['middleware' => 'auth_ajax', 'prefix' => 'view'], function() {
    Route::post('open', 'GridController@getViewEditForm');
    Route::post('save', 'GridController@saveView');
    Route::post('delete', 'GridController@deleteView');
    Route::post('auto_data', 'GridController@getAutocompleateData');
});

Route::group(['middleware' => 'auth_api', 'prefix' => 'api'], function() {    
    Route::group(['prefix' => 'view', 'namespace' => 'Api'], function () {
        Route::get('{view_id}/data/all', 'ViewController@getAllData');
        Route::get('{view_id}/data/filtered/{field}/{criteria}', 'ViewController@getFilteredData');
    });
});

// Startē procesu forsēti
Route::get('/force_process/{id}', array('as' => 'force_process', 'middleware' => 'auth', 'uses' => 'ProcessController@forceProcess'));

// Imitē REST servera atbildi
Route::get('/rest_test/{readviewentries}/{outputformat}/{Start}/{Count}', array('as' => 'rest_test', 'middleware' => 'auth', 'uses' => 'ProcessController@testRESTResponse'));

// Datnes
Route::get('/download_file_{item_id}_{list_id}_{field_id}.pdf', array('as' => 'download_file', 'middleware' => 'auth_ajax', 'uses' => 'FileController@getPDFFile'));
Route::get('/download_file_{item_id}_{list_id}_{file_field_id}', array('as' => 'download_file', 'middleware' => 'auth_ajax', 'uses' => 'FileController@getFile'));
Route::get('/download_filejs_{item_id}_{list_id}_{file_field_id}', array('as' => 'download_file', 'middleware' => 'auth_ajax', 'uses' => 'FileController@getFile_js'));
Route::get('/download_by_field_{item_id}_{list_id}_{field_name}', array('as' => 'download_file_field', 'middleware' => 'auth_ajax', 'uses' => 'FileController@getFileByField'));
Route::get('/download_first_file_{item_id}_{list_id}', array('as' => 'download_first_file', 'middleware' => 'auth_ajax', 'uses' => 'FileController@getFirstFile'));

Route::post('/calendar/events', array('middleware' => 'auth_ajax', 'uses' => 'CalendarController@getCalendarEvents'));

// Darbplūsmas
Route::post('/form_task', array('as' => 'task_form', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@getTaskForm'));
Route::post('/task_yes', array('as' => 'task_yes', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@doYes'));
Route::post('/task_no', array('as' => 'task_no', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@doNo'));
Route::post('/workflow_init', array('as' => 'workflow_init', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@initWorkflow'));
Route::post('/save_delegate', array('as' => 'save_delegate', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@saveDelegateTask'));
Route::post('/workflow_custom_approve', array('as' => 'workflow_custom_approve', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@getCustomApprove'));
Route::post('/workflow_find_approver', array('as' => 'workflow_find_approver', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@getAutocompleateApprovers'));
Route::post('/send_info_task', array('as' => 'send_info_task', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@sendInfoTask'));
Route::post('/tasks/get_delegated', array('as' => 'workflow_get_delegated_tasks', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@getDelegatedTasksList'));
Route::post('/tasks/cancel_delegated', array('as' => 'workflow_cancel_delegated_tasks', 'middleware' => 'auth_ajax', 'uses' => 'TasksController@cancelDelegatedTask'));

Route::group(['prefix' => 'workflow'], function() {
    Route::group(['prefix' => 'visual'], function () {
        Route::get('/test', array('middleware' => 'auth', 'uses' => 'VisualWFController@test'));

        Route::get('/steps/{id}', array('middleware' => 'auth_ajax', 'uses' => 'VisualWFController@getSteps'));
        Route::post('/form', array('middleware' => 'auth_ajax', 'uses' => 'VisualWFController@getWFForm'));
        Route::post('/save', array('middleware' => 'auth_ajax', 'uses'=>'VisualWFController@save'));
    });
});

Route::group(['prefix' => 'crypto', 'namespace' => 'Crypto'], function() {
        Route::get('/user_panel', array('middleware' => 'auth', 'uses' => 'CryptoCertificateController@getUserPanelView'));   
        Route::post('/save_cert', array('middleware' => 'auth', 'uses' => 'CryptoCertificateController@saveUserCertificate'));   
});

// Lietotāji - autorizācija, atslēgšanās
Route::post('/login', 'UserController@loginUser');
Route::get('/login', array('as' => 'login', 'uses' => 'UserController@showIndex'));
Route::get('/logout', array('as' => 'logout', 'uses' => 'UserController@logOut'));
Route::post('/ajax/change_password', array('as' => 'change_password', 'middleware' => 'auth_ajax', 'uses' => 'UserController@changePassw'));
Route::post('/ajax/form_password', array('as' => 'change_password', 'middleware' => 'auth_ajax', 'uses' => 'UserController@formPassw'));
Route::post('/relogin', 'UserController@reLoginUser');

// Route group for employee profile
Route::group(['middleware' => 'auth', 'prefix' => 'employee'], function() {
    Route::get('test', 'EmplProfileController@create');

    Route::group(['prefix' => 'personal_docs', 'namespace' => 'Employee'], function () {
        Route::get('/get/employee_docs/{user_id}', 'EmployeePersonalDocController@getEmployeeDocs');
        Route::get('/get/docs_by_country/{country_id}', 'EmployeePersonalDocController@getPersonalDocsByCountry');
        Route::post('/save', 'EmployeePersonalDocController@save');
    });

    Route::group(['prefix' => 'notes', 'namespace' => 'Employee'], function () {
        Route::get('/get/view/{user_id}', 'NoteController@getView');
        Route::post('/save', 'NoteController@save');
        Route::delete('/delete', 'NoteController@delete');
    });

    Route::group(['prefix' => 'timeoff', 'namespace' => 'Employee'], function () {
        Route::get('/get/view/{user_id}', 'TimeoffController@getView');
        Route::get('/get/filter/year/{user_id}', 'TimeoffController@getYearFilterView');
        Route::get('/get/calculate/{user_id}/{timeoff_id}', 'TimeoffController@calculateTimeoff');
        Route::get('/get/table/{user_id}/{timeoff_type_id}/{date_from}/{date_to}', 'TimeoffController@getTable');
        Route::get('/get/chart/{user_id}/{timeoff_type_id}/{date_from}/{date_to}', 'TimeoffController@getChartData');
        Route::get('/get/delete_calculated/{user_id}/{timeoff_id}', 'TimeoffController@deleteTimeoff');
    });

    Route::get('profile/{id?}', 'EmplProfileController@show')->name('profile');
    Route::get('profile/{id}/chunks', ['as' => 'profile_chunks', 'middleware' => 'auth_ajax', 'uses' => 'EmplProfileController@ajaxShowChunks']);
    Route::get('profile/{id}/tabs', ['as' => 'profile_tabs', 'middleware' => 'auth_ajax', 'uses' => 'EmplProfileController@ajaxShowTab']);
    Route::get('new', 'EmplProfileController@create');
});

Route::group(['middleware' => 'auth_ajax', 'prefix' => 'widget'], function() {
    Route::group(['prefix' => 'report'], function () {
        Route::get('/get/chart/{report_name}/{group_id}/{date_from}/{date_to}', 'ReportController@getChartData');
    });
    
    Route::group(['prefix' => 'eployeecount', 'namespace' => 'Widgets'], function () {
         Route::post('/get/view', 'EmployeeCountController@getView');
    });      
});

Route::group(['middleware' => 'auth_ajax', 'prefix' => 'freeform'], function() {
    Route::post('{id}/edit', 'FreeFormController@edit');
    Route::put('{id}', 'FreeFormController@update');
});

Route::group(['middleware' => 'auth_ajax', 'prefix' => 'inlineform'], function() {
    Route::post('', 'InlineFormController@store');
    Route::post('{id}/edit', 'InlineFormController@edit');
    Route::put('{id}', 'InlineFormController@update');
    Route::delete('{id}', 'InlineFormController@destroy');
});

Route::group(['middleware' => ['auth', 'orgchart_access'], 'prefix' => 'organization'], function() {
    Route::get('chart/{id?}', ['as' => 'organization_chart', 'uses' => 'OrgChartController@show']);
    Route::get('departments', ['as' => 'organization_departments', 'uses' => 'DepartmentsChartController@show']);
});

Route::group(['middleware' => ['auth', 'mail_access'], 'prefix' => 'mail'], function() {
	Route::get('', ['as' => 'mail_index', 'uses' => 'MailController@index']);
	Route::get('sent', ['as' => 'mail_sent', 'uses' => 'MailController@index']);
	Route::get('draft', ['as' => 'mail_draft', 'uses' => 'MailController@index']);
	Route::get('scheduled', ['as' => 'mail_scheduled', 'uses' => 'MailController@index']);
	Route::get('compose', ['as' => 'mail_compose', 'uses' => 'MailController@create']);
	Route::post('store', ['as' => 'mail_store', 'middleware' => 'auth_ajax', 'uses' => 'MailController@store']);
	Route::get('to_autocomplete', ['as' => 'mail_to_autocomplete', 'middleware' => 'auth_ajax', 'uses' => 'MailController@ajaxToAutocomplete']);
	Route::delete('mass_delete', ['as' => 'mail_mass_delete', 'middleware' => 'auth_ajax', 'uses' => 'MailController@massDelete']);
	Route::delete('attachment/{id}', ['as' => 'mail_delete_attachment', 'middleware' => 'auth_ajax', 'uses' => 'MailController@deleteAttachment']);
	Route::post('{id}/update', ['as' => 'mail_update', 'middleware' => 'auth_ajax', 'uses' => 'MailController@update']);
	Route::get('{id}/edit', ['as' => 'mail_edit', 'uses' => 'MailController@edit']);
	Route::get('{id}', ['as' => 'mail_show', 'uses' => 'MailController@show']);
	Route::delete('{id}', ['as' => 'mail_delete', 'middleware' => 'auth_ajax', 'uses' => 'MailController@destroy']);
});

// Lapas
/*
Route::get('/{id}/{item}', array('as' => 'page',  'middleware' => 'auth', 'uses'=>'PagesController@showPageItem'));
Route::get('/{id}', array('as' => 'page',  'middleware' => 'auth', 'uses'=>'PagesController@showPage'));
Route::post('/{id}', array('as' => 'page',  'middleware' => 'auth', 'uses'=>'PagesController@showPage'));

// Noklusētā lapa
Route::get('/', array('as' => 'home', 'middleware' => 'auth', 'uses'=>'PagesController@showRoot'));
*/
