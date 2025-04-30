<?php

$v1RegisterFields = [
    'fullname',
    'birthdate',
    'mphone',
    'email',
];

$v1FillSteps = [
    1 => [
        'fill_step' => ['required', 'in:1'],
        'passport_title' => ['required', 'string', 'max:255'],
        'passport_code' => ['required', 'string', 'max:255'],
        'passport_date' => ['required', 'date'],
    ],
    2 => [
        'fill_step' => ['required', 'in:2'],
//        'reg_region_name' => ['required', 'string', 'max:255'],
        'reg_city_name' => ['required', 'string', 'max:255'],
        'reg_street' => ['required', 'string', 'max:255'],
        'reg_house' => ['required', 'string', 'max:255'],
//        'reg_flat' => ['string', 'max:255'],

//        'fact_region_name' => ['required', 'string', 'max:255'],
        'fact_city_name' => ['required', 'string', 'max:255'],
    ],
];

$v2RegisterFields = [
    'mphone',
];

$v2FillSteps = [
    1 => [
        'fill_step' => ['required', 'in:1'],
        'first_name' => ['required', 'string', 'max:255'],
        'last_name' => ['required', 'string', 'max:255'],
        'middlename' => ['nullable', 'string', 'max:255'],
        'birthdate' => ['nullable', 'date', 'before:today', 'after:01.01.1900', 'regex:/[0-9]{2}\.[0-9]{2}\.[0-9]{4}/'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    ],
    2 => [
        'fill_step' => ['required', 'in:2'],
        'passport_title' => ['required', 'string', 'max:255'],
        'passport_code' => ['required', 'string', 'max:255'],
        'passport_date' => ['required', 'date'],
    ],
    3 => [
        'fill_step' => ['required', 'in:3'],
        'reg_city_name' => ['required', 'string', 'max:255'],
        'reg_street' => ['required', 'string', 'max:255'],
        'reg_house' => ['required', 'string', 'max:255'],
        'reg_flat' => ['required', 'string', 'max:255'],
    ],
];

return [
    'active' => env('ACTIVE_THEME', 'centrzaim'),
    'default' => 'centrzaim',
    'centrzaim' => [
        'views_path' => resource_path('views/front/centrzaim'),

        'register_fields' => $v2RegisterFields,
        'has_expanded_terms' => true,

        'fill_steps' => $v2FillSteps,
    ],
];