<?php

return [
    'seo' => [
        'title' => 'Kudvo Smart Surveys – Easy & Powerful Survey Builder',
        'description' => 'Build and share professional surveys effortlessly. Gain real-time insights, analyze feedback, and make data-driven decisions with Kudvo powerful survey platform.',
    ],
    'content' => [
        'hero' => [
            'title' => 'Create & Share Kudvo Smart Surveys with Ease',
            'description' => 'Transform the way you gather feedback with our intuitive Kudvo survey builder. Collect actionable insights in minutes.',
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
            'title' => 'Why Choose Our Survey Platform?',
            'description' => 'Kudvo Powerful features that make survey creation and analysis effortless',
            'items' => [
                [
                    'icon' => 'heroicon-o-users',
                    'title' => 'Easy-to-Use Builder',
                    'description' => 'Create surveys with a simple drag-and-drop interface.',
                ],
                [
                    'icon' => 'heroicon-o-chart-bar',
                    'title' => 'Modern Data Analysis',
                    'description' => 'Gain deep insights with interactive visual reports.',
                ],
                [
                    'icon' => 'heroicon-o-chart-pie',
                    'title' => 'Real-Time Responses',
                    'description' => 'Get instant feedback and make data-driven decisions.',
                ],
                [
                    'icon' => 'heroicon-o-sparkles',
                    'title' => 'Fully Customizable',
                    'description' => 'Brand your surveys with logos, themes, and colors.',
                ],
                [
                    'icon' => 'heroicon-o-globe-alt',
                    'title' => 'Multi-Device Support',
                    'description' => 'Accessible on desktops, tablets, and mobile devices.',
                ],
                [
                    'icon' => 'heroicon-o-shield-check',
                    'title' => 'Secure & Compliant',
                    'description' => 'Ensure data privacy and GDPR compliance.',
                ],
            ],
        ],
        'uses' => [
            'title' => 'Perfect for Every Use Case',
            'items' => [

                [
                    'title' => 'Market Research',
                    'description' => 'Gather insights on consumer behavior and product demand',
                    'image' => asset('img/products/survey/light-bulb-with-drawing-graph.webp'),
                ],
                [
                    'title' => 'Customer Feedback',
                    'description' => 'Collect opinions to improve products and services',
                    'image' => asset('img/products/survey/medium-shot-young-people-with-reviews.webp'),
                ],

                [
                    'title' => 'User Experience (UX) Research',
                    'description' => 'Enhance website and app usability through feedback',
                    'image' => asset('img/products/survey/ui-ux.webp'),
                ],
                [
                    'title' => 'Event Surveys',
                    'description' => 'Evaluate event success and gather attendee feedback',
                    'image' => asset('img/products/survey/event-survey.webp'),
                ],
                [
                    'title' => 'Healthcare Surveys',
                    'description' => 'Improve patient experience and healthcare services',
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
                    'description' => 'View real-time insights and make informed decisions',
                ],
            ],
        ],
        'cta_section' => [
            'title' => 'Ready to Transform Your Feedback Process?',
            'description' => 'Start your first survey today and revolutionize the way you collect feedback!',
            'cta' => [
                'url' => route('filament.user.auth.register'),
                'label' => 'Sign Up for Free',
            ],
        ],
    ],
];
