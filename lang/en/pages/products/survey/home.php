<?php

return [
    'seo' => [
        'title' => 'Best Free Online Survey Tool | Create, Share & Analyze Surveys',
        'description' => 'Easily create free online surveys with Kudvo’s powerful survey builder. Collect feedback, conduct market research, and analyze responses in real-time—all without coding!',
    ],
    'content' => [
        'hero' => [
            'title' => 'Create & Share Smart Surveys with Kudvo – 100% Free',
            'description' => 'Effortless Surveys, Powerful Insights Transform how you collect feedback with Kudvo’s intuitive survey builder. Create, distribute, and analyze surveys in minutes—no technical skills required.',
            'cta' => [
                'label' => 'Sign Up for Free',
                'url' => route('filament.user.auth.register'),
            ],
            'card' => [
                'title' => '98% Response Rate',
                'description' => 'Industry Leading',
            ],
        ],
        'features' => [
            'title' => 'Kudvo Online Survey Features',
            'description' => 'Advanced Features for Smarter Data Collection',
            'items' => [
                [
                    'icon' => 'heroicon-o-users',
                    'title' => 'Easy-to-Use Builder',
                    'description' => 'Create professional surveys in minutes with a user-friendly interface.',
                ],
                [
                    'icon' => 'heroicon-o-chart-bar',
                    'title' => 'Comprehensive Data Analysis',
                    'description' => 'Gain valuable insights with detailed reports and visual charts.',
                ],
                [
                    'icon' => 'heroicon-o-chart-pie',
                    'title' => 'Real-Time Responses',
                    'description' => 'Monitor responses as they come in and make faster decisions.',
                ],
                [
                    'icon' => 'heroicon-o-sparkles',
                    'title' => 'Fully Customizable',
                    'description' => 'Choose from 10+ field types like multiple-choice, ratings, and text inputs and more.',
                ],
                [
                    'icon' => 'heroicon-o-globe-alt',
                    'title' => 'Multi-Device Accessibility',
                    'description' => 'Ensure seamless access on desktops, tablets, and mobile devices.',
                ],
                [
                    'icon' => 'heroicon-o-shield-check',
                    'title' => 'GDPR & Data Security Compliance',
                    'description' => 'Keep data safe with enterprise-grade security.',
                ],
            ],
        ],
        'uses' => [
            'title' => 'Ideal for Every Industry & Use Case',
            'items' => [

                [
                    'title' => 'Market Research',
                    'description' => 'Understand customer needs and emerging trends.',
                    'image' => asset('img/products/survey/light-bulb-with-drawing-graph.webp'),
                ],
                [
                    'title' => 'Customer Feedback',
                    'description' => 'Improve products and services with real-time opinions.',
                    'image' => asset('img/products/survey/medium-shot-young-people-with-reviews.webp'),
                ],

                [
                    'title' => 'User Experience (UX) Research',
                    'description' => 'Enhance website and app usability through user feedback.',
                    'image' => asset('img/products/survey/ui-ux.webp'),
                ],
                [
                    'title' => 'Employee Engagement ',
                    'description' => 'Boost workplace satisfaction and performance',
                    'image' => asset('img/products/survey/employee.webp'),
                ],
                [
                    'title' => 'Event Surveys',
                    'description' => 'Measure event success and enhance future experiences',
                    'image' => asset('img/products/survey/event-survey.webp'),
                ],
                [
                    'title' => 'Healthcare Surveys',
                    'description' => 'Improve patient care with structured feedback.',
                    'image' => asset('img/products/survey/filling-medical-history.webp'),
                ],

            ],
        ],
        'how_its_work' => [
            'title' => 'Get Started in 3 Simple Steps',
            'items' => [
                [
                    'icon' => 'heroicon-o-plus-circle',
                    'title' => 'Create',
                    'description' => 'Design your survey with our user-friendly builder'],
                [
                    'icon' => 'heroicon-o-share',
                    'title' => 'Share',
                    'description' => 'Distribute via email, social media, or embedded links'],
                [
                    'icon' => 'heroicon-o-presentation-chart-line',
                    'title' => 'Analyze',
                    'description' => 'rack responses in real-time and gain meaningful insights.',
                ],
            ],
        ],
        'keyfeatures' => [
            [
                'icon' => 'heroicon-o-currency-dollar',
                'name' => '100% Free with No Hidden Costs',
                'description' => 'Enjoy unlimited surveys and responses without restrictions.',
            ],
            [
                'icon' => 'heroicon-o-chart-bar',
                'name' => 'Detailed Reports & Insights',
                'description' => 'Transform raw data into meaningful conclusions.'],
            [
                'icon' => 'heroicon-o-check-circle',
                'name' => 'Higher Response Rates',
                'description' => 'Our optimized design ensures more completed surveys.',
            ],
            [
                'icon' => 'heroicon-o-users',
                'name' => 'User-Friendly & Reliable',
                'description' => 'Designed for individuals and businesses of all sizes.',
            ],
        ],
        'cta_section' => [
            'title' => 'Start Collecting Smarter Feedback Today!',
            'description' => 'Create your first survey now—100% free, no credit card required!',
            'cta' => [
                'url' => route('filament.user.auth.register'),
                'label' => 'Sign Up for Free',
            ],
        ],
    ],
];
