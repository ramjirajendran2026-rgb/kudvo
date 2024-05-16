<?php

return [

    'title' => 'अपने ईमेल पते की पुष्टि करें',

    'heading' => 'अपने ईमेल पते की पुष्टि करें',

    'actions' => [

        'resend_notification' => [
            'label' => 'इसे फिर से भेजें',
        ],

    ],

    'messages' => [
        'notification_not_received' => 'हमारे द्वारा भेजा गया ईमेल प्राप्त नहीं हुआ?',
        'notification_sent' => 'हमने :email पर एक ईमेल भेजा है, जिसमें आपके ईमेल पते की पुष्टि करने के निर्देश हैं।',
    ],

    'notifications' => [

        'notification_resent' => [
            'title' => 'हमने ईमेल पर नाराजगी व्यक्त की है।',
        ],

        'notification_resend_throttled' => [
            'title' => 'पुनः भेजने के बहुत सारे प्रयास',
            'body' => 'कृपया :सेकंड सेकंड में पुनः प्रयास करें।',
        ],

    ],

];
