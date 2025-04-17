<?php

return [
    'seo' => [
        'title' => 'Secure Online Voting with Kudvo - Empower Your Community',
        'description' => 'Empower your community with Kudvo\'s seamless and secure online voting platform. Boost participation, get faster results, and ensure enhanced security for your elections with our intuitive 3-step process.',
    ],
    'content' => [
        'hero' => [
            'title' => 'Secure and Transparent Voting with Kudvo\'s Phygital Solution',
            'description' => 'Kudvo’s Phygital is a cutting-edge voting system that seamlessly blends physical presence with digital technology, offering a Voter-Verifiable Paper Audit Trail (VVPAT) for enhanced security and transparency.',
            'image' => asset('img/products/phygital/phygital-voting-system-hero.webp'),
            'image_alt' => 'phygital voting',
            'cta' => [
                'label' => 'Watch Video',
                'url' => route('products.phygital.home') . '#how-we-do',
            ],
        ],
        'key_features' => [
            'label' => 'Key Features',
            'title' => 'Why Choose Kudvo\'s Phygital Voting System?',
            'description' => 'With Kudvo\'s Phygital Solution, you can:',
            'image' => asset('img/products/phygital/ballot-box-booth.webp'),
            'image_alt' => 'A yellow ballot booth and ballot box featuring the Kudvo logo',
            'items' => [
                [
                    'title' => 'Voter-Verifiable Paper Audit Trail (VVPAT):',
                    'description' => 'Voters can confirm their votes on a printed paper record, ensuring transparency and accountability.',
                ],
                [
                    'title' => 'Tamper-Evident Design',
                    'description' => 'Kudvo’s Phygital system utilizes secure hardware and software to prevent unauthorized access, ensuring the integrity of every vote.',
                ],
                [
                    'title' => 'Accessibility',
                    'description' => 'Designed with inclusivity in mind, Kudvo’s Phygital voting system is accessible to all voters, including those with disabilities.',
                ],
            ],
        ],
        'vvpat_printing' => [
            'label' => 'VVPAT Printing',
            'title' => 'Phygital System Features',
            'description' => 'Kudvo’s Phygital solution allows voters to verify their votes through a physical paper record, adding an extra layer of security and transparency.',
            'image' => asset('img/products/phygital/images-of-phygital-voting-system.webp'),
            'image_alt' => 'Man voting using kudvo phygital voting system using VVPAT system.',
            'items' => [
                [
                    'title' => 'Secure Printing',
                    'description' => 'The VVPAT printing process is tamper-evident and secure, safeguarding the integrity of all printed records.',
                ],
                [
                    'title' => 'Voter Verification',
                    'description' => 'Voters can visually inspect the printed record to ensure their votes are accurately recorded.',
                ],
                [
                    'title' => 'Audit Trail',
                    'description' => 'The VVPAT records create a physical audit trail, allowing for the verification of election results to maintain trust and integrity.',
                ],
            ],
        ],
        'how_we_do' => [
            'videos' => [
                [
                    'yt-video-id' => 'hGv5iyassD8',
                    'title' => 'How to Vote in Kudvo Using Phygital Voting',
                ],
            ],
        ],
        'footer' => [
            'description' => 'With Kudvo’s Phygital voting system, experience 200% security through the perfect synergy of 100% physical and 100% digital voting technologies.',
        ],
    ],
];
