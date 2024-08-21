<?php

namespace App\Console\Commands;

use App\Actions\GenerateSitemap;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate sitemap';

    public function handle(): int
    {
        GenerateSitemap::execute();

        return self::SUCCESS;
    }
}
