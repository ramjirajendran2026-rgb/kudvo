<?php

return [
    'add_on' => 'ऐड-ऑन',
    'all' => 'सभी',
    'enums' => [
        'election_status' => [
            'cancelled' => [
                'label' => 'रद्द',
            ],
            'closed' => [
                'label' => 'बंद',
            ],
            'draft' => [
                'label' => 'ड्राफ्ट',
            ],
            'published' => [
                'label' => 'प्रकाशित',
            ],
            'open' => [
                'label' => 'मतदान प्रगति पर है',
            ],
            'completed' => [
                'label' => 'पूर्ण',
            ],
        ],
        'election_setup_step' => [
            'preference' => [
                'label' => 'पसंद',
            ],
            'electors' => [
                'label' => 'चुनावकर्ता जोड़ें',
            ],
            'ballot' => [
                'label' => 'बैलेट सेटअप',
            ],
            'timing' => [
                'label' => 'समय सेट करें',
            ],
            'payment' => [
                'label' => 'भुगतान',
            ],
            'publish' => [
                'label' => 'प्रकाशित करें',
            ],
        ],
        'candidate_sort' => [
            'manual' => [
                'label' => 'मैन्युअल',
            ],
            'random' => [
                'label' => 'रैंडम',
            ],
            'ascending' => [
                'label' => 'आरोही',
            ],
            'descending' => [
                'label' => 'अवरोही',
            ],
        ],
        'election_panel_dashboard_state' => [
            'yet_to_start' => [
                'label' => 'अब तक शुरू नहीं हुआ',
            ],
            'voted_now' => [
                'label' => 'सफलतापूर्वक वोट दिया गया',
                'description' => 'आपका वोट सफलतापूर्वक सबमिट किया गया है।',
            ],
            'already_voted' => [
                'label' => 'पहले ही वोट किया गया',
                'description' => 'आपने पहले ही इस चुनाव के लिए अपना वोट दे दिया है।',
            ],
            'closed' => [
                'label' => 'वोटिंग बंद',
                'description' => 'इस चुनाव के लिए वोटिंग बंद हो गई है',
            ],
            'completed' => [
                'label' => 'वोटिंग बंद',
                'description' => 'इस चुनाव के लिए वोटिंग बंद हो गई है',
            ],
            'expired' => [
                'label' => 'वोटिंग समाप्त हो गई',
                'description' => 'इस चुनाव के लिए वोटिंग समाप्त हो गई है',
            ],
        ],
        'election_dashboard_state' => [
            'pending_preference' => [
                'label' => 'पसंद विन्यास करें',
            ],
            'pending_electors_list' => [
                'label' => 'चुनावकर्ता जोड़ें',
            ],
            'pending_ballot' => [
                'label' => 'स्थान और उम्मीदवार जोड़ें',
            ],
            'pending_timing' => [
                'label' => 'समय सेट करें',
                'description' => 'चुनाव की शुरुआत और समाप्ति तिथि और समय सेट करें',
            ],
            'pending_checkout' => [
                'label' => 'भुगतान',
                'description' => 'इस चुनाव को प्रकाशित करने के लिए भुगतान पूरा करें',
            ],
            'draft' => [
                'label' => 'प्रकाशित करने के लिए तैयार',
            ],
            'upcoming' => [
                'label' => 'अब तक शुरू नहीं हुआ',
            ],
            'open' => [
                'label' => 'वोटिंग के लिए खुला',
            ],
            'expired' => [
                'label' => 'वोटिंग का समय समाप्त हो गया',
            ],
            'closed' => [
                'label' => 'वोटिंग बंद',
                'description' => 'इस चुनाव के लिए वोटिंग बंद हो गई है:datetime',
            ],
            'completed' => [
                'label' => 'पूर्ण',
            ],
            'cancelled' => [
                'label' => 'रद्द किया गया',
                'description' => 'यह चुनाव :datetime पर रद्द किया गया है',
            ],
        ],
        'election_collaborator_permission' => [
            'full_access' => [
                'label' => 'पूर्ण पहुंच',
            ],
            'read_only' => [
                'label' => 'केवल पढ़ने की अनुमति',
            ],
            'no_access' => [
                'label' => 'कोई पहुँच नहीं',
            ],
        ],
    ],
    'nav' => [
        'home' => [
            'label' => 'होम',
        ],
        'clientele' => [
            'label' => 'ग्राहक',
        ],
        'products' => [
            'label' => 'उत्पाद',
            'items' => [
                'election' => [
                    'label' => 'ऑनलाइन चुनाव',
                ],
                'resolution_voting' => [
                    'label' => 'संकल्प वोटिंग',
                ],
                'phygital' => [
                    'label' => 'Phygital मतदान',
                ],
            ],
        ],
        'wiki' => [
            'label' => 'ब्लॉग',
        ],
        'contact' => [
            'label' => 'संपर्क',
        ],
        'privacy_policy' => [
            'label' => 'गोपनीयता नीति',
        ],
        'terms_of_service' => [
            'label' => 'सेवा की शर्तें',
        ],
        'help' => [
            'label' => 'सहायता',
            'items' => [
                'faq' => [
                    'label' => 'सामान्य प्रश्न',
                ],
                'contact' => 'संपर्क',
            ],
            'how_it_works' => 'कैसे काम करता है',
        ],
        'vote_now' => [
            'label' => 'अभी वोट करें',
        ],
        'sign_in' => [
            'label' => 'साइन इन करें',
        ],
        'dashboard' => [
            'label' => 'डैशबोर्ड',
        ],
        'sign_up' => [
            'label' => 'साइन अप करें',
        ],
    ],
    'contact' => [
        'phone' => [
            'label' => 'कॉल / व्हाट्सएप',
            'number' => '+1-631-731-3526',
        ],
        'email' => [
            'address' => 'support@kudvo.com',
        ],
    ],
];
