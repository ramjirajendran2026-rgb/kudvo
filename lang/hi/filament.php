<?php

return [
    'base' => [
        'widgets' => [
            'voted_ballots' => [
                'heading' => 'मतदान करने वाले निर्वाचक', // Voted Electors
                'table' => [
                    'membership_number' => [
                        'label' => 'सदस्यता संख्या', // Membership Number
                    ],
                    'elector' => [
                        'label' => 'निर्वाचक', // Elector
                    ],
                    'voted_at' => [
                        'label' => 'मतदान किया गया', // Voted At
                    ],
                    'type' => [
                        'label' => 'विधि', // Method
                    ],
                    'description' => ':timestamp पर अपडेट किया गया', // Updated at :timestamp
                ],
            ],
            'election_stats_overview' => [
                'total_electors' => [
                    'label' => 'कुल निर्वाचक', // Total Electors
                ],
                'voted_electors' => [
                    'label' => 'मतदान किया', // Voted
                ],
                'non_voted_electors' => [
                    'label' => 'गैर-मतदान', // Non-Voted
                ],
            ],
            'non_voted_electors' => [
                'description' => ':timestamp पर अपडेट किया गया', // Updated at :timestamp
                'table' => [
                    'membership_number' => [
                        'label' => 'सदस्यता संख्या', // Membership Number
                    ],
                    'display_name' => [
                        'label' => 'निर्वाचक', // Elector
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
                                'get_otp_label' => 'OTP प्राप्त करें', // Get OTP
                                'sign_in_label' => 'साइन इन करें', // Sign in
                            ],
                        ],
                        'phone' => [
                            'label' => 'आपका फ़ोन नंबर', // Your phone number
                        ],
                        'consent' => [
                            'label' => 'मैं OTP (एक समय पासवर्ड) प्राप्त करने के लिए सहमत हूं', // I agree to receive OTP (One Time Password)
                        ],
                    ],
                ],
            ],
            'mfa' => [
                'notice' => [
                    'form' => [
                        'heading' => 'MFA सत्यापन', // MFA Verification
                        'consent' => [
                            'label' => 'मैं OTP प्राप्त करने के लिए सहमत हूं', // I agree to receive OTP
                        ],
                        'actions' => [
                            'submit' => [
                                'label' => 'OTP भेजें', // Send OTP
                            ],
                        ],
                        'notice' => [
                            'content' => '6 अंकों का OTP कोड आपके :via पर भेजा जाएगा।', // 6 digit OTP code will be sent to your :via.
                        ],
                    ],
                ],
                'verify' => [
                    'form' => [
                        'heading' => 'MFA सत्यापन', // MFA Verification
                        'actions' => [
                            'resend' => [
                                'label' => 'पुनः भेजें', // Resend
                                'success_notification' => [
                                    'title' => 'OTP पुनः भेजा गया', // OTP resent
                                ],
                            ],
                        ],
                        'notice' => [
                            'content' => '6 अंकों का OTP कोड आपके :via पर भेजा गया है।', // 6 digit OTP code has been sent to your :via.
                        ],
                    ],
                ],
            ],
            'concerns' => [
                'interacts_with_election' => [
                    'subheading' => [
                        'to' => 'करने के लिए', // to
                    ],
                ],
            ],
            'ballot' => [
                'index' => [
                    'form' => [
                        'actions' => [
                            'continue' => [
                                'label' => 'जारी रखें', // Continue
                            ],
                            'confirm' => [
                                'label' => 'पुष्टि करें', // Confirm
                            ],
                            'back' => [
                                'label' => 'वापस', // Back
                            ],
                            'submit' => [
                                'booth_success_notification' => [
                                    'title' => 'सबमिट किया गया', // Submitted
                                    'body' => 'आपके वोट सफलतापूर्वक सबमिट किए गए हैं। यह पृष्ठ 30 सेकंड में स्वतः समाप्त हो जाएगा।', // Your votes are submitted successfully. This page will be automatically expire in 30 seconds.
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
            'model_label' => 'चुनाव', // Election
            'plural_model_label' => 'चुनाव', // Elections
            'form' => [
                'name' => [
                    'label' => 'चुनाव का नाम / शीर्षक', // Election name / title
                ],
                'booth_starts_at' => [
                    'label' => 'बूथ की शुरुआत', // Booth starts at
                ],
                'booth_ends_at' => [
                    'label' => 'बूथ समाप्त होता है', // Booth ends at
                ],
                'description' => 'विवरण', // Description
                'starts_at' => [
                    'label' => 'इस समय शुरू होता है', // Starts at
                ],
                'ends_at' => [
                    'label' => 'इस समय समाप्त होता है', // Ends at
                ],
                'timezone' => [
                    'label' => 'समय क्षेत्र', // Timezone
                ],
            ],
            'pages' => [
                'base' => [
                    'access_denied' => [
                        'notification' => [
                            'title' => 'अनुमति नहीं है', // Not allowed
                            'body' => 'पिछले कदम पूरे करें इस पृष्ठ तक पहुंचने से पहले', // Complete previous steps before accessing this page
                        ],
                    ],
                    'subheading' => '**:starts** से **:ends**', // **:starts** to **:ends**
                ],
                'plan' => [
                    'heading' => 'एक योजना चुनें', // Choose a Plan
                    'description' => 'उन योजनाओं का चयन करें जो आपकी आवश्यकताओं को सबसे अधिक समाप्त होता है।', // Choose a plan that best suits your needs.
                    'whats_included' => 'क्या शामिल है', // What’s included
                    'add_on_features' => 'अतिरिक्त सुविधाएँ', // Add-on features
                    'per_elector' => '/चुनाव', // /elector
                    'actions' => [
                        'choose_plan' => [
                            'label' => 'चुनें', // Select
                        ],
                    ],
                    'notes' => 'बिल और रसीदें आसान कंपनी के प्रत्यार्थन के लिए उपलब्ध हैं।', // Invoices and receipts available for easy company reimbursement
                    'navigation_label' => 'योजना', // Plan
                ],
                'preference' => [
                    'form' => [
                        'ballot_access_section' => [
                            'heading' => 'बैलेट एक्सेस', // Ballot Access
                            'description' => 'चुनावकर्ताओं को बैलेट तक पहुंच कैसे मिलेगी चुनें।', // Choose how electors will access the ballot.
                        ],
                        'ballot_link_common' => [
                            'label' => 'सामान्य लिंक', // Common link
                            'validation' => [
                                'accepted_if' => 'यह सक्षम किया जाना चाहिए जब अद्वितीय लिंक अक्षम होता है।', // This must be enabled when unique link is disabled
                            ],
                        ],
                        'ballot_link_unique' => [
                            'label' => 'अद्वितीय लिंक', // Unique link
                            'validation' => [
                                'custom_rule' => 'जब यह सक्षम होता है, तो कम से कम एक वितरण विधि सक्षम होनी चाहिए।', // When this is enabled, at least one delivery method must be enabled
                            ],
                        ],
                        'ip_restriction_section' => [
                            'description' => 'समान आईपी पते से चुनावकर्ताओं को मतदान सीमित करें', // Restrict electors voting from same IP address
                            'heading' => 'आईपी प्रतिबंध', // IP Restriction
                        ],
                        'ip_restriction' => [
                            'label' => 'सक्षम करें', // Enable
                        ],
                        'ip_restriction_threshold' => [
                            'heading' => 'अधिकतम वोट', // Max. votes
                        ],
                        'ballot_link_delivery_section' => [
                            'heading' => 'बैलेट लिंक वितरण', // Ballot Link Delivery
                            'description' => 'चुनावकर्ताओं को इन माध्यमों के माध्यम से उनके बैलेट लिंक प्राप्त होगा।', // Electors will receive their ballot link through these medium.
                        ],
                        'ballot_link_mail' => [
                            'label' => 'ईमेल', // Email
                        ],
                        'ballot_link_sms' => [
                            'label' => 'SMS', // SMS
                        ],
                        'ballot_link_whatsapp' => [
                            'label' => 'Whatsapp',
                            'hint' => 'जल्द आ रहा है', // Coming soon
                        ],
                        'mfa_code_delivery_section' => [
                            'heading' => 'MFA कोड वितरण', // MFA Code Delivery
                            'description' => 'चुनावकर्ताओं को इन माध्यमों के माध्यम से MFA (Multi-Factor Authentication) कोड प्राप्त होगा। यह उनकी पहचान सत्यापित करने के लिए उपयोग किया जाएगा पहले अपने वोट जमा करने से पहले।', // Electors will receive MFA (Multi-Factor Authentication) code through these medium. This will be used to verify the elector's identity before submitting their votes.
                        ],
                        'mfa_mail' => [
                            'label' => 'ईमेल', // Email
                        ],
                        'mfa_sms' => [
                            'label' => 'SMS', // SMS
                        ],
                        'mfa_sms_auto_fill_only' => [
                            'hint_icon' => [
                                'tooltip' => 'केवल Android (Chrome) और iOS (Safari) डिवाइस पर समर्थित है। अन्य डिवाइस से वोटिंग परिमित होगी।', // Supports only on Android (Chrome) and iOS (Safari) devices. Voting from other devices will be restricted.
                            ],
                            'label' => 'मैन्युअल एंट्री रोकें', // Prevent manual entry
                        ],
                        'mfa_whatsapp' => [
                            'hint' => 'जल्द आ रहा है', // Coming soon
                            'label' => 'Whatsapp',
                        ],
                        'ballot_ack_section' => [
                            'heading' => 'बैलेट प्रिपत्रिक', // Ballot Acknowledgement
                            'description' => 'चुनावकर्ताओं को इन माध्यमों के माध्यम से उनके वोटों की पुष्टि होगी।', // Electors will receive confirmation of their votes through these medium.
                        ],
                        'voted_confirmation_mail' => [
                            'label' => 'ईमेल', // Email
                        ],
                        'voted_confirmation_sms' => [
                            'label' => 'SMS', // SMS
                        ],
                        'voted_confirmation_whatsapp' => [
                            'hint' => 'जल्द आ रहा है', // Coming soon
                            'label' => 'Whatsapp',
                        ],
                        'ballot_copy_section' => [
                            'heading' => '

चुनावकर्ताओं की बैलेट प्रति का साझा करना', // Sharing of Electors's Ballot Copy
                            'description' => 'चुनावकर्ताओं को इन माध्यमों के माध्यम से उनके वोटेड बैलेट प्रति को साझा करने की अनुमति होगी।', // Electors will be able to share their voted ballot copy through these medium.
                        ],
                        'voted_ballot_download' => [
                            'label' => 'सीधे डाउनलोड', // Direct download
                        ],
                        'voted_ballot_mail' => [
                            'label' => 'ईमेल', // Email
                        ],
                        'voted_ballot_whatsapp' => [
                            'hint' => 'जल्द आ रहा है', // Coming soon
                            'label' => 'Whatsapp',
                        ],
                        'security_preference_section' => [
                            'heading' => 'सुरक्षा प्राथमिकता', // Security preference
                            'description' => 'ये वे सुरक्षा प्राथमिकताएं हैं जो आप अपने चुनाव के लिए सक्षम कर सकते हैं।', // These are security preferences that you can enable for your election.
                        ],
                        'dnt_votes' => [
                            'helper_text' => 'यह चुनावकर्ताओं के वोट को प्रणाली के ट्रैकिंग से रोकेगा।', // This will prevent the system from tracking the electors' votes.
                            'label' => 'चुनावकर्ताओं के वोट ट्रैक न करें', // Do Not Track electors's votes
                        ],
                        'voted_ballot_update' => [
                            'helper_text' => 'यह चुनावकर्ताओं को उनके वोटेड बैलेट को अपडेट करने की अनुमति देगा।', // This will allow electors to update their voted ballot.
                            'label' => 'संपादन योग्य वोट', // Editable votes
                        ],
                        'prevent_duplicate_device' => [
                            'helper_text' => 'यह चुनावकर्ताओं को समान डिवाइस से वोट देने से रोकेगा।', // This will prevent electors from casting votes from same device.
                            'hint' => 'प्रयोगशाला', // Experimental
                            'hint_icon' => [
                                'tooltip' => 'यह प्रयोगशाला है और अपेक्षित रूप से काम नहीं कर सकती। कृपया सावधानी से उपयोग करें।', // This is experimental and may not work as expected. Please use with caution.
                            ],
                            'label' => 'डुप्लिकेट डिवाइस को रोकें', // Prevent duplicate device
                        ],
                        'elector_preference_section' => [
                            'heading' => 'चुनावकर्ता पसंद', // Elector preference
                        ],
                        'elector_duplicate_email' => [
                            'helper_text' => 'यह आपको एक ही ईमेल पते के साथ कई चुनावकर्ता जोड़ने की अनुमति देगा।', // This will allow you to add multiple electors with same email address.
                            'label' => 'डुप्लिकेट ईमेल पते', // Duplicate email addresses
                        ],
                        'elector_duplicate_phone' => [
                            'helper_text' => 'यह आपको एक ही फ़ोन नंबर के साथ कई चुनावकर्ता जोड़ने की अनुमति देगा।', // This will allow you to add multiple electors with same phone number.
                            'label' => 'डुप्लिकेट फोन नंबर', // Duplicate phone numbers
                        ],
                        'elector_update_after_publish' => [
                            'helper_text' => 'यह आपको चुनाव प्रकाशित होने के बाद चुनावकर्ता विवरण को अपडेट करने की अनुमति देगा।', // This will allow you to update the elector details after the election is published.
                            'label' => 'प्रकाशन के बाद चुनावकर्ता अपडेट की अनुमति दें', // Allow elector update after publish
                        ],
                        'candidate_preference_section' => [
                            'heading' => 'उम्मीदवार पसंद', // Candidate preference
                        ],
                        'candidate_sort' => [
                            'label' => 'प्रदर्शन क्रम', // Display order
                        ],
                        'candidate_photo' => [
                            'label' => 'उम्मीदवार फोटो', // Candidate photo
                        ],
                        'candidate_symbol' => [
                            'label' => 'उम्मीदवार प्रतीक', // Candidate symbol
                        ],
                        '

candidate_bio' => [
                            'label' => 'उम्मीदवार जीवनी पाठ', // Candidate bio text
                        ],
                        'candidate_attachment' => [
                            'label' => 'उम्मीदवार संलग्नक', // Candidate attachments
                        ],
                        'candidate_group' => [
                            'label' => 'उम्मीदवार समूह', // Candidate group
                        ],
                        'advanced_preferences_section' => [
                            'heading' => 'उन्नत पसंद', // Advanced preferences
                            'description' => 'ये वे उन्नत प्राथमिकताएं हैं जो आप अपने चुनाव के लिए सक्षम कर सकते हैं।', // These are advanced preferences that you can enable for your election.
                        ],
                        'segmented_ballot' => [
                            'helper_text' => 'यह आपको चुनावकर्ता विवरण के आधार पर बैलेट को खंडित करने की अनुमति देगा।', // This will allow you to segment the ballot based on the elector details.
                            'label' => 'विभाजित बैलेट', // Segmented ballot
                        ],
                        'booth_voting_section' => [
                            'heading' => 'बूथ मतदान', // Booth voting
                            'description' => 'यह चुनावकर्ताओं को विशेष स्थान से मतदान करने की अनुमति देगा। यह शारीरिक स्थान में चुनाव आयोजित करने के लिए उपयोगी है।', // This will allow electors to cast votes from a specific location. This is useful for conducting elections in a physical location.
                        ],
                        'booth_voting' => [
                            'label' => 'बूथ मतदान सक्षम करें', // Enable booth voting
                        ],
                    ],
                    'actions' => [
                        'change_plan' => [
                            'label' => 'प्लान बदलें', // Change Plan
                        ],
                        'save' => [
                            'label' => 'प्राथमिकता सहेजें', // Save Preference
                            'success_notification' => [
                                'title' => 'सहेजा गया', // Saved
                            ],
                        ],
                    ],
                    'navigation_label' => 'प्राथमिकता', // Preference
                ],
                'electors' => [
                    'actions' => [
                        'next' => [
                            'label' => 'अगला', // Next
                        ],
                        'send_ballot_link' => [
                            'label' => 'मतपत्र लिंक भेजें', // Send Ballot Link
                            'success_notification' => [
                                'title' => 'मतपत्र लिंक भेजा गया है', // Ballot Link Sent
                                'body' => 'मतपत्र लिंक चुनावकर्ता को भेज दिया गया है।', // The ballot link has been sent to the elector.
                            ],
                        ],
                    ],
                    'bulk_actions' => [
                        'send_ballot_link' => [
                            'label' => 'मतपत्र लिंक भेजें', // Send Ballot Links
                            'success_notification' => [
                                'title' => 'मतपत्र लिंक भेजे गए हैं', // Ballot Links Sent
                                'body' => 'चयनित चुनावकर्ताओं को मतदान नहीं किए गए चुनावकर्ताओं को मतपत्र लिंक भेज दिया गया है।', // Ballot links have been sent to selected electors who have not yet voted.
                            ],
                        ],
                    ],
                    'navigation_label' => 'चुनावकर्ता', // Electors
                ],
                'ballot_setup' => [
                    'infolist' => [
                        'positions' => [
                            'empty_state' => [
                                'heading' => 'अपना मतपत्र सेट करें', // Set up your ballot
                                'description' => 'अपने मतपत्र में पद और उम्मीदवार जोड़ें', // Add positions and candidates to your ballot
                            ],
                            'candidates' => [
                                'empty_state' => [
                                    'heading' => 'कोई उम्मीदवार नहीं', // No candidates
                                    'description' => 'नया उम्मीदवार बनाएं', // Create new candidate
                                ],
                            ],
                        ],
                    ],
                    'actions' => [
                        'next' => [
                            'label' => 'आगे', // Next
                        ],
                        'edit_position' => [
                            'success_notification' => [
                                'title' => 'सहेजा गया', // Saved
                            ],
                        ],
                        'delete_position' => [
                            'success_notification' => [
                                'title' => 'हटा दिया गया', // Deleted
                            ],
                        ],
                        'reorder_candidate' => [
                            'modal_actions' => [
                                'submit' => [
                                    'label' => 'परिवर्तन सहेजें', // Save changes
                                ],
                            ],
                            'modal_heading' => 'क्रमबद्ध करें :label उम्मीदवार', // Reorder :label Candidates
                            'success_notification' => [
                                'title' => 'सहेजा गया', // Saved
                            ],
                        ],
                        'import_candidate' => [
                            'label' => 'आयात', // Import
                        ],
                        'create_candidate' => [
                            'label' => 'नया उम्मीदवार', // New candidate
                            'success_notification' => [
                                'title' => 'बनाया गया', // Created
                            ],
                        ],
                        'edit_candidate' => [
                            'modal_heading' => 'संपादित करें :label', // Edit :label
                            'modal_actions' => [
                                'submit' => [
                                    'label' => 'परिवर्तन सहेजें', // Save changes
                                ],
                            ],
                            'success_notification' => [
                                'title' => 'सहेजा गया', // Saved
                            ],
                        ],
                        'delete_candidate' => [
                            'modal_heading' => 'हटाएँ :label', // Delete :label
                            'success_notification' => [
                                'title' => 'हटा दिया गया', // Deleted
                            ],
                        ],
                    ],
                    'navigation_label' => 'मतपत्र सेटअप', // Ballot Setup
                ],
                'dashboard' => [
                    'navigation_label' => 'डैशबोर्ड', // Dashboard
                ],
                'booth_tokens' => [
                    'navigation_label' => 'बूथ टोकन', // Booth Tokens
                ],
                'collaborators' => [
                    'navigation_label' => 'सहयोगी', // Collaborators
                    'navigation_group' => 'अन्य', // Others
                    'table' => [
                        'name' => [
                            'label' => 'नाम', // Name
                        ],
                        'email' => [
                            'label' => 'ईमेल पता', // Email address
                        ],
                        'actions' => [
                            'detach' => [
                                'label' => 'हटाएं', // Remove
                            ],
                            'set_as_admin' => [
                                'label' => 'व्यवस्थापक के रूप में सेट करें', // Set as admin
                                'success_notification' => [
                                    'title' => 'व्यवस्थापक सफलतापूर्वक बदल गया', // Admin changed successfully
                                ],
                            ],
                            'invite_collaborator' => [
                                'label' => 'सहयोगी को आमंत्रित करें', // Invite collaborator
                                'modal_actions' => [
                                    'submit' => [
                                        'label' => 'आमंत्रित करें', // Invite
                                    ],
                                ],
                                'success_notification' => [
                                    'title' => 'निमंत्रण सफलतापूर्वक भेजा गया', // Invitation sent successfully
                                ],
                            ],
                        ],
                        'heading' => 'सहयोगी', // Collaborators
                    ],
                    'designation' => [
                        'label' => 'पदनाम', // Designation
                    ],
                    'form' => [
                        'designation' => [
                            'label' => 'पदनाम', // Designation
                            'placeholder' => 'उदाहरण: वापसी अधिकारी / चुनाव अधिकारी', // e.g. Returning Officer / Election Officer
                        ],
                        'permissions' => [
                            'label' => 'अनुमतियां', // Permissions
                            'preference' => [
                                'label' => 'प्राथमिकता', // Preference
                            ],
                            'electors' => [
                                'label' => 'निर्वाचक', // Electors
                            ],
                            'ballot_setup' => [
                                'label' => 'बैलट सेटअप', // Ballot setup
                            ],
                            'timing' => [
                                'label' => 'समय', // Timing
                            ],
                            'payment' => [
                                'label' => 'भुगतान', // Payment
                            ],
                            'monitor_tokens' => [
                                'label' => 'मॉनिटर टोकन', // Monitor tokens
                            ],
                            'elector_logs' => [
                                'label' => 'निर्वाचक लॉग्स', // Elector logs
                            ],
                        ],
                        'email' => [
                            'label' => 'ईमेल पता', // Email address
                            'validation' => [
                                'not_in' => 'ईमेल पता पहले से ही एक सहयोगी है।', // The email address is already a collaborator.
                            ],
                        ],
                    ],
                ],
                'monitor_tokens' => [
                    'navigation_label' => 'टोकन मॉनिटर करें', // Monitor Tokens
                    'table' => [
                        'actions' => [
                            'create' => [
                                'label' => 'नया टोकन', // New Token
                                'success_notification' => [
                                    'title' => 'बनाया गया', // Created
                                ],
                            ],
                        ],
                        'key' => [
                            'label' => 'टोकन', // Token
                        ],
                        'activated_at' => [
                            'label' => 'सक्रिय किया गया', // Activated At
                        ],
                        'user_agent' => [
                            'label' => 'उपकरण', // Device
                        ],
                    ],
                ],
                'result' => [
                    'navigation_label' => 'परिणाम', // Result
                ],
                'logs' => [
                    'elector_ballots' => [
                        'navigation_label' => 'मतदान लॉग्स', // Ballot Logs
                        'navigation_group' => 'चुनावकर्ता रिपोर्ट्स', // Elector Reports
                    ],
                    'elector_emails' => [
                        'navigation_label' => 'ईमेल लॉग्स', // Email Logs
                        'navigation_group' => 'चुनावकर्ता रिपोर्ट्स', // Elector Reports
                    ],
                    'elector_sms_messages' => [
                        'navigation_label' => 'SMS संदेश', // SMS Messages
                        'navigation_group' => 'चुनावकर्ता रिपोर्ट्स', // Elector Reports
                    ],
                    'elector_whats_app_messages' => [
                        'navigation_label' => 'व्हाट्सएप लॉग करता है', // WhatsApp Logs
                        'navigation_group' => 'चुनावकर्ता रिपोर्ट्स', // Elector Reports
                    ],
                ],
            ],
            'table' => [
                'code' => [
                    'label' => 'कोड', // Code
                ],
                'name' => [
                    'label' => 'शीर्षक', // Title
                ],
                'status' => [
                    'label' => 'स्थिति', // Status
                ],
            ],
            'actions' => [
                'set_timing' => [
                    'label' => 'समय निर्धारित करें', // Set Timing
                ],
                'edit_timing' => [
                    'label' => 'समय संपादित करें', // Edit Timing
                ],
                'cancel' => [
                    'label' => 'रद्द करें', // Cancel
                    'success_notification' => [
                        'title' => 'रद्द किया गया', // Cancelled
                    ],
                    'modal_actions' => [
                        'submit' => [
                            'label' => 'हाँ', // Yes
                        ],
                        'cancel' => [
                            'label' => 'नहीं', // No
                        ],
                    ],
                ],
                'publish' => [
                    'label' => 'प्रकाशित करें', // Publish
                    'success_notification' => [
                        'title' => 'प्रकाशित', // Published
                    ],
                    'form' => [
                        'notify_electors' => [
                            'label' => 'सभी निर्वाचकों को बैलेट लिंक भेजें', // Send ballot link all electors
                        ],
                    ],
                ],
                'close' => [
                    'pre_close' => [
                        'label' => 'पूर्व-बंद', // Pre-close
                    ],
                    'label' => 'बंद करें', // Close
                    'success_notification' => [
                        'title' => 'बंद किया गया', // Closed
                    ],
                ],
                'generate_result' => [
                    'label' => 'परिणाम उत्पन्न करें', // Generate Result
                    'success_notification' => [
                        'title' => 'परिणाम उत्पन्न', // Result generated
                    ],
                ],
                'edit' => [
                    'label' => 'शीर्षक संपादित करें', // Edit title
                ],
                'replicate' => [
                    'form' => [
                        'replicate_electors' => [
                            'label' => 'निर्वाचकों को शामिल करें', // Include electors
                        ],
                        'replicate_ballot_setup' => [
                            'label' => 'बैलेट सेटअप शामिल करें', // Include ballot setup
                        ],
                    ],
                ],
                'preview' => [
                    'label' => 'पूर्वावलोकन', // Preview
                ],
                'collaborators' => [
                    'label' => 'सहयोगी', // Collaborators
                ],
            ],
        ],
        'elector-resource' => [
            'model_label' => 'निर्वाचक', // Elector
            'plural_model_label' => 'निर्वाचकों', // Electors
            'form' => [
                'email' => [
                    'label' => 'ईमेल पता', // Email address
                ],
                'first_name' => [
                    'label' => 'पहला नाम', // First name
                    'placeholder' => 'पहला नाम', // First name
                ],
                'full_name' => [
                    'label' => 'पूरा नाम', // Full name
                ],
                'groups' => [
                    'label' => 'समूह', // Groups
                ],
                'last_name' => [
                    'label' => 'अंतिम नाम', // Last name
                    'placeholder' => 'अंतिम नाम', // Last name
                ],
                'membership_number' => [
                    'label' => 'सदस्यता संख्या', // Membership number
                ],
                'phone' => [
                    'label' => 'फ़ोन नंबर', // Phone number
                ],
                'title' => [
                    'label' => 'शीर्षक', // Salutation
                    'placeholder' => 'शीर्षक', // Title
                ],
                'segments' => [
                    'label' => 'खंड', // Segments
                    'helper_text' => 'उदाहरण: जीवन सदस्य, गोल्ड सदस्य, सिल्वर सदस्य, आदि।', // e.g. Life Member, Gold Member, Silver Member, etc.
                    'placeholder' => 'खंड चुनें', // Select segments
                    'create_action' => [
                        'heading' => 'खंड बनाएं', // Create Segment
                    ],
                ],
            ],
            'table' => [
                'membership_number' => [
                    'label' => 'सदस्यता संख्या', // Membership number
                ],
                'full_name' => [
                    'label' => 'पूरा नाम', // Full name
                ],
                'phone' => [
                    'label' => 'फ़ोन नंबर', // Phone number
                ],
                'email' => [
                    'label' => 'ईमेल पता', // Email address
                ],
                'weightage' => [
                    'label' => 'महत्व', // Weightage
                ],
                'segments' => [
                    'label' => 'खंड', // Segments
                ],
            ],
        ],
        'candidate-resource' => [
            'form' => [
                'attachments' => [
                    'label' => 'अनुलग्नक', // Attachments
                ],
                'bio' => [
                    'label' => 'जीवनी', // Bio
                ],
                'elector_id' => [
                    'label' => 'निर्वाचक आईडी', // Elector ID
                ],
                'candidate_group_id' => [
                    'label' => 'समूह', // Group
                    'placeholder' => 'एक समूह चुनें', // Choose a group
                    'form' => [
                        'name' => [
                            'label' => 'समूह का नाम', // Group name
                        ],
                        'short_name' => [
                            'label' => 'संक्षिप्त नाम', // Short name
                        ],
                    ],
                ],
                'email' => [
                    'label' => 'ईमेल पता', // Email address
                    'placeholder' => 'ईमेल पता', // Email address
                ],
                'first_name' => [
                    'label' => 'पहला नाम', // First name
                    'placeholder' => 'पहला नाम', // First name
                ],
                'last_name' => [
                    'label' => 'अंतिम नाम', // Last name
                    'placeholder' => 'अंतिम नाम', // Last name
                ],
                'membership_number' => [
                    'label' => 'सदस्यता संख्या', // Membership number
                    'placeholder' => 'सदस्यता संख्या', // Membership number
                    'validation' => [
                        'exists' => 'यह :attribute निर्वाचकों के डेटा में नहीं मिला', // This :attribute is not found in electors data
                    ],
                ],
                'phone' => [
                    'label' => 'फ़ोन नंबर', // Phone number
                ],
                'photo' => [
                    'label' => 'फोटो', // Photo
                    'placeholder' => 'अपनी फोटो खींचें और ड्रॉप करें या <span class="filepond--label-action">ब्राउज़ करें</span>', // Drag & Drop your photo or <span class="filepond--label-action">Browse</span>
                ],
                'position_id' => [
                    'label' => 'पद', // Position
                    'placeholder' => 'एक पद चुनें', // Choose a position
                ],
                'symbol' => [
                    'label' => 'प्रतीक', // Symbol
                    'placeholder' => 'अपने प्रतीक को खींचें और ड्रॉप करें या <span class="filepond--label-action">ब्राउज़ करें</span>', // Drag & Drop your symbol or <span class="filepond--label-action">Browse</span>
                ],
                'title' => [
                    'label' => 'शीर्षक', // Salutation
                    'placeholder' => 'शीर्षक', // Title
                ],
                'logo' => [
                    'label' => 'लोगो', // Logo
                ],
                'full_name' => [
                    'label' => 'पूरा नाम', // Full name
                ],
            ],
        ],
        'organisation-resource' => [
            'form' => [
                'country' => [
                    'label' => 'देश', // Country
                ],
                'logo' => [
                    'label' => 'लोगो', // Logo
                    'placeholder' => 'अपना लोगो खींचें और ड्रॉप करें या <span class="filepond--label-action">ब्राउज़ करें</span>', // Drag & Drop your logo or <span class="filepond--label-action">Browse</span>
                ],
                'name' => [
                    'label' => 'संगठन का नाम', // Organisation name
                ],
                'timezone' => [
                    'label' => 'समय क्षेत्र', // Timezone
                ],
            ],
        ],
        'position-resource' => [
            'form' => [
                'elector_groups' => [
                    'label' => 'योग्य समूह', // Eligible groups
                ],
                'name' => [
                    'label' => 'पद का नाम', // Position name
                    'placeholder' => 'प्रेसिडेंट / सचिव / इसीआदि', // President / Secretary / EC Members etc.,
                ],
                'quota' => [
                    'label' => 'उपलब्ध पद', // Available posts
                ],
                'abstain' => [
                    'label' => 'अनुपस्थिति सक्षम करें', // Enable abstain
                ],
                'threshold' => [
                    'label' => 'न्यूनतम चयन', // Min selection
                ],
                'segments' => [
                    'label' => 'खंड', // Segments
                    'form' => [
                        'name' => [
                            'label' => 'खंड का नाम', // Segment name
                        ],
                    ],
                    'actions' => [
                        'create' => [
                            'heading' => 'खंड बनाएं', // Create Segment
                        ],
                    ],
                ],
            ],
            'table' => [
                'name' => [
                    'label' => 'पद का नाम', // Position name
                ],
                'quota' => [
                    'label' => 'उपलब्ध पद', // Available posts
                ],
                'threshold' => [
                    'label' => 'न्यूनतम चयन', // Min selection
                ],
                'segments' => [
                    'label' => 'खंड', // Segments
                ],
            ],
            'label' => 'पद', // Position
            'plural_label' => 'पद', // Positions
        ],
        'user-resource' => [
            'form' => [
                'name' => [
                    'label' => 'आपका नाम', // Your name
                ],
                'email' => [
                    'label' => 'ईमेल पता', // Email address
                ],
                'password' => [
                    'label' => 'पासवर्ड', // Password
                ],
                'password_confirmation' => [
                    'label' => 'पासवर्ड की पुष्टि करें', // Confirm password
                ],
            ],
        ],
        'pages' => [
            'organisation' => [
                'edit' => [
                    'label' => 'संगठन प्रोफ़ाइल', // Organisation Profile
                ],
                'register' => [
                    'label' => 'संगठन पंजीकरण', // Organisation Registration
                    'form' => [
                        'actions' => [
                            'register' => [
                                'label' => 'सेटअप समाप्त करें', // Complete Setup
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
                    'selected' => 'चयनित', // Selected
                ],
            ],
        ],
    ],
];
