<?php

return [
    'base' => [
        'widgets' => [
            'voted_ballots' => [
                'heading' => 'Voted Electors',
                'table' => [
                    'membership_number' => [
                        'label' => 'Membership Number',
                    ],
                    'elector' => [
                        'label' => 'Elector',
                    ],
                    'voted_at' => [
                        'label' => 'Voted At',
                    ],
                    'type' => [
                        'label' => 'Method',
                    ],
                    'description' => 'Updated at :timestamp',
                ],
            ],
            'election_stats_overview' => [
                'total_electors' => [
                    'label' => 'Total Electors',
                ],
                'voted_electors' => [
                    'label' => 'Voted',
                ],
                'non_voted_electors' => [
                    'label' => 'Non-Voted',
                ],
            ],
            'non_voted_electors' => [
                'description' => 'Updated at :timestamp',
                'table' => [
                    'membership_number' => [
                        'label' => 'Membership Number',
                    ],
                    'display_name' => [
                        'label' => 'Elector',
                    ],
                ],
            ],
        ],
    ],
    'election' => [
        'pages' => [
            'auth' => [
                'login' => [
                    'form' => [
                        'actions' => [
                            'authenticate' => [
                                'get_otp_label' => 'Get OTP',
                                'sign_in_label' => 'Sign in',
                            ],
                        ],
                        'phone' => [
                            'label' => 'Your phone number',
                        ],
                        'consent' => [
                            'label' => 'I agree to receive OTP (One Time Password)',
                        ],
                    ],
                ],
            ],
            'mfa' => [
                'notice' => [
                    'form' => [
                        'heading' => 'MFA Verification',
                        'consent' => [
                            'label' => 'I agree to receive OTP',
                        ],
                        'actions' => [
                            'submit' => [
                                'label' => 'Send OTP',
                            ],
                        ],
                        'notice' => [
                            'content' => '6 digit OTP code will be sent to your :via.',
                        ],
                    ],
                ],
                'verify' => [
                    'form' => [
                        'heading' => 'MFA Verification',
                        'actions' => [
                            'resend' => [
                                'label' => 'Resend',
                                'success_notification' => [
                                    'title' => 'OTP resent',
                                ],
                            ],
                        ],
                        'notice' => [
                            'content' => '6 digit OTP code has been sent to your :via.',
                        ],
                    ],
                ],
            ],
            'concerns' => [
                'interacts_with_election' => [
                    'subheading' => [
                        'to' => 'to',
                    ],
                ],
            ],
            'ballot' => [
                'index' => [
                    'form' => [
                        'actions' => [
                            'continue' => [
                                'label' => 'Continue',
                            ],
                            'confirm' => [
                                'label' => 'Confirm',
                            ],
                            'back' => [
                                'label' => 'Back',
                            ],
                            'submit' => [
                                'booth_success_notification' => [
                                    'title' => 'Submitted',
                                    'body' => 'Your votes are submitted successfully. This page will be automatically expire in 30 seconds.',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'user' => [
        'election-resource' => [
            'model_label' => 'Election',
            'plural_model_label' => 'Elections',
            'form' => [
                'name' => [
                    'label' => 'Election name / title',
                ],
                'booth_starts_at' => [
                    'label' => 'Booth starts at',
                ],
                'booth_ends_at' => [
                    'label' => 'Booth ends at',
                ],
                'description' => 'Description',
                'starts_at' => [
                    'label' => 'Starts at',
                ],
                'ends_at' => [
                    'label' => 'Ends at',
                ],
                'timezone' => [
                    'label' => 'Timezone',
                ],
            ],
            'pages' => [
                'base' => [
                    'access_denied' => [
                        'notification' => [
                            'title' => 'Not allowed',
                            'body' => 'Complete previous steps before accessing this page',
                        ],
                    ],
                    'subheading' => '**:starts** to **:ends**',
                ],
                'plan' => [
                    'heading' => 'Choose a Plan',
                    'description' => 'Choose a plan that best suits your needs.',
                    'total_electors_input' => [
                        'prefix' => 'for',
                        'suffix' => 'electors',
                    ],
                    'price_loading_label' => 'Calculating...',
                    'whats_included' => 'What’s included',
                    'add_on_features' => 'Add-on features',
                    'per_elector' => '/elector',
                    'actions' => [
                        'choose_plan' => [
                            'label' => 'Select',
                        ],
                    ],
                    'notes' => 'Invoices and receipts available for easy company reimbursement',
                    'navigation_label' => 'Choose Plan',
                ],
                'preference' => [
                    'form' => [
                        'ballot_access_section' => [
                            'heading' => 'Ballot Access',
                            'description' => 'Choose how electors will access the ballot.',
                        ],
                        'ballot_link_common' => [
                            'label' => 'Common link',
                            'validation' => [
                                'accepted_if' => 'This must be enabled when unique link is disabled',
                            ],
                        ],
                        'ballot_link_unique' => [
                            'label' => 'Unique link',
                            'validation' => [
                                'custom_rule' => 'When this is enabled, at least one delivery method must be enabled',
                            ],
                        ],
                        'ip_restriction_section' => [
                            'description' => 'Restrict electors voting from same IP address',
                            'heading' => 'IP Restriction',
                        ],
                        'ip_restriction' => [
                            'label' => 'Enable',
                        ],
                        'ip_restriction_threshold' => [
                            'heading' => 'Max. votes',
                        ],
                        'ballot_link_delivery_section' => [
                            'heading' => 'Ballot Link Delivery',
                            'description' => 'Electors will receive their ballot link through these medium.',
                        ],
                        'ballot_link_mail' => [
                            'label' => 'Email',
                        ],
                        'ballot_link_sms' => [
                            'label' => 'SMS',
                        ],
                        'ballot_link_whatsapp' => [
                            'label' => 'Whatsapp',
                            'hint' => 'Coming soon',
                        ],
                        'mfa_code_delivery_section' => [
                            'heading' => 'MFA Code Delivery',
                            'description' => 'Electors will receive MFA (Multi-Factor Authentication) code through these medium. This will be used to verify the elector\'s identity before submitting their votes.',
                        ],
                        'mfa_mail' => [
                            'label' => 'Email',
                        ],
                        'mfa_sms' => [
                            'label' => 'SMS',
                        ],
                        'mfa_sms_auto_fill_only' => [
                            'hint_icon' => [
                                'tooltip' => 'Supports only on Android (Chrome) and iOS (Safari) devices. Voting from other devices will be restricted.',
                            ],
                            'label' => 'Prevent manual entry',
                        ],
                        'mfa_whatsapp' => [
                            'hint' => 'Coming soon',
                            'label' => 'Whatsapp',
                        ],
                        'ballot_ack_section' => [
                            'heading' => 'Ballot Acknowledgement',
                            'description' => 'Electors will receive confirmation of their votes through these medium.',
                        ],
                        'voted_confirmation_mail' => [
                            'label' => 'Email',
                        ],
                        'voted_confirmation_sms' => [
                            'label' => 'SMS',
                        ],
                        'voted_confirmation_whatsapp' => [
                            'hint' => 'Coming soon',
                            'label' => 'Whatsapp',
                        ],
                        'ballot_copy_section' => [
                            'heading' => 'Sharing of Electors\'s Ballot Copy',
                            'description' => 'Electors will be able to share their voted ballot copy through these medium.',
                        ],
                        'voted_ballot_download' => [
                            'label' => 'Direct download',
                        ],
                        'voted_ballot_mail' => [
                            'label' => 'Email',
                        ],
                        'voted_ballot_whatsapp' => [
                            'hint' => 'Coming soon',
                            'label' => 'Whatsapp',
                        ],
                        'security_preference_section' => [
                            'heading' => 'Security preference',
                            'description' => 'These are security preferences that you can enable for your election.',
                        ],
                        'dnt_votes' => [
                            'helper_text' => 'This will prevent the system from tracking the electors\' votes.',
                            'label' => 'Do Not Track electors\'s votes',
                        ],
                        'voted_ballot_update' => [
                            'helper_text' => 'This will allow electors to update their voted ballot.',
                            'label' => 'Editable votes',
                        ],
                        'prevent_duplicate_device' => [
                            'helper_text' => 'This will prevent electors from casting votes from same device.',
                            'hint' => 'Experimental',
                            'hint_icon' => [
                                'tooltip' => 'This is experimental and may not work as expected. Please use with caution.',
                            ],
                            'label' => 'Prevent duplicate device',
                        ],
                        'elector_preference_section' => [
                            'heading' => 'Elector preference',
                        ],
                        'elector_duplicate_email' => [
                            'helper_text' => 'This will allow you to add multiple electors with same email address.',
                            'label' => 'Duplicate email addresses',
                        ],
                        'elector_duplicate_phone' => [
                            'helper_text' => 'This will allow you to add multiple electors with same phone number.',
                            'label' => 'Duplicate phone numbers',
                        ],
                        'elector_update_after_publish' => [
                            'helper_text' => 'This will allow you to update the elector details after the election is published.',
                            'label' => 'Allow elector update after publish',
                        ],
                        'candidate_preference_section' => [
                            'heading' => 'Candidate preference',
                        ],
                        'candidate_sort' => [
                            'label' => 'Display order',
                        ],
                        'candidate_photo' => [
                            'label' => 'Candidate photo',
                        ],
                        'candidate_symbol' => [
                            'label' => 'Candidate symbol',
                        ],
                        'candidate_bio' => [
                            'label' => 'Candidate bio text',
                        ],
                        'candidate_attachment' => [
                            'label' => 'Candidate attachments',
                        ],
                        'candidate_group' => [
                            'label' => 'Candidate group',
                        ],
                        'advanced_preferences_section' => [
                            'heading' => 'Advanced preferences',
                            'description' => 'These are advanced preferences that you can enable for your election.',
                        ],
                        'segmented_ballot' => [
                            'helper_text' => 'This will allow you to segment the ballot based on the elector details.',
                            'label' => 'Segmented ballot',
                        ],
                        'booth_voting_section' => [
                            'heading' => 'Booth voting',
                            'description' => 'This will allow electors to cast votes from a specific location. This is useful for conducting elections in a physical location.',
                        ],
                        'booth_voting' => [
                            'label' => 'Enable booth voting',
                        ],
                    ],
                    'actions' => [
                        'change_plan' => [
                            'label' => 'Change Plan',
                        ],
                        'save' => [
                            'label' => 'Save Preference',
                            'success_notification' => [
                                'title' => 'Saved',
                            ],
                        ],
                    ],
                    'navigation_label' => 'Preference',
                ],
                'electors' => [
                    'actions' => [
                        'next' => [
                            'label' => 'Next',
                        ],
                        'send_ballot_link' => [
                            'label' => 'Send Ballot Link',
                            'success_notification' => [
                                'title' => 'Ballot Link Sent',
                                'body' => 'The ballot link has been sent to the elector.',
                            ],
                        ],
                    ],
                    'bulk_actions' => [
                        'send_ballot_link' => [
                            'label' => 'Send Ballot Links',
                            'success_notification' => [
                                'title' => 'Ballot Links Sent',
                                'body' => 'Ballot links have been sent to selected electors who have not yet voted.',
                            ],
                        ],
                    ],
                    'navigation_label' => 'Electors',
                ],
                'ballot_setup' => [
                    'infolist' => [
                        'positions' => [
                            'empty_state' => [
                                'heading' => 'Set up your ballot',
                                'description' => 'Add positions and candidates to your ballot',
                            ],
                            'candidates' => [
                                'empty_state' => [
                                    'heading' => 'No candidates',
                                    'description' => 'Create new candidate',
                                ],
                            ],
                        ],
                    ],
                    'actions' => [
                        'next' => [
                            'label' => 'Next',
                        ],
                        'edit_position' => [
                            'success_notification' => [
                                'title' => 'Saved',
                            ],
                        ],
                        'delete_position' => [
                            'success_notification' => [
                                'title' => 'Deleted',
                            ],
                        ],
                        'reorder_candidate' => [
                            'modal_actions' => [
                                'submit' => [
                                    'label' => 'Save changes',
                                ],
                            ],
                            'modal_heading' => 'Reorder :label Candidates',
                            'success_notification' => [
                                'title' => 'Saved',
                            ],
                        ],
                        'import_candidate' => [
                            'label' => 'Import',
                        ],
                        'create_candidate' => [
                            'label' => 'New candidate',
                            'success_notification' => [
                                'title' => 'Created',
                            ],
                            'modal_heading' => 'New :position candidate',
                        ],
                        'edit_candidate' => [
                            'modal_heading' => 'Edit :label',
                            'modal_actions' => [
                                'submit' => [
                                    'label' => 'Save changes',
                                ],
                            ],
                            'success_notification' => [
                                'title' => 'Saved',
                            ],
                        ],
                        'delete_candidate' => [
                            'modal_heading' => 'Delete :label',
                            'success_notification' => [
                                'title' => 'Deleted',
                            ],
                        ],
                    ],
                    'navigation_label' => 'Setup Ballot',
                ],
                'dashboard' => [
                    'navigation_label' => 'Dashboard',
                ],
                'booth_tokens' => [
                    'navigation_label' => 'Booth Tokens',
                ],
                'collaborators' => [
                    'navigation_label' => 'Collaborators',
                    'navigation_group' => 'Others',
                    'table' => [
                        'name' => [
                            'label' => 'Name',
                        ],
                        'email' => [
                            'label' => 'Email address',
                        ],
                        'actions' => [
                            'detach' => [
                                'label' => 'Remove',
                            ],
                            'set_as_admin' => [
                                'label' => 'Set as admin',
                                'success_notification' => [
                                    'title' => 'Admin changed successfully',
                                ],
                            ],
                            'invite_collaborator' => [
                                'label' => 'Invite collaborator',
                                'modal_actions' => [
                                    'submit' => [
                                        'label' => 'Invite',
                                    ],
                                ],
                                'success_notification' => [
                                    'title' => 'Invitation sent successfully',
                                ],
                            ],
                        ],
                        'heading' => 'Collaborators',
                    ],
                    'designation' => [
                        'label' => 'Designation',
                    ],
                    'form' => [
                        'designation' => [
                            'label' => 'Designation',
                            'placeholder' => 'e.g. Returning Officer / Election Officer',
                        ],
                        'permissions' => [
                            'label' => 'Permissions',
                            'preference' => [
                                'label' => 'Preference',
                            ],
                            'electors' => [
                                'label' => 'Electors',
                            ],
                            'ballot_setup' => [
                                'label' => 'Ballot setup',
                            ],
                            'timing' => [
                                'label' => 'Timing',
                            ],
                            'payment' => [
                                'label' => 'Payment',
                            ],
                            'monitor_tokens' => [
                                'label' => 'Monitor tokens',
                            ],
                            'elector_logs' => [
                                'label' => 'Elector logs',
                            ],
                        ],
                        'email' => [
                            'label' => 'Email address',
                            'validation' => [
                                'not_in' => 'The email address is already a collaborator.',
                            ],
                        ],
                    ],
                ],
                'monitor_tokens' => [
                    'navigation_label' => 'Monitor Tokens',
                    'table' => [
                        'actions' => [
                            'create' => [
                                'label' => 'New Token',
                                'success_notification' => [
                                    'title' => 'Created',
                                ],
                            ],
                        ],
                        'key' => [
                            'label' => 'Token',
                        ],
                        'activated_at' => [
                            'label' => 'Activated At',
                        ],
                        'user_agent' => [
                            'label' => 'Device',
                        ],
                    ],
                ],
                'result' => [
                    'navigation_label' => 'Result',
                ],
                'logs' => [
                    'elector_ballots' => [
                        'navigation_label' => 'Voted / Non-Voted',
                        'navigation_group' => 'Elector Reports',
                    ],
                    'elector_emails' => [
                        'navigation_label' => 'Email Logs',
                        'navigation_group' => 'Elector Reports',
                    ],
                    'elector_sms_messages' => [
                        'navigation_label' => 'SMS Logs',
                        'navigation_group' => 'Elector Reports',
                    ],
                ],
            ],
            'table' => [
                'code' => [
                    'label' => 'Code',
                ],
                'name' => [
                    'label' => 'Title',
                ],
                'status' => [
                    'label' => 'Status',
                ],
            ],
            'actions' => [
                'set_timing' => [
                    'label' => 'Set Timing',
                ],
                'edit_timing' => [
                    'label' => 'Edit Timing',
                ],
                'cancel' => [
                    'label' => 'Cancel',
                    'success_notification' => [
                        'title' => 'Cancelled',
                    ],
                    'modal_actions' => [
                        'submit' => [
                            'label' => 'Yes',
                        ],
                        'cancel' => [
                            'label' => 'No',
                        ],
                    ],
                ],
                'publish' => [
                    'label' => 'Publish',
                    'success_notification' => [
                        'title' => 'Published',
                    ],
                    'form' => [
                        'notify_electors' => [
                            'label' => 'Send ballot link all electors',
                        ],
                    ],
                ],
                'close' => [
                    'pre_close' => [
                        'label' => 'Pre-close',
                    ],
                    'label' => 'Close',
                    'success_notification' => [
                        'title' => 'Closed',
                    ],
                ],
                'generate_result' => [
                    'label' => 'Generate Result',
                    'success_notification' => [
                        'title' => 'Result generated',
                    ],
                ],
                'edit' => [
                    'label' => 'Edit title',
                ],
                'replicate' => [
                    'form' => [
                        'replicate_electors' => [
                            'label' => 'Include electors',
                        ],
                        'replicate_ballot_setup' => [
                            'label' => 'Include ballot setup',
                        ],
                    ],
                ],
                'preview' => [
                    'label' => 'Preview',
                ],
                'collaborators' => [
                    'label' => 'Collaborators',
                ],
            ],
        ],
        'elector-resource' => [
            'model_label' => 'Elector',
            'plural_model_label' => 'Electors',
            'form' => [
                'email' => [
                    'label' => 'Email address',
                ],
                'first_name' => [
                    'label' => 'First name',
                    'placeholder' => 'First name',
                ],
                'full_name' => [
                    'label' => 'Full name',
                ],
                'groups' => [
                    'label' => 'Groups',
                ],
                'last_name' => [
                    'label' => 'Last name',
                    'placeholder' => 'Last name',
                ],
                'membership_number' => [
                    'label' => 'Membership number',
                ],
                'phone' => [
                    'label' => 'Phone number',
                ],
                'title' => [
                    'label' => 'Salutation',
                    'placeholder' => 'Title',
                ],
                'segments' => [
                    'label' => 'Segments',
                    'helper_text' => 'e.g. Life Member, Gold Member, Silver Member, etc.',
                    'placeholder' => 'Select segments',
                    'create_action' => [
                        'heading' => 'Create Segment',
                    ],
                ],
            ],
            'table' => [
                'membership_number' => [
                    'label' => 'Membership number',
                ],
                'full_name' => [
                    'label' => 'Full name',
                ],
                'phone' => [
                    'label' => 'Phone number',
                ],
                'email' => [
                    'label' => 'Email address',
                ],
                'segments' => [
                    'label' => 'Segments',
                ],
            ],
        ],
        'candidate-resource' => [
            'form' => [
                'attachments' => [
                    'label' => 'Attachments',
                ],
                'bio' => [
                    'label' => 'Bio',
                ],
                'elector_id' => [
                    'label' => 'Elector ID',
                ],
                'candidate_group_id' => [
                    'label' => 'Group',
                    'placeholder' => 'Choose a group',
                    'form' => [
                        'name' => [
                            'label' => 'Group name',
                        ],
                        'short_name' => [
                            'label' => 'Short name',
                        ],
                    ],
                ],
                'email' => [
                    'label' => 'Email address',
                    'placeholder' => 'Email address',
                ],
                'first_name' => [
                    'label' => 'First name',
                    'placeholder' => 'First name',
                ],
                'last_name' => [
                    'label' => 'Last name',
                    'placeholder' => 'Last name',
                ],
                'membership_number' => [
                    'label' => 'Membership number',
                    'placeholder' => 'Membership number',
                    'validation' => [
                        'exists' => 'This :attribute is not found in electors data',
                    ],
                    'helper_text' => 'If membership number is not applicable then leave it blank.',
                ],
                'phone' => [
                    'label' => 'Phone number',
                ],
                'photo' => [
                    'label' => 'Photo',
                    'placeholder' => 'Drag & Drop your photo or <span class="filepond--label-action">Browse</span>',
                ],
                'position_id' => [
                    'label' => 'Position',
                    'placeholder' => 'Choose a position',
                ],
                'symbol' => [
                    'label' => 'Symbol',
                    'placeholder' => 'Drag & Drop your symbol or <span class="filepond--label-action">Browse</span>',
                ],
                'title' => [
                    'label' => 'Salutation',
                    'placeholder' => 'Title',
                ],
                'logo' => [
                    'label' => 'Logo',
                ],
                'full_name' => [
                    'label' => 'Full name',
                ],
            ],
        ],
        'organisation-resource' => [
            'form' => [
                'country' => [
                    'label' => 'Country',
                ],
                'logo' => [
                    'label' => 'Logo',
                    'placeholder' => 'Drag & Drop your logo or <span class="filepond--label-action">Browse</span>',
                ],
                'name' => [
                    'label' => 'Organisation name',
                ],
                'timezone' => [
                    'label' => 'Timezone',
                ],
            ],
        ],
        'position-resource' => [
            'form' => [
                'elector_groups' => [
                    'label' => 'Eligible groups',
                ],
                'name' => [
                    'label' => 'Position name',
                    'placeholder' => 'President / Secretary / EC Members etc.,',
                ],
                'quota' => [
                    'label' => 'Available posts',
                ],
                'abstain' => [
                    'label' => 'Enable abstain',
                ],
                'threshold' => [
                    'label' => 'Min selection',
                ],
                'segments' => [
                    'label' => 'Segments',
                    'form' => [
                        'name' => [
                            'label' => 'Segment name',
                        ],
                    ],
                    'actions' => [
                        'create' => [
                            'heading' => 'Create Segment',
                        ],
                    ],
                ],
            ],
            'table' => [
                'name' => [
                    'label' => 'Position name',
                ],
                'quota' => [
                    'label' => 'Available posts',
                ],
                'threshold' => [
                    'label' => 'Min selection',
                ],
                'segments' => [
                    'label' => 'Segments',
                ],
            ],
            'label' => 'Position',
            'plural_label' => 'Positions',
        ],
        'user-resource' => [
            'form' => [
                'name' => [
                    'label' => 'Contact name',
                ],
                'email' => [
                    'label' => 'Email address',
                ],
                'password' => [
                    'label' => 'Password',
                ],
                'password_confirmation' => [
                    'label' => 'Confirm password',
                ],
            ],
        ],
        'pages' => [
            'auth' => [
                'register' => [
                    'title' => 'Host sign up',
                    'heading' => 'Host sign up',
                ],
            ],
            'organisation' => [
                'edit' => [
                    'label' => 'Organisation Profile',
                ],
                'register' => [
                    'label' => 'Organisation Setup',
                    'form' => [
                        'actions' => [
                            'register' => [
                                'label' => 'Finish Setup',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'forms' => [
        'components' => [
            'vote_picker' => [
                'general' => [
                    'selected' => 'selected',
                ],
                'placeholder' => [
                    'none_selected' => 'None selected',
                    'no_candidates' => 'No candidates',
                ],
            ],
        ],
    ],
];
