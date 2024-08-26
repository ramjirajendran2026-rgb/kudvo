<?php

return [
    'seo' => [
        'title' => 'अभी वोट करें',
        'description' => 'अब अपने पसंदीदा उम्मीदवार के लिए वोट करें',
    ],
    'content' => [
        'form' => [
            'heading' => 'अभी वोट करें',
            'description' => 'अपने पसंदीदा उम्मीदवार को वोट दें',
            'fields' => [
                'has_election_code' => [
                    'label' => 'क्या आपके पास चुनाव संहिता है?',
                ],
                'election_code' => [
                    'helper_text' => 'आप इसे अपने चुनाव अधिकारी से प्राप्त कर सकते हैं',
                    'label' => 'चुनाव संहिता',
                    'placeholder' => 'अपना चुनाव कोड दर्ज करें',
                ],
                'organisation_id' => [
                    'label' => 'संगठन',
                    'placeholder' => 'अपना संगठन चुनें',
                ],
                'election_id' => [
                    'label' => 'चुनाव',
                    'placeholder' => 'अपना चुनाव चुनें',
                ],
            ],
            'actions' => [
                'proceed' => [
                    'label' => 'वोट करने के लिए आगे बढ़ें',
                ],
            ],
        ],
    ],
];
