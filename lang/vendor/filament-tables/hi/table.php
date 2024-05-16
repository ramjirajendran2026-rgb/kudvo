<?php

return [

    'column_toggle' => [

        'heading' => 'स्तंभ',

    ],

    'columns' => [

        'text' => [

            'actions' => [

                'collapse_list' => ':count कम दिखाएं',

                'expand_list' => ':count अधिक दिखाएं',

            ],

            'more_list_items' => 'और :count अधिक',

        ],
    ],

    'fields' => [

        'search' => [

            'label' => 'खोजें',

            'placeholder' => 'खोजें',

        ],

    ],

    'actions' => [

        'filter' => [
            'label' => 'फ़िल्टर',
        ],

        'open_bulk_actions' => [
            'label' => 'क्रियाएँ खोलें',
        ],

    ],

    'empty' => [
        'heading' => 'कोई रिकॉर्ड उपलब्ध नहीं',
        'description' => 'शुरू करने के लिए :model बनाएं।',
    ],

    'filters' => [

        'actions' => [

            'reset' => [
                'label' => 'फ़िल्टर रीसेट करें',
            ],

        ],

        'multi_select' => [
            'placeholder' => 'सब',
        ],

        'select' => [
            'placeholder' => 'सब',
        ],

    ],

    'selection_indicator' => [

        'selected_count' => '1 रिकॉर्ड चयनित।|:count रिकॉर्ड चयनित।',

        'actions' => [

            'select_all' => [
                'label' => 'सभी :count चुने',
            ],

            'deselect_all' => [
                'label' => 'सभी अचयनित करे',
            ],

        ],

    ],

];
