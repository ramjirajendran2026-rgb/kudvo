<?php

return [
    'seo' => [
        'title' => 'Election Ready in 3 Simple Steps',
        'description' => 'Discover how Kudvo.com makes online voting simple and secure with just 3 easy steps. Set up your election, invite voters, and ensure secure, anonymous voting with our encrypted platform.',
    ],

    'content' => [
        'hero' => [
            'title' => '"Election Ready in 3 Simple Steps"',
            'description' => 'Experience the ease and efficiency of online voting with our intuitive platform.<br />From registration to result, manage your election seamlessly with these key steps.',
            'bg_image' => asset('img/products/election/how-its-work-bg.webp'),
            'cta' => [
                'label' => 'Watch Video',
                'url' => route('products.election.how-it-works') . '#online-voting-video',
            ],
        ],
        'steps' => [
            [
                'title' => 'Set Up Your Election',
                'description' => 'Create your election, define the voting options, and customize the branding.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                                <rect width="8" height="4" x="8" y="2" rx="1" ry="1"></rect>
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2">
                                </path>
                            </svg>',
            ],
            [
                'title' => 'Invite Your Voters',
                'description' => 'Easily invite your voters and manage their participation through our secure platform.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>',
            ],
            [
                'title' => 'Secure Voting',
                'description' => 'Your voters can cast their ballots securely and anonymously through our encrypted system.',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 mx-auto text-primary">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>',
            ],
        ],
        'videos' => [
            [
                'yt-video-id' => 'i1if-NjLJZs',
                'title' => 'How to Vote in Kudvo Using SMS',
            ],
            [
                'yt-video-id' => 'wQ2eSipPl7M',
                'title' => 'How to Vote in Kudvo Using Email',
            ],
        ],
    ],
];
