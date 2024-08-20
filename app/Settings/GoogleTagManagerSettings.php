<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GoogleTagManagerSettings extends Settings
{
    public ?string $google_tag_manager_id;

    public static function group(): string
    {
        return 'site';
    }

    public function getGoogleTagManagerId(): ?string
    {
        return $this->google_tag_manager_id;
    }

    public function getHeadScript(): string
    {
        if (blank($gtmId = $this->getGoogleTagManagerId())) {
            return '';
        }

        return <<<HTML
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'}); const f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!=='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','$gtmId');</script>
<!-- End Google Tag Manager -->
HTML;
    }

    public function getBodyScript(): string
    {
        if (blank($gtmId = $this->getGoogleTagManagerId())) {
            return '';
        }

        return <<<HTML
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=$gtmId"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
HTML;

    }
}
