<style>
    .xl
    {
        color: #fff;
    }
</style>

<?php

return [
    'seo' => [
        'title' => 'Kudvo - Secure and Efficient Online Voting System',
        'description' => 'Empower your organization with a secure online voting system. Simplify board elections, event planning, and more. Check out our products and pricing today!',
    ],

    'content' => [
        'headline' => 'Secure and Efficient eVote Solutions for Your Online Election System',
        'hero' => [
            'items' => [
                [
                    'title' => 'eVote Made Simple. Strengthening Democracy with Kudvo',
                    'description' => 'Increase voter turnout, enhance security, and streamline your election process with our innovative platform.',
                    'image' => asset('img/home/hero/online-voting-system.webp'),
                    'image_alt' => ' A computer and a voting machine are displayed against a backdrop of various icons representing technology and democracy.',
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Explore',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Effortless Online Voting for Clubs: Secure and Reliable eVote Solutions',
                    'description' => 'Streamline your decision-making process with our trusted system.',
                    'image' => asset('img/home/hero/club.webp'),
                    'image_alt' => 'Collage of sports and leisure activities including golf, tennis, soccer, and a marina with boats, showcasing a vibrant club environment.',
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Explore',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Revolutionizing Homeowners Association Governance with Online Elections',
                    'description' => 'Manage condominium community decisions',
                    'image' => asset('img/home/hero/hoa-home-owner-asspciation-or-condominium-associations.webp'),
                    'image_alt' => 'Aerial view of a suburban neighborhood with well-maintained houses and greenery, representing a homeowner association community.',
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Explore',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Simplify Corporate Decisions with Kudvo’s Secure Online Resolution Voting System',
                    'description' => 'offers a convenient solution for board meetings, corporate resolutions.',
                    'image' => asset('img/home/hero/corparate-industry.webp'),
                    'image_alt' => 'Industrial cityscape with a large chemical plant in the center, surrounded by skyscrapers and green spaces.',
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Explore',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Boost Engagement in Associations & Unions through Online Voting',
                    'description' => 'Foster stronger engagement among your association or union members.',
                    'image' => asset('img/home/hero/associations-and-unions.webp'),
                    'image_alt' => 'Diverse group of people connected by lines and circles, representing a network or collaboration.',
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Explore',
                    'cta2_url' => route('products.election.home'),
                ],
                [
                    
                ],
                [
                    'title' => 'Empower Employee Associations with Seamless Online Voting Platforms',
                    'description' => 'Empower your workforce with a voice in organizational decisions.',
                    'image' => asset('img/home/hero/employer-associations.webp'),
                    'image_alt' => 'The image shows a diverse group of professionals smiling. They are dressed in different work clothes, including business suits and safety gear, indicating various professions.',
                    'cta_label' => 'Get Started',
                    'cta_url' => route('filament.user.auth.register'),
                    'cta2_label' => 'Explore',
                    'cta2_url' => route('products.election.home'),
                ],
            ],
        ],
        'features' => [
            'title' => 'Explore Our Features',
            'items' => [
                [
                    'title' => 'Secure and Convenient Ballot Access',
                    'image' => asset('img/home/features/ballot-link-delivery.webp'),
                    'image_alt' => 'A person in a green shirt looks at a smartphone with a message: "your Ballot link is https://kudvo.com/b/SRThs to vote for elections" alongside SMS and email icons.',
                    'points' => [
                        'Simplify the voting process with quick and efficient access to the ballot.',
                        'Offer unique links or a common access point for eligible voters.',
                        'Ensure convenience and reliability for voters.',
                    ],
                ],
                [
                    'title' => 'Enhanced Security with Multi-Factor Authentication',
                    'image' => asset('img/home/features/multi-factor-authentication-code-delivery.webp'),
                    'image_alt' => 'Screenshot of an acknowledgment from Kudvo confirming Mr. Vikram T vote for iNodesys on Feb 21, 2024, at 05:02 PM (IST). The document serves as a ballot copy.',
                    'points' => [
                        'Prioritize the integrity of the voting process with enhanced security measures.',
                        'Implement multi-factor authentication (MFA) codes for voter verification.',
                        'Protect against fake or unauthorized votes, safeguarding the validity of election results.',
                    ],
                ],
                [
                    'title' => 'Transparent Ballot Acknowledgement',
                    'image' => asset('img/home/features/ballot-acknowledgement.webp'),
                    'image_alt' => 'An image showing a verification message with the code "715363" sent by "iNodesys" for OTP verification.',
                    'points' => [
                        'Provide voters with confirmation of their ballots to ensure transparency.',
                        'Verify the authenticity of votes and enhance voter satisfaction with the process.',
                        'Offer additional support and resources to voters, enhancing their overall experience.',
                    ],
                ],
                [
                    'title' => 'Advanced Security Preferences',
                    'image' => asset('img/home/features/do-not-track-vote.webp'),
                    'image_alt' => 'A man in a suit and face mask stands at a podium with a laptop, next to graphics emphasizing voter privacy and security.',
                    'points' => [
                        'Customize security preferences to track and prevent duplicate devices.',
                        'Implement advanced tracking and prevention features to protect against voting fraud.',
                        'Ensure the integrity of elections with robust security measures in place.',
                    ],
                ],
                [
                    'title' => 'Comprehensive Election Management',
                    'image' => asset('img/home/features/elector-update-after-publish.webp'),
                    'image_alt' => 'A woman in business attire smiles while holding a laptop. A shaded panel behind her displays options for allowing elector updates with a \'Publish\' button below.',
                    'points' => [
                        'Keep elector details updated even after the election has been published.',
                        'Ensure the accuracy of voter information and election results.',
                        'Provide a seamless and reliable voting experience for all participants.',
                    ],
                ],
                [
                    'title' => 'Segmented Ballot for Enhanced Efficiency',
                    'image' => asset('img/home/features/segmented-voting-system.webp'),
                    'image_alt' => 'Illustration of a tree with blue circular nodes connected by lines. Some nodes contain smaller orange and white circles. The background is a light teal color.',
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
                    'title_color' => '#008F78',
                    'cta_label' => 'Pricing & more details',
                    'cta_url' => route('products.election.home'),
                ],
                [
                    'title' => 'Resolution Voting',
                    'description' => 'Make informed decisions on crucial matters with Kudvo\'s Resolution Voting feature. Propose, debate, and vote on resolutions effectively to ensure clarity and transparency.',
                    'title_color' => '#DD6400',
                    'cta_label' => 'Pricing & more details',
                    'cta_url' => route('products.meeting.home'),
                ],
                [
                    'title' => 'Survey',
                    'description' => 'Gather valuable insights and feedback from stakeholders with Kudvo\'s Survey tool. Conduct surveys on organizational performance, member satisfaction, and more to drive informed decision-making.',
                    'title_color' => '#21A300',
                    'cta_label' => 'Free Survey',
                    'cta_url' => route('products.survey.home'),
                ],
                [
                    'title' => 'AGM Meeting Voting',
                    'description' => 'Streamline Annual General Meetings (AGMs) with Kudvo\'s AGM Meeting Voting solution. Enable remote participation and voting for attendees while maintaining the integrity of the process.',
                    'title_color' => '#E92E66',
                    'cta_label' => 'Coming soon...',
                    'cta_url' => null,
                ],
                [
                    'title' => 'Live Polling',
                    'description' => 'Engage your audience in real-time with Kudvo\'s Live Polling feature. Conduct interactive polls during events, webinars, or meetings to gather feedback and enhance audience interaction.',
                    'title_color' => '#4285F7',
                    'cta_label' => 'Coming soon...',
                    'cta_url' => null,
                ],
                [
                    'title' => 'Meeting Voting',
                    'description' => 'Make meetings more productive and inclusive with Kudvo\'s Meeting Voting functionality. Enable attendees to vote on agenda items, proposals, or decisions, ensuring every voice is heard.',
                    'title_color' => '#8C5A85',
                    'cta_label' => 'Coming soon...',
                    'cta_url' => null,
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
