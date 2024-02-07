<?php

namespace App\Enums;

enum WebAppManifestDisplay: string
{
    case Fullscreen = 'fullscreen';

    case Standalone = 'standalone';

    case MinimalUi = 'minimal-ui';

    case Browser = 'browser';
}
