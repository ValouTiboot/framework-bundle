<?php

namespace Digitix\FrameworkBundle\Updater;

use Symfony\Component\Translation\Util\ArrayConverter;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Dumper\PhpFileDumper;
use Symfony\Component\Translation\Writer\TranslationWriterInterface;

final class TranslationUpdater
{
	private $writer;
	private $translations = [];
	private $domain;

	public function __construct(TranslationWriterInterface $writer, $projectDir)
	{
		$this->writer = $writer;
		$this->translationDir = $projectDir.'/translations/';
	}

	public function prepare($oldTranslations, $form)
	{
		$data = $form->getData();
		$this->domain = str_replace('_', '.', $form->getName());

		foreach ($oldTranslations[$this->domain] as $key => $value) {
			if (isset($data[md5($key)])) {
				$this->translations[$key] = $data[md5($key)];
			}
		}

		return true;
	}

	public function write($locale = 'fr_FR')
	{
		$messages = ArrayConverter::expandToTree($this->translations);
		$catalog = new MessageCatalogue($locale);
		$catalog->add($messages, $this->domain);

		$this->writer->addDumper('php', (new PhpFileDumper()));
		$this->writer->write($catalog, 'php', ['path' => $this->translationDir.$catalog->getLocale()]);
	}
}
