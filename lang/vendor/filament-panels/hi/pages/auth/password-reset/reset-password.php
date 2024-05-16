<?php

return [

    'title' => 'अपना पासवर्ड रीसेट करें',

    'heading' => 'अपना पासवर्ड रीसेट करें',

    'form' => [

        'email' => [
            'label' => 'ई-मेल एड्रेस',
        ],

        'password' => [
            'label' => 'पासवर्ड',
            'validation_attribute' => 'password',
        ],

        'password_confirmation' => [
            'label' => 'पासवर्ड की पुष्टि कीजिये',
        ],

        'actions' => [

            'reset' => [
                'label' => 'पासवर्ड रीसेट',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'बहुत अधिक रीसेट प्रयास',
            'body' => 'कृपया :सेकंड सेकंड में पुनः प्रयास करें।',
        ],

    ],

];
