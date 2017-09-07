<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_users_salaries
|--------------------------------------------------------------------------
*/
return [
    
    'probation_salary' => 'Pārbaudes perioda alga',
    'probation_months' => 'Pārbaudes mēneši',
    'probation_salary_annual' => 'Pārbaudes perioda gada alga',

    'probation_salary_hint' => "Ja norādīta pārbaudes perioda alga, tad veicot datu saglabāšanu tiks izveidoti divi algas ieraksti - viens ar pārbaudes perioda algu un otrs ar standarta algu, kas stājas spēkā pēc pārbaudes laika. Standarta algas datums 'Spēkā no' tiks automātiski aprēķināts ņemot vērā pārbaudes perioda mēneša skaitu.",
    'probation_months_hint' => "Jānorāda obligāti, ja ir ievadīta pārbaudes laika alga. Tiks izmantots, lai aprēķinātu standarta algas datumu 'Spēkā no'.",

    'probation_salary_action' => "Ģenerē pārbaudes laika algas ierakstu pie pirmās algas saglabāšanas",
    
    'errors' => [
        'probation_month_not_set' => 'Nav norādīts pārbaudes perioda mēnešu skaits!',
        'valid_from_not_set' => "Nav norādīts datums 'Spēkā no'!",
        'probation_salary_not_set' => 'Nav norādīta pārbuades perioda alga!',
    ],

    'form_js' => 'Aprēķina gada atalgojumu un parāda vai paslēpj pārbaudes perioda laukus atkarībā no ieraksta statusa',
];