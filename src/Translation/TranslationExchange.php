<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Export and import of the translations of one locale, as JSON, to carry
 * them from one environment to another (a client translates on staging,
 * the file is imported in production).
 *
 * {
 *   "format": "digitix-translations/1",
 *   "locale": "fr_FR",
 *   "exported_at": "2026-09-09T10:00:00+00:00",
 *   "entries": [{"domain": "Admin.List.Default", "key": "list.default.add", "value": "Ajouter"}, ...]
 * }
 *
 * Obsolete entries are not exported. Imported keys unknown to the code are
 * created anyway; the next extraction flags them obsolete if they never show up.
 */
final class TranslationExchange
{
    public const FORMAT = 'digitix-translations/1';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslationRepository $repository,
        private readonly TranslationCompiler $compiler,
    ) {
    }

    /**
     * @return array{format: string, locale: string, exported_at: string, entries: list<array{domain: string, key: string, value: string|null}>}
     */
    public function export(string $locale): array
    {
        $entries = [];

        foreach ($this->repository->findBy(['locale' => $locale], ['domain' => 'ASC', 'key' => 'ASC']) as $translation) {
            if ($translation->isObsolete()) {
                continue;
            }

            $entries[] = [
                'domain' => (string) $translation->getDomain(),
                'key' => (string) $translation->getKey(),
                'value' => $translation->getValue(),
            ];
        }

        return [
            'format' => self::FORMAT,
            'locale' => $locale,
            'exported_at' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
            'entries' => $entries,
        ];
    }

    public function exportToJson(string $locale): string
    {
        return (string) json_encode($this->export($locale), \JSON_PRETTY_PRINT | \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param array<string, mixed> $data      decoded JSON
     * @param string|null          $locale    target locale, defaults to the one written in the file
     * @param bool                 $overwrite replace values that already exist (otherwise only empty ones are filled)
     *
     * @throws \InvalidArgumentException when the file is not a translation export
     */
    public function import(array $data, ?string $locale = null, bool $overwrite = false): TranslationImportReport
    {
        if (!\is_string($data['format'] ?? null) || !str_starts_with($data['format'], 'digitix-translations/')) {
            throw new \InvalidArgumentException('This file is not a Digitix translation export.');
        }

        if (!\is_array($data['entries'] ?? null)) {
            throw new \InvalidArgumentException('The file has no "entries" list.');
        }

        $locale ??= (string) ($data['locale'] ?? '');
        if ('' === $locale) {
            throw new \InvalidArgumentException('The file does not say which locale it holds.');
        }

        $report = new TranslationImportReport($locale, $overwrite);
        $existing = $this->repository->findIndexedByLocale($locale);

        foreach ($data['entries'] as $entry) {
            $domain = \is_array($entry) ? trim((string) ($entry['domain'] ?? '')) : '';
            $key = \is_array($entry) ? (string) ($entry['key'] ?? '') : '';
            $value = \is_array($entry) && \is_scalar($entry['value'] ?? null) ? trim((string) $entry['value']) : null;

            if ('' === $domain || '' === $key) {
                ++$report->invalid;
                continue;
            }

            $index = TranslationRepository::index($domain, $key);
            $translation = $existing[$index] ?? null;

            if (null === $translation) {
                if (null === $value || '' === $value) {
                    ++$report->skipped; // nothing to bring for a key we do not know
                    continue;
                }

                $translation = Translation::create($domain, $key, $locale)->setValue($value);
                $this->entityManager->persist($translation);
                $existing[$index] = $translation;
                ++$report->added;
                continue;
            }

            if (null === $value || '' === $value) {
                ++$report->skipped; // an empty value never erases a translation
                continue;
            }

            if ($translation->getValue() === $value) {
                ++$report->unchanged;
                continue;
            }

            if ($translation->isTranslated() && !$overwrite) {
                ++$report->skipped;
                continue;
            }

            $translation->setValue($value);
            ++$report->updated;
        }

        $this->entityManager->flush();

        if ($report->added > 0 || $report->updated > 0) {
            $this->compiler->compile($locale);
        }

        return $report;
    }

    /**
     * @throws \InvalidArgumentException when the content is not valid JSON
     */
    public function importJson(string $json, ?string $locale = null, bool $overwrite = false): TranslationImportReport
    {
        $data = json_decode($json, true);

        if (!\is_array($data)) {
            throw new \InvalidArgumentException('The file is not valid JSON.');
        }

        return $this->import($data, $locale, $overwrite);
    }
}
