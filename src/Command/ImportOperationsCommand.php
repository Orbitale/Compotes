<?php

declare(strict_types=1);

namespace App\Command;

use App\Operations\OperationsImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportOperationsCommand extends Command
{
    protected static $defaultName = 'operations:import';

    private OperationsImporter $writer;

    public function __construct(OperationsImporter $writer)
    {
        parent::__construct(self::$defaultName);
        $this->writer = $writer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $persistedCount = $this->writer->import();

        if ($persistedCount) {
            $io->success(sprintf('Wrote %d new operations!', $persistedCount));
        } else {
            $io->comment('Nothing new to persist.');
        }

        return 0;
    }
}
