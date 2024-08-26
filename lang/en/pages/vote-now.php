<?php

return [
    'seo' => [
        'title' => 'Vote Now',
        'description' => 'Vote now for your favourite candidate',
    ],
    'content' => [
        'form' => [
            'heading' => 'Vote Now',
            'description' => 'Vote for your favourite candidate',
            'fields' => [
                'has_election_code' => [
                    'label' => 'Do you have an election code?',
                ],
                'election_code' => [
                    'helper_text' => 'You can get this from your election officer',
                    'label' => 'Election Code',
                    'placeholder' => 'Enter your election code',
                ],
                'organisation_id' => [
                    'label' => 'Organisation',
                    'placeholder' => 'Select your organisation',
                ],
                'election_id' => [
                    'label' => 'Election',
                    'placeholder' => 'Select your election',
                ],
            ],
            'actions' => [
                'proceed' => [
                    'label' => 'Proceed to Vote',
                ],
            ],
        ],
    ],
];
