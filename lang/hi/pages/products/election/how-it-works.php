<?php

return [
    'seo' => [
        'title' => 'कैसे काम करता है',
        'description' => 'कैसे काम करता है',
    ],

    'content' => [
        'hero' => [
            'title' => '"3 सरल चरणों में चुनाव तैयार"',
            'description' => 'हमारे सहज प्लेटफ़ॉर्म के साथ ऑनलाइन मतदान की सरलता और दक्षता का अनुभव करें।<br /> पंजीकरण से परिणाम तक, इन प्रमुख चरणों के साथ अपने चुनाव को संचालित करें।',
            'bg_image' => asset('img/products/election/how-its-work-bg.webp'),
            'cta' => [
                'label' => 'शुरू करें',
                'url' => route('filament.user.auth.register'),
            ],
        ],
        'steps' => [
            [
                'title' => 'अपने चुनाव को सेट करें',
                'description' => 'अपने चुनाव को बनाएं, मतदान विकल्पों को परिभाषित करें, और ब्रांडिंग को कस्टमाइज़ करें।',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                            <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                            <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2">
                            </path>
                        </svg>',
            ],
            [
                'title' => 'अपने मतदाताओं को आमंत्रित करें',
                'description' => 'हमारे सुरक्षित प्लेटफ़ॉर्म के माध्यम से आसानी से अपने मतदाताओं को आमंत्रित करें और उनकी भागीदारी का प्रबंधन करें।',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>',
            ],
            [
                'title' => 'सुरक्षित मतदान',
                'description' => 'आपके मतदाता हमारे एन्क्रिप्टेड सिस्टम के माध्यम से सुरक्षित और अनामता में अपने मतदान को कास्ट कर सकते हैं।',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>',
            ],
        ],
        'videos' => [
            [
                'title' => 'एसएमएस का उपयोग करके कुदवो के साथ वोट कैसे करें',
                'thumbnail' => asset('img/products/election/how-to-vote-using-sms.webp'),
                'url' => 'https://kudvo.s3.amazonaws.com/media/marketing/products/election/how-it-works-sms-voting.mp4',
                'aspect_ratio' => '9/16',
                'unsupported_label' => 'आपका ब्राउज़र HTML5 वीडियो का समर्थन नहीं करता है।',
            ],
            [
                'title' => 'ईमेल का उपयोग करके कुदवो के साथ वोट कैसे करें',
                'thumbnail' => asset('img/products/election/how-to-vote-using-sms.webp'),
                'url' => 'https://kudvo.s3.amazonaws.com/media/marketing/products/election/how-it-works-email-voting.mp4',
                'aspect_ratio' => '16/9',
                'unsupported_label' => 'आपका ब्राउज़र HTML5 वीडियो का समर्थन नहीं करता है।',
            ],
        ],
    ],
];
