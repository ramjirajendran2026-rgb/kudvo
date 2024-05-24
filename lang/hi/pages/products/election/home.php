<?php

return [
    'seo' => [
        'title' => 'ऑनलाइन चुनाव',
        'description' => 'ऑनलाइन चुनाव',
    ],
    'content' => [
        'hero' => [
            'title' => 'Kudvo के साथ सुरक्षित ऑनलाइन वोटिंग',
            'description' => 'अपने समुदाय को एक सरल और सुरक्षित ऑनलाइन वोटिंग अनुभव से सजीव करें।',
            'image' => asset('img/products/election/online-voting-system.webp'),
            'cta' => [
                'label' => 'शुरू करें',
                'url' => route('filament.user.auth.register'),
            ],
        ],
        'benefits' => [
            'title' => 'ऑनलाइन वोटिंग के लाभ',
            'description' => 'Kudvo का ऑनलाइन वोटिंग प्लेटफ़ॉर्म आपके समुदाय को सशक्तित करने के लिए विभिन्न लाभ प्रदान करता है।',
            'items' => [
                [
                    'title' => 'बढ़ी हुई भागीदारी',
                    'description' => 'ऑनलाइन वोटिंग से अधिक लोगों को भाग लेना आसान हो जाता है, जिससे वोटर उत्तरदाताओं का बढ़ता निर्णय लेते हैं।',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                                <circle cx="16" cy="4" r="1"></circle>
                                <path d="m18 19 1-7-6 1"></path>
                                <path d="m5 8 3-3 5.5 3-2.36 3.5"></path>
                                <path d="M4.24 14.5a5 5 0 0 0 6.88 6"></path>
                                <path d="M13.76 17.5a5 5 0 0 0-6.88-6"></path>
                            </svg>',
                ],
                [
                    'title' => 'तेज़ परिणाम',
                    'description' => 'Kudvo की स्वचालित वोट गणना और रिपोर्टिंग से आपको सटीक परिणाम तुरंत मिलते हैं।',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                                <path d="m12 14 4-4"></path>
                                <path d="M3.34 19a10 10 0 1 1 17.32 0"></path>
                            </svg>',
                ],
                [
                    'title' => 'बढ़ी हुई सुरक्षा',
                    'description' => 'हमारे उन्नत एन्क्रिप्शन और प्रमाणीकरण विधियां आपके चुनाव की अखंडता की रक्षा करती हैं।',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
                            </svg>',
                ],
            ],
        ],
        'how_it_works' => [
            'title' => 'काम कैसे करता है',
            'description' => 'कुडवो बस 3 सरल कदमों में सुरक्षित ऑनलाइन चुनाव बनाने और प्रबंधित करना आसान बनाता है।',
            'cta' => [
                'label' => 'और अधिक जानें',
                'url' => route('products.election.how-it-works'),
            ],
            'items' => [
                [
                    'title' => 'अपने चुनाव को सेट करें',
                    'description' => 'अपने चुनाव बनाएं, वोटिंग विकल्पों को परिभाषित करें, और ब्रांडिंग को कस्टमाइज़ करें।',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2">
                        </path>
                    </svg>',
                ],
                [
                    'title' => 'अपने वोटर्स को आमंत्रित करें',
                    'description' => 'हमारे सुरक्षित प्लेटफ़ॉर्म के माध्यम से आसानी से अपने वोटर्स को आमंत्रित करें और उनकी भागीदारी का प्रबंधन करें।',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>',
                ],
                [
                    'title' => 'सुरक्षित वोटिंग',
                    'description' => 'हमारी एन्क्रिप्टेड सिस्टम के माध्यम से आपके वोटर्स अपने मतदान सुरक्षित और गुमनाम रूप से डाल सकते हैं।',
                    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>',
                ],
            ],
        ],
        'additional_sections' => [
            [
                'title' => 'सुरक्षित और विश्वसनीय',
                'description' => 'कुडवो का ऑनलाइन वोटिंग प्लेटफ़ॉर्म सुरक्षा और डेटा गोपनीयता के सर्वोच्च मानकों के साथ तैयार किया गया है। हमारे उन्नत एन्क्रिप्शन एल्गोरिदम, मल्टी-फैक्टर प्रमाणीकरण, और सुरक्षित डेटा स्टोरेज से आपके चुनाव की अखंडता सुनिश्चित की जाती है।',
                'image' => asset('img/products/election/secure-and-reliable-online-voting-platform.webp'),
                'items' => [
                    [
                        'value' => 'एंड-टू-एंड एन्क्रिप्शन',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"></path>
                        </svg>',
                    ],
                    [
                        'value' => 'सुरक्षित डेटा स्टोरेज और बैकअप',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                            <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>',
                    ],
                    [
                        'value' => 'मल्टी-फैक्टर प्रमाणीकरण',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                            <path d="M2 12C2 6.5 6.5 2 12 2a10 10 0 0 1 8 4"></path>
                            <path d="M5 19.5C5.5 18 6 15 6 12c0-.7.12-1.37.34-2"></path>
                            <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"></path>
                            <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"></path>
                            <path d="M8.65 22c.21-.66.45-1.32.57-2"></path>
                            <path d="M14 13.12c0 2.38 0 6.38-1 8.88"></path>
                            <path d="M2 16h.01"></path>
                            <path d="M21.8 16c.2-2 .131-5.354 0-6"></path>
                            <path d="M9 6.8a6 6 0 0 1 9 5.2c0 .47 0 1.17-.02 2"></path>
                        </svg>',
                    ],
                ],
            ],
            [
                'title' => 'KUDVO ऑनलाइन वोटिंग प्रबंधक',
                'description' => 'कुडवो का सहज ऑनलाइन वोटिंग प्रबंधक ऑनलाइन चुनाव सेट करना और प्रबंधन करना आसान बनाता है। एक उपयोगकर्ता-मित्रित इंटरफेस और कदम-से-कदम मार्गदर्शन के साथ, आप बिना किसी विलम्ब के अपनी वोटिंग प्रक्रिया को बना, कस्टमाइज़ कर, और लॉन्च कर सकते हैं।',
                'image' => asset('img/products/election/online-voting-system.webp'),
                'items' => [
                    [
                        'value' => 'आसानी से अपने चुनाव बनाएं और कस्टमाइज़ करें',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                        <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2">
                        </path>
                    </svg>',
                    ],
                    [
                        'value' => 'मतदाता आमंत्रण और भागीदारी का प्रबंधन करें',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>',
                    ],
                    [
                        'value' => 'अपने चुनाव परिणाम का मॉनिटर और रिपोर्ट करें',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>',
                    ],
                ],
            ],
            [
                'title' => 'विशेषज्ञ मार्गदर्शन और समर्थन',
                'description' => 'कुडवो की चुनाव के विशेषज्ञों की टीम पूरे ऑनलाइन वोटिंग प्रक्रिया के दौरान आपका समर्थन करने के लिए यहां है। प्रारंभिक परामर्श से पोस्ट-चुनाव विश्लेषण तक, हम व्यक्तिगत मार्गदर्शन प्रदान करेंगे ताकि आपके चुनाव में सफलता हो।',
                'image' => asset('img/products/election/expert-guidance-and-support.webp'),
                'items' => [
                    [
                        'value' => 'विशेषज्ञ समर्थन टीम',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                        <path d="M3 14h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-7a9 9 0 0 1 18 0v7a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3">
                        </path>
                    </svg>',
                    ],
                    [
                        'value' => 'व्यापक मार्गदर्शन और सर्वोत्तम प्रथाएँ',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>',
                    ],
                    [
                        'value' => 'पोस्ट-चुनाव विश्लेषण और रिपोर्टिंग',
                        'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6 text-primary">
                        <path d="M21.21 15.89A10 10 0 1 1 8 2.83"></path>
                        <path d="M22 12A10 10 0 0 0 12 2v10z"></path>
                    </svg>',
                    ],
                ],
            ],
        ],
        'pricing' => [
            'title' => 'मूल्य निर्धारण',
            'description' => 'अपनी आवश्यकताओं और बजट के अनुसार सबसे उपयुक्त योजना चुनें',
        ],
        'cta_section' => [
            'title' => 'क्या आप अपने चुनाव को अगले स्तर पर ले जाने के लिए तैयार हैं?',
            'description' => 'कुडवो को आजमाएं और देखें कि यह आपके चुनाव के लिए काम करता है।',
            'cta_label' => 'साइन अप करें',
            'cta_url' => route('filament.user.auth.register'),
        ],
    ],
];
