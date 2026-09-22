<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Command;

use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Digitix\FrameworkBundle\Translation\TranslationExtractor;
use Digitix\FrameworkBundle\Translation\TranslationSynchronizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'dgtx:translation:extract',
    description: 'Finds the translation keys used by the code and registers them in the database',
)]
final class TranslationExtractCommand extends Command
{
    public function __construct(
        private readonly TranslationExtractor $extractor,
        private readonly TranslationSynchronizer $synchronizer,
        private readonly TranslationCompiler $compiler,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Locale(s) to synchronise (default: every active language)')
            ->addOption('no-compile', null, InputOption::VALUE_NONE, 'Do not regenerate the catalogue files afterwards')
            ->setHelp(<<<'HELP'
                Scans the configured directories (project and bundle sources and templates)
                and the digitix configuration, then creates one database entry per new key
                and per locale. Keys that disappeared from the code are flagged obsolete.

                  <info>php %command.full_name%</info>
                  <info>php %command.full_name% --locale=fr_FR --locale=en_US</info>
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Scanning:');
        $io->listing($this->extractor->getPaths());

        /** @var string[] $locales */
        $locales = $input->getOption('locale');
        $report = $this->synchronizer->synchronize($this->extractor->extract(), [] === $locales ? null : $locales);

        $io->table(['Keys', 'Locales', 'Added', 'Seeded', 'Reactivated', 'Obsoleted'], [array_values($report->toArray())]);

        if (!$input->getOption('no-compile')) {
            $files = $this->compiler->compile();
            $io->success(sprintf('%d catalogue file(s) written in %s', \count($files), $this->compiler->getOutputDir()));
        }

        return Command::SUCCESS;
    }
}
