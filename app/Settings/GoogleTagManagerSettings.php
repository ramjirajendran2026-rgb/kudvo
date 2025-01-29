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
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=$gtmId"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '$gtmId');
</script>
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
