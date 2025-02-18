<?php

return [

    'seo' => [
        'title' => 'Online Resolution Voting | Secure & Reliable Decision-Making | Kudvo',
        'description' => 'Empower your organization with Kudvo’s secure and efficient online resolution voting platform. Make fast, transparent, and reliable decisions anytime, anywhere.',
    ],
    'content' => [
        'hero' => [
            'title' => 'Empower Your Decisions with Online',
            'highlight' => 'Resolution Voting',
            'description' => 'Make secure and informed decisions with our online resolution voting platform. Ideal for clubs, associations, corporations, industries, and educational institutions, our system ensures fast, transparent, and user-friendly voting from anywhere.',
            'image' => asset('img/products/meeting/hero-resolution-voting.webp'),
            'image_alt' => 'resolution voting',
            'cta' => [
                'label' => 'Get Started',
                'url' => route('filament.user.auth.register'),
            ],
        ],
        'key_features' => [
            'label' => 'Secure | Reliable | Accessible',
            'title' => 'Why Choose Kudvo\'s Online Resolution Voting System?',
            'image' => asset('img/products/meeting/online-platform.webp'),
            'image_alt' => 'Reference image of online resolution voting system',
            'items' => [
                [
                    'icon' => 'heroicon-o-user-group',
                    'color' => 'green',
                    'title' => 'Simplify Group Decisions',
                    'description' => 'Conduct resolution voting with ease and efficiency. Our platform allows members to participate in decision-making remotely, securely, and with full transparency.',
                ],
                [
                    'icon' => 'heroicon-o-shield-check',
                    'color' => 'orange',
                    'title' => 'Top-Tier Security & Data Protection',
                    'description' => 'With advanced encryption and secure authentication, your votes are fully protected, ensuring the integrity of every resolution.',
                ],
                [
                    'icon' => 'heroicon-o-cog-8-tooth',
                    'color' => 'violet',
                    'title' => 'Customizable to Fit Your Needs',
                    'description' => 'Our system is adaptable to your organization’s unique voting requirements, offering customizable voting policies, formats, and access levels.',
                ],
                [
                    'icon' => 'heroicon-o-chart-bar',
                    'color' => 'red',
                    'title' => 'Instant, Real-Time Results',
                    'description' => 'Get immediate voting outcomes with comprehensive reporting and transparency, enabling quick and confident decision-making.',
                ],
            ],
        ],
        'numbers' => [
            'title' => 'Proven Track Record of Excellence',
            'description' => 'Discover why organizations trust us to handle their voting needs.',
            'items' => [
                [
                    'number' => '12+',
                    'title' => 'Years of',
                    'description' => 'delivering cutting-edge online resolution voting solutions',
                ],
                [
                    'number' => '1200+',
                    'title' => 'Successful',
                    'description' => 'Voting Sessions conducted worldwide',
                ],
                [
                    'number' => '100%',
                    'title' => 'Customer',
                    'description' => 'Satisfaction with our secure and reliable platform',
                ],
            ],
        ],
        'industries' => [
            'title' => 'Trusted by Leaders Across Industries',
            'description' => 'Our online Resolution voting platform is trusted by organizations in:',
            'image' => asset('img/products/meeting/services-industries.webp'),
            'image_alt' => 'kudvo service industries logo\'s',
            'items' => [
                [
                    'title' => 'Clubs: ',
                    'description' => 'Ensure fair and efficient voting for leadership, events, and decision-making.',
                ],
                [
                    'title' => 'Associations: ',
                    'description' => 'Conduct transparent and secure voting on key organizational resolutions.',
                ],
                [
                    'title' => 'Corporations: ',
                    'description' => ' Facilitate seamless board resolutions and shareholder voting across global locations.',
                ],
                [
                    'title' => 'Industries: ',
                    'description' => 'Digitize and enhance policy approvals and operational decision-making.',
                ],
                [
                    'title' => 'Educational Institutions: ',
                    'description' => 'Enable faculty, staff, and student voting with ease and security.',
                ],
            ],
        ],
        'hiw' => [
            'title' => 'How It Works',
            'image' => asset('img/products/meeting/how-online-voting-event-works.webp'),
            'image_alt' => 'how-online-voting-event-works',
            'items' => [
                [
                    'icon' => 'heroicon-o-wrench-screwdriver',
                    'title' => 'Create a Voting Session',
                    'description' => 'Set up resolutions, define parameters, and configure settings.',
                ],
                [
                    'icon' => 'heroicon-o-paper-airplane',
                    'title' => 'Invite Participants',
                    'description' => 'Send secure invitations via email or SMS',
                ],  [
                    'icon' => 'heroicon-o-lock-closed',
                    'title' => 'Vote with Confidence',
                    'description' => 'Participants can cast votes securely from any device.',
                ],  [
                    'icon' => 'heroicon-o-presentation-chart-line',
                    'title' => 'Review Results',
                    'description' => 'Access real-time outcomes and generate comprehensive reports.',
                ],
            ],
        ],
        'cta_section' => [
            'title' => 'Ready to Revolutionize Your Decision-Making Process?',
            'description' => 'Join hundreds of organizations leveraging Kudvo\'s secure resolution voting platform to enhance transparency, efficiency, and engagement in decision-making..',
            'image' => asset('img/products/meeting/web-browser-website.webp'),
            'image_alt' => 'how-online-voting-event-works',
            'cta' => [
                'label' => 'Start Your Resolution Voting Today!',
                'url' => route('filament.user.auth.register'),
            ],
        ],
    ],

];
