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

namespace App\Operations;

use App\Entity\BankAccount;
use App\Entity\Operation;
use App\Model\ImportOptions;
use App\Repository\OperationRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OperationsImporter
{
    private string $sourceFilesDirectory;
    private OperationRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(string $sourceFilesDirectory, OperationRepository $repository, EntityManagerInterface $em)
    {
        $this->setSourceFilesDirectory($sourceFilesDirectory);
        $this->repository = $repository;
        $this->em = $em;
    }

    public function setSourceFilesDirectory(string $sourceFilesDirectory): void
    {
        $this->sourceFilesDirectory = $sourceFilesDirectory;
    }

    public function importFile(SplFileInfo $file, BankAccount $bankAccount, array $csvColumns = ImportOptions::CSV_COLUMNS, ImportOptions $importOptions = null, bool $flush = true): int
    {
        if (!$importOptions) {
            $importOptions = ImportOptions::create();
        }

        $operations = $this->extractOperationsFromFile($file, $bankAccount, $csvColumns, $importOptions);

        $numberPersisted = 0;

        foreach ($operations as $operation) {
            $this->em->persist($operation);
            $numberPersisted++;
        }

        if ($flush) {
            $this->em->flush();
        }

        return $numberPersisted;
    }

    public function importFromSources(array $csvColumns, BankAccount $account, ImportOptions $importOptions = null): int
    {
        if (!$importOptions) {
            $importOptions = ImportOptions::create();
        }

        $files = (new Finder())->files()->in($this->sourceFilesDirectory)->sortByName();

        $numberPersisted = 0;

        foreach ($files as $file) {
            $numberPersisted += $this->importFile($file, $account, $csvColumns, $importOptions, false);
        }

        $this->em->flush();

        return $numberPersisted;
    }

    /**
     * @return Generator<Operation>
     */
    private function extractOperationsFromFile(SplFileInfo $file, BankAccount $bankAccount, array $csvColumns, ImportOptions $importOptions): Generator
    {
        $filename = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getBasename();

        $month = pathinfo($filename, \PATHINFO_FILENAME);

        $monthDate = DateTimeImmutable::createFromFormat(ImportOptions::FILE_DATE_FORMAT, $month);

        if (false === $monthDate) {
            throw new RuntimeException(sprintf(
                'File date format was expected to be a valid date respecting the "%s" format, "%s" given.',
                ImportOptions::FILE_DATE_FORMAT,
                $month,
            ));
        }

        if ($this->repository->monthIsPopulated($monthDate)) {
            throw new RuntimeException(sprintf('The month %s is already persisted.', $month));
        }

        $h = fopen($file->getPathname(), 'rb+');

        $csvFunctionArguments = $importOptions->getCsvFunctionArguments();

        // Line 1 must be headers or details for you so we ignore it
        fgetcsv($h, ...$csvFunctionArguments);

        while ($line = fgetcsv($h, ...$csvFunctionArguments)) {
            yield Operation::fromImportLine($bankAccount, array_combine($csvColumns, $line));
        }
    }
}
