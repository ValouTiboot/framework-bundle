<?php

namespace Digitix\FrameworkBundle\Finder;

final class TranslationFinder
{
	private $projectDir;

	public function __construct($projectDir)
	{
		$this->projectDir = $projectDir;
	}

	public function findFiles($dir, $type = '', $root = true): array
	{
		$dir = str_replace($this->projectDir, '', $dir);

		if ($root) {
			$dir = $this->projectDir.$dir;
		}

		$files = [];
		if (empty($dir)) {
			return [];
		}

		$tmpFiles = glob($dir.$type.'*', GLOB_MARK);

		if (count($tmpFiles) && $tmpFiles !== false) {
			foreach ($tmpFiles as $f) {
				if (substr($f, -1) == '/') {
					$files = array_merge($files, $this->findFiles($f, $type, false));
				} else {
					$files[] = $f;
				}
			}
		}

		return $files;
	}

	public function searchInController($dir, $type = '', $pattern = '.*'): array
	{
		$files = $this->findFiles($dir, $type);
		return $this->searchInFiles($files, '@trans[(](.*),(?:[\s])(?:.*),(?:[\s])\'('.$pattern.')\'[)]@');
	}

	public function searchInTemplate($dir, $type = ''): array
	{
		$files = $this->findFiles($dir, $type);
		return $this->searchInFiles($files, '@\'([0-9A-za-z\s?!.,\'\"_\%-)(]+)\'(?:[|]{1})trans[(][{](?:.*)[}],(?:[\s])\'(.*)\'[)]@m');
	}

	public function searchInConfig($dir, $type = '', $domain = 'Admin.Fields.Label'): array
	{
		$files = $this->findFiles($dir, $type);
		$matches = $this->searchInYaml($files, '@((label|help)\.[a-zA-Z._]+)@', $domain);
		// $choices = $this->searchInYaml($files, '@(?:label|help):(?:[\s])(.*)@');

		return $matches;
	}

	private function searchInYaml(array $files, string $regex, $domain): array
	{
		$trads = [];
		foreach ($files as $file) {
			$content = file_get_contents($file);
			preg_match_all($regex, $content, $matches);

			if (count($matches[0])) {
				for ($i=0; $i < count($matches[0]) ; $i++) {
					$key = trim(str_replace("'", '', $matches[1][$i]));
					$trads[$domain][$key] = '';
				}
			}
		}

		return $trads;
	}

	private function searchInFiles(array $files, string $regex): array
	{
		$trads = [];
		foreach ($files as $file) {
			$content = file_get_contents($file);
			preg_match_all($regex, $content, $matches);

			if (count($matches[0])) {
				for ($i=0; $i < count($matches[0]) ; $i++) {
					$key = trim(str_replace("'", '', $matches[1][$i]));
					$trads[$matches[2][$i]][$key] = '';
				}
			}
		}

		return $trads;
	}
}
