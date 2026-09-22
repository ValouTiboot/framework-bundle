<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Translation;

use Digitix\FrameworkBundle\Entity\Language;
use Digitix\FrameworkBundle\Entity\Translation;
use Digitix\FrameworkBundle\Provider\LanguageProvider;
use Digitix\FrameworkBundle\Repository\TranslationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * Aligns the database with the keys found in the code, for every active
 * language:
 *
 *  - a new key gets one entry per locale, pre-filled with the value the
 *    translator already knows for it (bundle defaults, legacy translation
 *    files of the project) when there is one;
 *  - an entry whose key is no longer in the code is flagged obsolete, its
 *    value is kept;
 *  - an obsolete entry whose key is back becomes active again.
 *
 * Nothing is ever deleted.
 */
final class TranslationSynchronizer
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TranslationRepository $repository,
        private readonly LanguageProvider $languages,
        private readonly TranslationExtractor $extractor,
        private readonly TranslatorBagInterface $translator,
    ) {
    }

    /**
     * @param MessageCatalogue|null $keys    keys to synchronise, extracted from the code when null
     * @param string[]|null         $locales defaults to the active languages
     */
    public function synchronize(?MessageCatalogue $keys = null, ?array $locales = null): SyncReport
    {
        $keys ??= $this->extractor->extract();
        $locales ??= array_map(
            static fn (Language $language) => (string) $language->getLocale(),
            $this->languages->getActiveLanguages()
        );

        $report = new SyncReport();
        $report->keys = TranslationExtractor::countKeys($keys);
        $report->locales = array_values(array_unique($locales));

        foreach ($report->locales as $locale) {
            $this->synchronizeLocale($keys, $locale, $report);
        }

        $this->entityManager->flush();

        return $report;
    }

    private function synchronizeLocale(MessageCatalogue $keys, string $locale, SyncReport $report): void
    {
        $existing = $this->repository->findIndexedByLocale($locale);
        $seeds = $this->translator->getCatalogue($locale);
        $seen = [];

        foreach ($keys->getDomains() as $domain) {
            foreach (array_keys($keys->all($domain)) as $key) {
                $key = (string) $key;
                $index = TranslationRepository::index($domain, $key);
                $seen[$index] = true;

                if (isset($existing[$index])) {
                    if ($existing[$index]->isObsolete()) {
                        $existing[$index]->markActive();
                        ++$report->reactivated;
                    }

                    continue;
                }

                $translation = Translation::create($domain, $key, $locale);

                if ($seeds->defines($key, $domain)) {
                    $translation->setValue($seeds->get($key, $domain));
                    ++$report->seeded;
                }

                $this->entityManager->persist($translation);
                ++$report->added;
            }
        }

        foreach ($existing as $index => $translation) {
            if (!isset($seen[$index]) && !$translation->isObsolete()) {
                $translation->markObsolete();
                ++$report->obsoleted;
            }
        }
    }
}
