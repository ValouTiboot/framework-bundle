<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Asset;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * Cache busting without a build manifest: "?v=<file mtime>" is appended to
 * the URL of every asset that exists in the public directory, so a
 * recompiled theme.js is fetched again by browsers that cached the old one.
 *
 *   framework:
 *     assets:
 *       version_strategy: Digitix\FrameworkBundle\Asset\MtimeVersionStrategy
 */
final class MtimeVersionStrategy implements VersionStrategyInterface
{
    /** @var array<string, string> */
    private array $versions = [];

    public function __construct(private readonly string $publicDir)
    {
    }

    public function getVersion(string $path): string
    {
        if (!isset($this->versions[$path])) {
            $file = $this->publicDir.'/'.ltrim((string) strtok($path, '?'), '/');
            $mtime = is_file($file) ? filemtime($file) : false;

            $this->versions[$path] = false === $mtime ? '' : (string) $mtime;
        }

        return $this->versions[$path];
    }

    public function applyVersion(string $path): string
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        return $path.(str_contains($path, '?') ? '&' : '?').'v='.$version;
    }
}
