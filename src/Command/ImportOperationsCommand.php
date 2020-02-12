<?php

declare(strict_types=1);

/*
 * This file is part of the Compotes package.
 *
 * (c) Alex "Pierstoval" Rock <pierstoval@gmail.com>.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Model\ImportOptions;
use App\Operations\OperationsImporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportOperationsCommand extends Command
{
    protected static $defaultName = 'operations:import';

    private OperationsImporter $writer;
    private string $sourcesDir;

    public function __construct(OperationsImporter $writer, string $sourceFilesDirectory)
    {
        $this->writer = $writer;
        $this->sourcesDir = $sourceFilesDirectory;
        parent::__construct(self::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Import operations from the "account_exports" directory.')
            ->setHelp(<<<HELP
            Take files from the <info>{$this->sourcesDir}</info> directory by default.

            You can customize the directory by adding it as an argument:

            <comment>bin/console operations:import my_other_exports_dir/</comment>

            Files format must be CSV.
            Files names must be <info>{year}-{month}.csv</info>.
            First line of the file will be ignored, so it can contain headers or an empty line.
            Each <info>line column</info> must be the following:
            * <info>date</info> in <info>day/month/year</info> format (nothing else supported for now).
            * <info>operation_type</info> (given by bank provider, can be an empty string).
            * <info>display_type</info> (not mandatory, can be an empty string).
            * <info>operation_details</info> (can be a longer string).
            * <info>amount</info> of the operation. Can be a negative number.
              Note that this can be a string like <info>1 234,55</info> or <info>1,234.55</info>.
              Every non-digit character (except <info>+</info> and <info>-;</info>) will
              be removed and all remaining numbers will make the amount <info>in cents</info>.

            HELP)
            ->addArgument('sources-directory', InputArgument::OPTIONAL, 'Customize the source directory where to find the CSV files.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($sourcesDir = $input->getArgument('sources-directory')) {
            $this->writer->setSourceFilesDirectory($sourcesDir);
        }

        $persistedCount = $this->writer->importFromSources(ImportOptions::CSV_COLUMNS);

        if ($persistedCount) {
            $io->success(\sprintf('Wrote %d new operations!', $persistedCount));
        } else {
            $io->warning('Nothing new to persist.');
        }

        return 0;
    }
}
