<?php

return [

    'label' => 'पृष्ठ मार्गदर्शन',

    'overview' => ':first से :last प्रविष्टियां :total में से',

    'fields' => [

        'records_per_page' => [

            'label' => 'प्रति पृष्ठ',

            'options' => [
                'all' => 'सभी',
            ],

        ],

    ],

    'actions' => [

        'first' => [
            'label' => 'पहला',
        ],

        'go_to_page' => [
            'label' => 'पृष्ठ :page पर जाएं',
        ],

        'last' => [
            'label' => 'अंतिम',
        ],

        'next' => [
            'label' => 'अगला',
        ],

        'previous' => [
            'label' => 'पिछला',
        ],

    ],

];
