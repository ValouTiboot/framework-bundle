<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Finder;

/**
 * Scans PHP controllers, Twig templates and the bundle YAML for translation
 * keys, so the translation editor can list what needs translating.
 */
final class TranslationFinder
{
    public function __construct(private readonly string $projectDir)
    {
    }

    /**
     * Files under $dir whose name starts with $type, recursively.
     * $dir is relative to the project directory unless it is an existing absolute path.
     *
     * @return string[]
     */
    public function findFiles(string $dir, string $type = '', bool $root = true): array
    {
        if ($root && !is_dir($dir)) {
            $dir = $this->projectDir.'/'.ltrim(str_replace($this->projectDir, '', $dir), '/');
        }

        $dir = rtrim($dir, '/').'/';
        $files = [];

        foreach (glob($dir.$type.'*', \GLOB_MARK) ?: [] as $path) {
            if (str_ends_with($path, '/')) {
                $files = array_merge($files, $this->findFiles($path, $type, false));
            } else {
                $files[] = $path;
            }
        }

        return $files;
    }

    /**
     * Keys used through "trans('key', [], 'Domain')" in PHP files.
     *
     * @return array<string, array<string, string>> domain => key => ''
     */
    public function searchInController(string $dir, string $type = '', string $pattern = '.*'): array
    {
        return $this->searchInFiles($this->findFiles($dir, $type), '@trans[(](.*),(?:[\s])(?:.*),(?:[\s])\'('.$pattern.')\'[)]@');
    }

    /**
     * Keys used through "'key'|trans({}, 'Domain')" in Twig templates.
     *
     * @return array<string, array<string, string>> domain => key => ''
     */
    public function searchInTemplate(string $dir, string $type = ''): array
    {
        return $this->searchInFiles($this->findFiles($dir, $type), '@\'([0-9A-za-z\s?!.,\'\"_\%-)(]+)\'(?:[|]{1})trans[(][{](?:.*)[}],(?:[\s])\'(.*)\'[)]@m');
    }

    /**
     * "label.*" and "help.*" keys declared in the digitix YAML files.
     *
     * @return array<string, array<string, string>> domain => key => ''
     */
    public function searchInConfig(string $dir, string $type = '', string $domain = 'Admin.Fields.Label'): array
    {
        $keys = [];

        foreach ($this->findFiles($dir, $type) as $file) {
            preg_match_all('@((label|help)\.[a-zA-Z._]+)@', (string) file_get_contents($file), $matches);

            foreach ($matches[1] as $key) {
                $keys[$domain][trim(str_replace("'", '', $key))] = '';
            }
        }

        return $keys;
    }

    /**
     * @param string[] $files
     *
     * @return array<string, array<string, string>> domain => key => ''
     */
    private function searchInFiles(array $files, string $regex): array
    {
        $keys = [];

        foreach ($files as $file) {
            preg_match_all($regex, (string) file_get_contents($file), $matches);

            foreach ($matches[1] as $index => $key) {
                $keys[$matches[2][$index]][trim(str_replace("'", '', $key))] = '';
            }
        }

        return $keys;
    }
}
