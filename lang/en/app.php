<?php

return [
    'add_on' => 'Add-on',
    'all' => 'All',
    'enums' => [
        'election_status' => [
            'cancelled' => [
                'label' => 'Cancelled',
            ],
            'closed' => [
                'label' => 'Closed',
            ],
            'draft' => [
                'label' => 'Draft',
            ],
            'published' => [
                'label' => 'Published',
            ],
            'open' => [
                'label' => 'Voting In Progress',
            ],
            'completed' => [
                'label' => 'Completed',
            ],
        ],
        'election_setup_step' => [
            'preference' => [
                'label' => 'Preferences',
            ],
            'electors' => [
                'label' => 'Manage Electors',
            ],
            'ballot' => [
                'label' => 'Setup Ballot',
            ],
            'timing' => [
                'label' => 'Set Time',
            ],
            'payment' => [
                'label' => 'Payment',
            ],
            'publish' => [
                'label' => 'Publish',
            ],
        ],
        'candidate_sort' => [
            'manual' => [
                'label' => 'Manual',
            ],
            'random' => [
                'label' => 'Random',
            ],
            'ascending' => [
                'label' => 'Ascending',
            ],
            'descending' => [
                'label' => 'Descending',
            ],
        ],
        'election_panel_dashboard_state' => [
            'yet_to_start' => [
                'label' => 'Yet to start',
            ],
            'voted_now' => [
                'label' => 'Voted successfully',
                'description' => 'You vote has been submitted successfully.',
            ],
            'already_voted' => [
                'label' => 'Already voted',
                'description' => 'You have already casted your vote for this election.',
            ],
            'closed' => [
                'label' => 'Voting closed',
                'description' => 'Voting for this election is closed',
            ],
            'completed' => [
                'label' => 'Voting closed',
                'description' => 'Voting for this election is closed',
            ],
            'expired' => [
                'label' => 'Voting ended',
                'description' => 'Voting for this election is ended',
            ],
        ],
        'election_dashboard_state' => [
            'pending_preference' => [
                'label' => 'Configure Preference',
            ],
            'pending_electors_list' => [
                'label' => 'Add Electors',
            ],
            'pending_ballot' => [
                'label' => 'Add Positions and Candidates',
            ],
            'pending_timing' => [
                'label' => 'Set Timing',
                'description' => 'Set election start and end date and time',
            ],
            'pending_checkout' => [
                'label' => 'Payment',
                'description' => 'Complete payment to publish this election',
            ],
            'draft' => [
                'label' => 'Ready to Publish',
            ],
            'upcoming' => [
                'label' => 'Yet to Start',
            ],
            'open' => [
                'label' => 'Open for Voting',
            ],
            'expired' => [
                'label' => 'Voting time ended',
            ],
            'closed' => [
                'label' => 'Voting Closed',
                'description' => 'Voting is closed for this election on :datetime',
            ],
            'completed' => [
                'label' => 'Completed',
            ],
            'cancelled' => [
                'label' => 'Cancelled',
                'description' => 'This election is cancelled on :datetime',
            ],
        ],
        'election_collaborator_permission' => [
            'full_access' => [
                'label' => 'Full Access',
            ],
            'read_only' => [
                'label' => 'Read Only',
            ],
            'no_access' => [
                'label' => 'No Access',
            ],
        ],
    ],
    'nav' => [
        'home' => [
            'label' => 'Home',
        ],
        'clientele' => [
            'label' => 'Clientele',
        ],
        'products' => [
            'label' => 'Products',
            'items' => [
                'election' => [
                    'label' => 'Online Election',
                ],
                'resolution_voting' => [
                    'label' => 'Resolution Voting',
                ],
                'phygital' => [
                    'label' => 'Phygital Voting',
                ],
            ],
        ],
        'wiki' => [
            'label' => 'Wiki',
        ],
        'contact' => [
            'label' => 'Contact',
        ],
        'privacy_policy' => [
            'label' => 'Privacy Policy',
        ],
        'terms_of_service' => [
            'label' => 'Terms of Service',
        ],
        'help' => [
            'label' => 'Help',
            'items' => [
                'faq' => [
                    'label' => 'FAQ',
                ],
                'contact' => 'Contact',
            ],
            'how_it_works' => 'How it Works',
        ],
        'sign_in' => [
            'label' => 'Sign In',
        ],
        'dashboard' => [
            'label' => 'Dashboard',
        ],
        'sign_up' => [
            'label' => 'Sign Up',
        ],
    ],
    'footer' => [
        'quick_links' => [
            'label' => 'Quick Links',
        ],
        'description' => 'Get in touch with us to learn how we can run next election for your organization together.',
    ],
    'contact' => [
        'phone' => [
            'label' => 'Call / Whatsapp',
            'number' => '+1-631-731-3526',
        ],
        'email' => [
            'address' => 'support@kudvo.com',
        ],
    ],
];
