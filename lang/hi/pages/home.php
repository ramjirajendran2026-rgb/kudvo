<?php

return [
    'seo' => [
        'title' => 'ऑनलाइन वोटिंग सिस्टम',
        'description' => 'ऑनलाइन वोटिंग सिस्टम',
    ],

    'content' => [
        'hero' => [
            'items' => [
                [
                    'title' => 'गृहस्वामियों के संघ प्रबंधन को सुदृढ़ करें',
                    'description' => 'कॉनडोमिनियम समुदाय के निर्णयों का प्रबंधन करें',
                    'image' => asset('img/home/hero/hoa-home-owner-asspciation-or-condominium-associations.webp'),
                    'cta_label' => 'शुरू करें',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'और जानें',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'क्लबों के लिए सुरक्षित और प्रभावी ऑनलाइन वोटिंग सिस्टम',
                    'description' => 'हमारे विश्वसनीय सिस्टम के साथ अपने निर्णय-प्रक्रिया को सरल बनाएं।',
                    'image' => asset('img/home/hero/club.webp'),
                    'cta_label' => 'शुरू करें',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'और जानें',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'कॉर्पोरेट और उद्योग: निर्णय-प्रक्रिया को सरल बनाएं',
                    'description' => 'बोर्ड बैठकों, कॉर्पोरेट संकल्पों के लिए एक सुविधाजनक समाधान प्रदान करता है।',
                    'image' => asset('img/home/hero/corparate-industry.webp'),
                    'cta_label' => 'शुरू करें',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'और जानें',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'एसोसिएशनों और संघों: सदस्यता सहभागिता को सुदृढ़ करें',
                    'description' => 'अपने एसोसिएशन या संघ के सदस्यों के बीच मजबूत सहभागिता को बढ़ावा दें।',
                    'image' => asset('img/home/hero/associations-and-unions.webp'),
                    'cta_label' => 'शुरू करें',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'और जानें',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'विश्वास और शैक्षणिक संस्थान: छात्र और कर्मचारी सहभागिता को बढ़ावा दें',
                    'description' => 'संस्थागत निर्णयों में छात्र और कर्मचारी सहभागिता को प्रोत्साहित करें।',
                    'image' => asset('img/home/hero/school-or-university.webp'),
                    'cta_label' => 'शुरू करें',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'और जानें',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'कर्मचारी संघ: अपने कार्यबल को सशक्त बनाएं',
                    'description' => 'संगठनात्मक निर्णयों में अपने कार्यबल को आवाज दें।',
                    'image' => asset('img/home/hero/employer-associations.webp'),
                    'cta_label' => 'शुरू करें',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'और जानें',
                    'cta2_url' => route('products.election.home'),
                ],
            ],
        ],
        'features' => [
            'items' => [
                [
                    'title' => 'सुरक्षित और सुविधाजनक बैलट एक्सेस',
                    'image' => asset('img/home/features/ballot-link-delivery.webp'),
                    'points' => [
                        'त्वरित और प्रभावी बैलट एक्सेस के साथ वोटिंग प्रक्रिया को सरल बनाएं।',
                        'अधिकार प्राप्त मतदाताओं के लिए अद्वितीय लिंक या एक सामान्य एक्सेस प्वाइंट प्रदान करें।',
                        'मतदाताओं के लिए सुविधा और विश्वसनीयता सुनिश्चित करें।',
                    ],
                ],
                [
                    'title' => 'मल्टी-फैक्टर ऑथेंटिकेशन के साथ उन्नत सुरक्षा',
                    'image' => asset('img/home/features/multi-factor-authentication-code-delivery.webp'),
                    'points' => [
                        'वोटिंग प्रक्रिया की अखंडता को उन्नत सुरक्षा उपायों के साथ प्राथमिकता दें।',
                        'मतदाता सत्यापन के लिए मल्टी-फैक्टर ऑथेंटिकेशन (MFA) कोड लागू करें।',
                        'नकली या अनधिकृत वोटों से बचाव करें, चुनाव परिणामों की वैधता सुनिश्चित करें।',
                    ],
                ],
                [
                    'title' => 'पारदर्शी बैलट स्वीकृति',
                    'image' => asset('img/home/features/ballot-acknowledgement.webp'),
                    'points' => [
                        'पारदर्शिता सुनिश्चित करने के लिए मतदाताओं को उनके बैलट की पुष्टि प्रदान करें।',
                        'वोटों की प्रामाणिकता सत्यापित करें और प्रक्रिया में मतदाताओं की संतुष्टि बढ़ाएं।',
                        'मतदाताओं को अतिरिक्त समर्थन और संसाधन प्रदान करें, उनके समग्र अनुभव को बेहतर बनाएं।',
                    ],
                ],
                [
                    'title' => 'उन्नत सुरक्षा प्राथमिकताएँ',
                    'image' => asset('img/home/features/do-not-track-vote.webp'),
                    'points' => [
                        'डुप्लिकेट उपकरणों को ट्रैक और रोकने के लिए सुरक्षा प्राथमिकताओं को अनुकूलित करें।',
                        'वोटिंग धोखाधड़ी से बचाने के लिए उन्नत ट्रैकिंग और रोकथाम सुविधाएँ लागू करें।',
                        'चुनावों की अखंडता सुनिश्चित करने के लिए मजबूत सुरक्षा उपाय रखें।',
                    ],
                ],
                [
                    'title' => 'व्यापक चुनाव प्रबंधन',
                    'image' => asset('img/home/features/elector-update-after-publish.webp'),
                    'points' => [
                        'चुनाव प्रकाशित होने के बाद भी मतदाता विवरण अपडेट रखें।',
                        'मतदाता जानकारी और चुनाव परिणामों की सटीकता सुनिश्चित करें।',
                        'सभी प्रतिभागियों के लिए एक सहज और विश्वसनीय वोटिंग अनुभव प्रदान करें।',
                    ],
                ],
                [
                    'title' => 'विभाजित बैलट से बढ़ी हुई प्रभावशीलता',
                    'image' => asset('img/home/features/segmented-voting-system.webp'),
                    'points' => [
                        'मतदाता विवरण के आधार पर बैलट को विभाजित करें ताकि वोटिंग अधिक प्रभावी हो सके।',
                        'लक्षित वोटिंग अनुभव प्रदान करें और व्यक्तिगत बैलट के साथ मतदाताओं को बेहतर संलग्न करें।',
                        'समग्र वोटिंग अनुभव को बढ़ाएं और मतदाता भागीदारी बढ़ाएं।',
                    ],
                ],
            ],
        ],
        'products' => [
            'title' => 'उत्पाद',
            'items' => [
                [
                    'title' => 'ऑनलाइन चुनाव',
                    'description' => 'कुडवो के ऑनलाइन चुनाव समाधान का उपयोग करके आसानी से विभिन्न चुनावों का आयोजन करें। बोर्ड चुनाव, समिति वोट या संगठनात्मक सर्वेक्षणों को सरल बनाएं।',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'उत्पाद और मूल्य निर्धारण देखें',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'संकल्प वोटिंग',
                    'description' => 'कुडवो के संकल्प वोटिंग फीचर के साथ महत्वपूर्ण मुद्दों पर सूचित निर्णय लें। स्पष्टता और पारदर्शिता सुनिश्चित करने के लिए प्रभावी ढंग से प्रस्ताव रखें, बहस करें और वोट करें।',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'उत्पाद और मूल्य निर्धारण देखें',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'सर्वेक्षण',
                    'description' => 'कुडवो के सर्वेक्षण उपकरण के साथ हितधारकों से मूल्यवान जानकारी और प्रतिक्रिया प्राप्त करें। संगठनात्मक प्रदर्शन, सदस्य संतुष्टि और अधिक पर सर्वेक्षण करें ताकि सूचित निर्णय लिए जा सकें।',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'उत्पाद और मूल्य निर्धारण देखें',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'एजीएम बैठक वोटिंग',
                    'description' => 'कुडवो के एजीएम बैठक वोटिंग समाधान के साथ वार्षिक सामान्य बैठकों (एजीएम) को सरल बनाएं। प्रक्रिया की अखंडता बनाए रखते हुए उपस्थित लोगों के लिए दूरस्थ भागीदारी और वोटिंग सक्षम करें।',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'उत्पाद और मूल्य निर्धारण देखें',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'लाइव पोलिंग',
                    'description' => 'कुडवो के लाइव पोलिंग फीचर के साथ अपने दर्शकों को वास्तविक समय में

 संलग्न करें। इवेंट, वेबिनार या बैठकों के दौरान इंटरैक्टिव पोल संचालित करें ताकि प्रतिक्रिया प्राप्त हो और दर्शकों के साथ संपर्क बढ़े।',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'उत्पाद और मूल्य निर्धारण देखें',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'बैठक वोटिंग',
                    'description' => 'कुडवो की बैठक वोटिंग कार्यक्षमता के साथ बैठकों को अधिक उत्पादक और समावेशी बनाएं। उपस्थित लोगों को एजेंडा आइटम, प्रस्ताव या निर्णयों पर वोट करने की अनुमति दें, सुनिश्चित करें कि हर आवाज सुनी जाए।',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'उत्पाद और मूल्य निर्धारण देखें',
                    'cta_url' => route('home'),
                ],
            ],
        ],
        'clientele' => [
            'title' => 'हमारे खुश ग्राहक',
            'items' => [
                [
                    'logo' => asset('img/home/clients/axeman-michigan.webp'),
                    'name' => 'Axeman Michigan',
                ],
                [
                    'logo' => asset('img/home/clients/csir.webp'),
                    'name' => 'CSIR',
                ],
                [
                    'logo' => asset('img/home/clients/esic.webp'),
                    'name' => 'ESIC',
                ],
                [
                    'logo' => asset('img/home/clients/fron-junior-lebanon.webp'),
                    'name' => 'Fron Junior Lebanon',
                ],
                [
                    'logo' => asset('img/home/clients/hong-yuan-international-group.webp'),
                    'name' => 'Hong Yuan International Group',
                ],
                [
                    'logo' => asset('img/home/clients/international-youth-federation.webp'),
                    'name' => 'International Youth Federation',
                ],
                [
                    'logo' => asset('img/home/clients/jtf-union.webp'),
                    'name' => 'JTF Union',
                ],
                [
                    'logo' => asset('img/home/clients/rr-international-school.webp'),
                    'name' => 'RR International School',
                ],
                [
                    'logo' => asset('img/home/clients/simplernow-community.webp'),
                    'name' => 'Simplernow Community',
                ],
                [
                    'logo' => asset('img/home/clients/spo-society.webp'),
                    'name' => 'SPO Society',
                ],
                [
                    'logo' => asset('img/home/clients/telkom-indonesia.webp'),
                    'name' => 'Telkom Indonesia',
                ],
                [
                    'logo' => asset('img/home/clients/uae-bangladesh-investment-group-ltd.webp'),
                    'name' => 'UAE/Bangladesh Investment Group Ltd',
                ],
            ],
        ],
        'contact' => [
            'title' => 'संपर्क करें',
        ],
    ],
];
