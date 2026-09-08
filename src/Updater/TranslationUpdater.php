<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Updater;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Util\ArrayConverter;
use Symfony\Component\Translation\Writer\TranslationWriterInterface;

/**
 * Writes the translations submitted by the translation editor into
 * "translations/<locale>/<Domain>.<locale>.php".
 */
final class TranslationUpdater
{
    private readonly string $translationDir;

    /** @var array<string, string> key => translated value */
    private array $translations = [];

    private string $domain = 'messages';

    public function __construct(
        private readonly TranslationWriterInterface $writer,
        string $projectDir,
    ) {
        $this->translationDir = $projectDir.'/translations/';
    }

    /**
     * Keeps, from the submitted form, the values of the keys known for the
     * form's domain (form children are named after the md5 of the key).
     *
     * @param array<string, array<string, string>> $oldTranslations domain => key => value
     * @param FormInterface<mixed>                 $form
     */
    public function prepare(array $oldTranslations, FormInterface $form): void
    {
        $data = (array) $form->getData();
        $this->domain = str_replace('_', '.', $form->getName());
        $this->translations = [];

        foreach ($oldTranslations[$this->domain] ?? [] as $key => $value) {
            if (isset($data[md5($key)])) {
                $this->translations[$key] = (string) $data[md5($key)];
            }
        }
    }

    public function write(string $locale = 'fr_FR'): void
    {
        $catalogue = new MessageCatalogue($locale);
        $catalogue->add(ArrayConverter::expandToTree($this->translations), $this->domain);

        // the "php" dumper is registered on the framework's translation writer
        $this->writer->write($catalogue, 'php', ['path' => $this->translationDir.$locale]);
    }
}
