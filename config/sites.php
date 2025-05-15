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
    'fullname',
];

$v2FillSteps = [];

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