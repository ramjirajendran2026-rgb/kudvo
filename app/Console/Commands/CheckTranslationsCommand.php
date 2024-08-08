<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class CheckTranslationsCommand extends Command
{
    protected $signature = 'translations:check
                            {locales* : The locales to check.}';

    protected $description = 'Check for missing and removed translations';

    public function handle(): int
    {
        $this->scan();

        return self::SUCCESS;
    }

    protected function scan(): void
    {
        $localeRootDirectory = lang_path();

        $filesystem = app(Filesystem::class);

        if (! $filesystem->exists($localeRootDirectory)) {
            return;
        }

        collect($filesystem->directories($localeRootDirectory))
            ->mapWithKeys(static fn (string $directory): array => [$directory => (string) str($directory)->afterLast(DIRECTORY_SEPARATOR)])
            ->when(
                $locales = $this->argument('locales'),
                fn (Collection $availableLocales): Collection => $availableLocales->filter(fn (string $locale): bool => in_array($locale, $locales))
            )
            ->each(function (string $locale, string $localeDir) use ($filesystem, $localeRootDirectory) {
                $files = $filesystem->allFiles($localeDir);
                $baseFiles = $filesystem->allFiles(implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en']));

                $localeFiles = collect($files)->map(fn ($file) => (string) str($file->getPathname())->after(DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR));
                $englishFiles = collect($baseFiles)->map(fn ($file) => (string) str($file->getPathname())->after(DIRECTORY_SEPARATOR . 'en' . DIRECTORY_SEPARATOR));
                $missingFiles = $englishFiles->diff($localeFiles);
                $removedFiles = $localeFiles->diff($englishFiles);
                $path = implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, $locale]);

                if ($missingFiles->count() > 0 && $removedFiles->count() > 0) {
                    $this->warn("[!] {$missingFiles->count()} missing translation " . Str::plural('file', $missingFiles->count()) . " and {$removedFiles->count()} removed translation " . Str::plural('file', $missingFiles->count()) . ' for ' . 'en' . ".\n");

                    $this->newLine();
                } elseif ($missingFiles->count() > 0) {
                    $this->warn("[!] {$missingFiles->count()} missing translation " . Str::plural('file', $missingFiles->count()) . ' for ' . 'en' . ".\n");

                    $this->newLine();
                } elseif ($removedFiles->count() > 0) {
                    $this->warn("[!] {$removedFiles->count()} removed translation " . Str::plural('file', $removedFiles->count()) . ' for ' . 'en' . ".\n");

                    $this->newLine();
                }

                if ($missingFiles->count() > 0 || $removedFiles->count() > 0) {
                    $this->table(
                        [$path, ''],
                        array_merge(
                            array_map(fn (string $file): array => [$file, 'Missing'], $missingFiles->toArray()),
                            array_map(fn (string $file): array => [$file, 'Removed'], $removedFiles->toArray()),
                        ),
                    );

                    $this->newLine();
                }

                collect($files)
                    ->reject(function ($file) use ($localeRootDirectory) {
                        return ! file_exists(implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en', $file->getRelativePathname()]));
                    })
                    ->mapWithKeys(function (SplFileInfo $file) use ($localeDir, $localeRootDirectory) {
                        $expectedKeys = require implode(DIRECTORY_SEPARATOR, [$localeRootDirectory, 'en', $file->getRelativePathname()]);
                        $actualKeys = require $file->getPathname();

                        return [
                            (string) str($file->getPathname())->after("{$localeDir}/") => [
                                'missing' => array_keys(array_diff_key(
                                    Arr::dot($expectedKeys),
                                    Arr::dot($actualKeys)
                                )),
                                'removed' => array_keys(array_diff_key(
                                    Arr::dot($actualKeys),
                                    Arr::dot($expectedKeys)
                                )),
                            ],
                        ];
                    })
                    ->tap(function (Collection $files) use ($locale) {
                        $missingKeysCount = $files->sum(fn ($file): int => count($file['missing']));
                        $removedKeysCount = $files->sum(fn ($file): int => count($file['removed']));

                        $locale = 'en';

                        if ($missingKeysCount == 0 && $removedKeysCount == 0) {
                            $this->info("[✓] No missing or removed translation keys for {$locale}!\n");

                            $this->newLine();
                        } elseif ($missingKeysCount > 0 && $removedKeysCount > 0) {
                            $this->warn("[!] {$missingKeysCount} missing translation " . Str::plural('key', $missingKeysCount) . " and {$removedKeysCount} removed translation " . Str::plural('key', $removedKeysCount) . " for {$locale}.\n");
                        } elseif ($missingKeysCount > 0) {
                            $this->warn("[!] {$missingKeysCount} missing translation " . Str::plural('key', $missingKeysCount) . " for {$locale}.\n");
                        } elseif ($removedKeysCount > 0) {
                            $this->warn("[!] {$removedKeysCount} removed translation " . Str::plural('key', $removedKeysCount) . " for {$locale}.\n");
                        }
                    })
                    ->filter(static fn ($keys): bool => count($keys['missing']) || count($keys['removed']))
                    ->each(function ($keys, string $file) {
                        $this->table(
                            [$file, ''],
                            [
                                ...array_map(fn (string $key): array => [$key, 'Missing'], $keys['missing']),
                                ...array_map(fn (string $key): array => [$key, 'Removed'], $keys['removed']),
                            ],
                            'box',
                        );

                        $this->newLine();
                    });
            });
    }
}
