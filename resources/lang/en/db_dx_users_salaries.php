<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_users_salaries
|--------------------------------------------------------------------------
*/
return [
    
    'probation_salary' => 'Probation salary',
    'probation_months' => 'Probation months',    
    'probation_salary_annual' => 'Annual probation salary',

    'probation_salary_hint' => "If provided then on form saving will be created 2 salaries records - one for probation salary and one for regular salary. Regular salary date 'Valid from' will be calculated automaticaly depending on probation months.",
    'probation_months_hint' => "Required if probation salary is entered. Will be used to calculate regular salary date 'Valid from'.",

    'probation_salary_action' => "Generates probation salary row on first salary save",

    'errors' => [
        'probation_month_not_set' => 'Probation period months is not set!',
        'valid_from_not_set' => "Date 'Valid from' is not set!",
        'probation_salary_not_set' => 'Probation period salary is not set!',
    ],

    'form_js' => 'Calculates annual salary and show/hide probation fields regarding of item status',
    
];