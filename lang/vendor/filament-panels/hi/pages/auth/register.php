<?php

return [

    'title' => 'रजिस्टर',

    'heading' => 'साइन अप करें',

    'actions' => [

        'login' => [
            'before' => 'या',
            'label' => 'अपने अकाउंट में साइन इन करें',
        ],

    ],

    'form' => [

        'email' => [
            'label' => 'ई-मेल एड्रेस',
        ],

        'name' => [
            'label' => 'नाम',
        ],

        'password' => [
            'label' => 'पासवर्ड',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'पासवर्ड की पुष्टि कीजिये',
        ],

        'actions' => [

            'register' => [
                'label' => 'साइन अप करें',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'बहुत अधिक पंजीकरण प्रयास',
            'body' => 'कृपया :seconds सेकंड बाद पुनः प्रयास करें।',
        ],

    ],

];
