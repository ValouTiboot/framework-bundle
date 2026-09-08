<?php

declare(strict_types=1);

namespace Digitix\FrameworkBundle\Command;

use Digitix\FrameworkBundle\Translation\TranslationCompiler;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'dgtx:translation:compile',
    description: 'Writes the translations stored in the database as catalogue files and refreshes the translator cache',
)]
final class TranslationCompileCommand extends Command
{
    public function __construct(private readonly TranslationCompiler $compiler)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('locale', InputArgument::OPTIONAL, 'Only this locale (default: every locale)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        /** @var string|null $locale */
        $locale = $input->getArgument('locale');
        $files = $this->compiler->compile($locale);

        $io->listing($files);
        $io->success(sprintf('%d catalogue file(s) written in %s', \count($files), $this->compiler->getOutputDir()));

        return Command::SUCCESS;
    }
}
