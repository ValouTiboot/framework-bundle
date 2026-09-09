<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Command;

use Digitix\FrameworkBundle\Translation\TranslationExchange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'dgtx:translation:export',
    description: 'Exports the translations of a locale as JSON, to import them on another environment',
)]
final class TranslationExportCommand extends Command
{
    public function __construct(private readonly TranslationExchange $exchange)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('locale', InputArgument::REQUIRED, 'Locale to export (fr_FR)')
            ->addArgument('file', InputArgument::OPTIONAL, 'Target file (default: standard output)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $json = $this->exchange->exportToJson((string) $input->getArgument('locale'));
        $file = $input->getArgument('file');

        if (null === $file) {
            $output->writeln($json);

            return Command::SUCCESS;
        }

        (new Filesystem())->dumpFile((string) $file, $json."\n");
        (new SymfonyStyle($input, $output))->success(sprintf('Translations written to %s', $file));

        return Command::SUCCESS;
    }
}
