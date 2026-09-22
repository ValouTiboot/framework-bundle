<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Command;

use Digitix\FrameworkBundle\Translation\TranslationExchange;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'dgtx:translation:import',
    description: 'Imports a JSON translation export into the database and recompiles the catalogue',
)]
final class TranslationImportCommand extends Command
{
    public function __construct(private readonly TranslationExchange $exchange)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'JSON file produced by dgtx:translation:export or the admin')
            ->addOption('locale', 'l', InputOption::VALUE_REQUIRED, 'Import into this locale instead of the one written in the file')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Replace existing translations (by default only empty ones are filled)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = (string) $input->getArgument('file');

        if (!is_file($file)) {
            $io->error(sprintf('File "%s" not found.', $file));

            return Command::FAILURE;
        }

        try {
            $report = $this->exchange->importJson(
                (string) file_get_contents($file),
                $input->getOption('locale'),
                (bool) $input->getOption('overwrite'),
            );
        } catch (\InvalidArgumentException $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }

        $io->table(['Locale', 'Overwrite', 'Added', 'Updated', 'Unchanged', 'Skipped', 'Invalid'], [[
            $report->locale,
            $report->overwrite ? 'yes' : 'no',
            $report->added,
            $report->updated,
            $report->unchanged,
            $report->skipped,
            $report->invalid,
        ]]);
        $io->success(sprintf('%d entries processed.', $report->total()));

        return Command::SUCCESS;
    }
}
