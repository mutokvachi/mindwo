<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_users_salaries
|--------------------------------------------------------------------------
*/
return [
    
    'probation_salary' => 'Probation salary',
    'probation_months' => 'Probation months',    
    'probation_salary_hint' => "If provided then on form saving will be created 2 salaries records - one for probation salary and one for regular salary. Regular salary date 'Valid from' will be calculated automaticaly depending on probation months.",
    'probation_months_hint' => "Required if probation salary is entered. Will be used to calculate regular salary date 'Valid from'.",
];