<?php

declare(strict_types=1);

namespace App\Command;

use App\Operations\OperationsWriter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WriteOperationsCommand extends Command
{
    protected static $defaultName = 'operations:import';

    private $writer;

    public function __construct(OperationsWriter $writer)
    {
        parent::__construct(self::$defaultName);
        $this->writer = $writer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $persistedCount = $this->writer->write();

        if ($persistedCount) {
            $io->success(sprintf('Wrote %d new operations!', $persistedCount));
        } else {
            $io->comment('Nothing new to persist.');
        }

        return 0;
    }
}
