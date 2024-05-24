<?php

return [
    'seo' => [
        'title' => 'Online Voting System',
        'description' => 'Online Voting System',
    ],

    'content' => [
        'hero' => [
            'items' => [
                [
                    'title' => 'Enhance Homeowners Association Governance',
                    'description' => 'Manage condominium community decisions',
                    'image' => asset('img/home/hero/hoa-home-owner-asspciation-or-condominium-associations.webp'),
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Learn More',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Secure and Efficient Online Voting System for Clubs',
                    'description' => 'Streamline your decision-making process with our trusted system.',
                    'image' => asset('img/home/hero/club.webp'),
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Learn More',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Corporate and Industry: Streamline Decision-Making Processes',
                    'description' => 'offers a convenient solution for board meetings, corporate resolutions.',
                    'image' => asset('img/home/hero/corparate-industry.webp'),
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Learn More',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Associations & Unions: Strengthen Membership Engagement',
                    'description' => 'Foster stronger engagement among your association or union members.',
                    'image' => asset('img/home/hero/associations-and-unions.webp'),
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Learn More',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Trust & Educational Institutions: Promote Student and Staff Participation',
                    'description' => 'Encourage student and staff participation in institutional decisions.',
                    'image' => asset('img/home/hero/school-or-university.webp'),
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Learn More',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Employee Associations: Empower Your Workforce',
                    'description' => 'Empower your workforce with a voice in organizational decisions.',
                    'image' => asset('img/home/hero/employer-associations.webp'),
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Learn More',
                    'cta2_url' => route('products.election.home'),
                ],
            ],
        ],
        'features' => [
            'items' => [
                [
                    'title' => 'Secure and Convenient Ballot Access',
                    'image' => asset('img/home/features/ballot-link-delivery.webp'),
                    'points' => [
                        'Simplify the voting process with quick and efficient access to the ballot.',
                        'Offer unique links or a common access point for eligible voters.',
                        'Ensure convenience and reliability for voters.',
                    ],
                ],
                [
                    'title' => 'Enhanced Security with Multi-Factor Authentication',
                    'image' => asset('img/home/features/multi-factor-authentication-code-delivery.webp'),
                    'points' => [
                        'Prioritize the integrity of the voting process with enhanced security measures.',
                        'Implement multi-factor authentication (MFA) codes for voter verification.',
                        'Protect against fake or unauthorized votes, safeguarding the validity of election results.',
                    ],
                ],
                [
                    'title' => 'Transparent Ballot Acknowledgement',
                    'image' => asset('img/home/features/ballot-acknowledgement.webp'),
                    'points' => [
                        'Provide voters with confirmation of their ballots to ensure transparency.',
                        'Verify the authenticity of votes and enhance voter satisfaction with the process.',
                        'Offer additional support and resources to voters, enhancing their overall experience.',
                    ],
                ],
                [
                    'title' => 'Advanced Security Preferences',
                    'image' => asset('img/home/features/do-not-track-vote.webp'),
                    'points' => [
                        'Customize security preferences to track and prevent duplicate devices.',
                        'Implement advanced tracking and prevention features to protect against voting fraud.',
                        'Ensure the integrity of elections with robust security measures in place.',
                    ],
                ],
                [
                    'title' => 'Comprehensive Election Management',
                    'image' => asset('img/home/features/elector-update-after-publish.webp'),
                    'points' => [
                        'Keep elector details updated even after the election has been published.',
                        'Ensure the accuracy of voter information and election results.',
                        'Provide a seamless and reliable voting experience for all participants.',
                    ],
                ],
                [
                    'title' => 'Segmented Ballot for Enhanced Efficiency',
                    'image' => asset('img/home/features/segmented-voting-system.webp'),
                    'points' => [
                        'Divide the ballot based on elector details to make voting more efficient.',
                        'Offer targeted voting experiences and better engage voters with personalized ballots.',
                        'Enhance the overall voting experience and increase voter participation.',
                    ],
                ],
            ],
        ],
        'products' => [
            'title' => 'Products',
            'items' => [
                [
                    'title' => 'Online Election',
                    'description' => 'Host a variety of elections with ease using Kudvo\'s Online Election solution. Simplify board elections, committee votes, or organizational polls with adaptable voting methods.',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'See Products & Pricing',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'Resolution Voting',
                    'description' => 'Make informed decisions on crucial matters with Kudvo\'s Resolution Voting feature. Propose, debate, and vote on resolutions effectively to ensure clarity and transparency.',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'See Products & Pricing',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'Survey',
                    'description' => 'Gather valuable insights and feedback from stakeholders with Kudvo\'s Survey tool. Conduct surveys on organizational performance, member satisfaction, and more to drive informed decision-making.',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'See Products & Pricing',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'AGM Meeting Voting',
                    'description' => 'Streamline Annual General Meetings (AGMs) with Kudvo\'s AGM Meeting Voting solution. Enable remote participation and voting for attendees while maintaining the integrity of the process.',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'See Products & Pricing',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'Live Polling',
                    'description' => 'Engage your audience in real-time with Kudvo\'s Live Polling feature. Conduct interactive polls during events, webinars, or meetings to gather feedback and enhance audience interaction.',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'See Products & Pricing',
                    'cta_url' => route('home'),
                ],
                [
                    'title' => 'Meeting Voting',
                    'description' => 'Make meetings more productive and inclusive with Kudvo\'s Meeting Voting functionality. Enable attendees to vote on agenda items, proposals, or decisions, ensuring every voice is heard.',
                    'title_color' => '#49D8BE',
                    'cta_label' => 'See Products & Pricing',
                    'cta_url' => route('home'),
                ],
            ],
        ],
        'clientele' => [
            'title' => 'Our Happy Clients',
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
            'title' => 'Contact Us',
        ],
    ],
];
