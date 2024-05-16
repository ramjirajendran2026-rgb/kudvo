<?php

return [
    'add_on' => 'अतिरिक्त', // Add-on
    'all' => 'सभी', // All
    'enums' => [
        'election_status' => [
            'cancelled' => [
                'label' => 'रद्द', // Cancelled
            ],
            'closed' => [
                'label' => 'बंद', // Closed
            ],
            'draft' => [
                'label' => 'मसौदा', // Draft
            ],
            'published' => [
                'label' => 'प्रकाशित', // Published
            ],
            'completed' => [
                'label' => 'पूर्ण', // Completed
            ],
        ],
        'election_setup_step' => [
            'preference' => [
                'label' => 'पसंद', // Preference
            ],
            'electors' => [
                'label' => 'चुनावकर्ता जोड़ें', // Add Electors
            ],
            'ballot' => [
                'label' => 'मतपत्र सेटअप', // Setup Ballot
            ],
            'timing' => [
                'label' => 'समय निर्धारित करें', // Set Timing
            ],
            'payment' => [
                'label' => 'भुगतान', // Payment
            ],
            'publish' => [
                'label' => 'प्रकाशित करें', // Publish
            ],
        ],
        'candidate_sort' => [
            'manual' => [
                'label' => 'मैनुअल', // Manual
            ],
            'random' => [
                'label' => 'रैंडम', // Random
            ],
            'ascending' => [
                'label' => 'आरोही', // Ascending
            ],
            'descending' => [
                'label' => 'अवरोही', // Descending
            ],
        ],
        'election_panel_dashboard_state' => [
            'yet_to_start' => [
                'label' => 'अभी शुरू नहीं हुआ', // Yet to start
            ],
            'voted_now' => [
                'label' => 'सफलतापूर्वक वोट किया गया', // Voted successfully
                'description' => 'आपका वोट सफलतापूर्वक सबमिट किया गया है।', // Your vote has been submitted successfully.
            ],
            'already_voted' => [
                'label' => 'पहले से ही वोट किया गया है', // Already voted
                'description' => 'आपने इस चुनाव के लिए पहले से ही अपना वोट डाल दिया है।', // You have already casted your vote for this election.
            ],
            'closed' => [
                'label' => 'वोटिंग बंद हो गई है', // Voting closed
                'description' => 'इस चुनाव के लिए वोटिंग बंद हो गई है।', // Voting for this election is closed
            ],
            'completed' => [
                'label' => 'वोटिंग बंद हो गई है', // Voting closed
                'description' => 'इस चुनाव के लिए वोटिंग बंद हो गई है।', // Voting for this election is closed
            ],
            'expired' => [
                'label' => 'वोटिंग समाप्त हो गई है', // Voting ended
                'description' => 'इस चुनाव के लिए वोटिंग समाप्त हो गई है।', // Voting for this election is ended
            ],
        ],
        'election_dashboard_state' => [
            'pending_preference' => [
                'label' => 'प्राथमिकता कॉन्फ़िगर करें', // Configure Preference
            ],
            'pending_electors_list' => [
                'label' => 'निर्वाचक जोड़ें', // Add Electors
            ],
            'pending_ballot' => [
                'label' => 'पद और उम्मीदवार जोड़ें', // Add Positions and Candidates
            ],
            'pending_timing' => [
                'label' => 'समय निर्धारित करें', // Set Timing
                'description' => 'चुनाव की शुरुआत और समाप्ति तिथि और समय सेट करें', // Set election start and end date and time
            ],
            'pending_checkout' => [
                'label' => 'भुगतान', // Payment
                'description' => 'इस चुनाव को प्रकाशित करने के लिए भुगतान पूरा करें', // Complete payment to publish this election
            ],
            'draft' => [
                'label' => 'प्रकाशित करने के लिए तैयार', // Ready to Publish
            ],
            'upcoming' => [
                'label' => 'अभी शुरू नहीं हुआ', // Yet to Start
            ],
            'open' => [
                'label' => 'वोटिंग के लिए खुला है', // Open for Voting
            ],
            'expired' => [
                'label' => 'वोटिंग समय समाप्त हो गया है', // Voting time ended
            ],
            'closed' => [
                'label' => 'वोटिंग बंद हो गई है', // Voting Closed
                'description' => 'इस चुनाव के लिए वोटिंग :datetime को बंद कर दी गई है', // Voting is closed for this election on :datetime
            ],
            'completed' => [
                'label' => 'सम्पन्न', // Completed
            ],
            'cancelled' => [
                'label' => 'रद्द', // Cancelled
                'description' => 'यह चुनाव :datetime पर रद्द किया गया है', // This election is cancelled on :datetime
            ],
        ],
        'election_collaborator_permission' => [
            'full_access' => [
                'label' => 'पूरी पहुंच', // Full Access
            ],
            'read_only' => [
                'label' => 'केवल पढ़ें', // Read Only
            ],
            'no_access' => [
                'label' => 'कोई पहुंच नहीं', // No Access
            ],
        ],
    ],
];
