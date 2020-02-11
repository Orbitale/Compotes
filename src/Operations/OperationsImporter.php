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

use App\Entity\Operation;
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
    /**
     * File format MUST be "Y-m.csv".
     */
    public const FILE_DATE_FORMAT = 'Y-m';

    public const DEFAULT_LINE_HEADERS = ['date', 'type', 'type_display', 'details', 'amount'];

    public const DEFAULT_CSV_PARAMS = [0, ';', '"', '\\'];

    private string $bankSourcesDir;
    private OperationRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(string $bankSourcesDir, OperationRepository $repository, EntityManagerInterface $em)
    {
        $this->bankSourcesDir = $bankSourcesDir;
        $this->repository = $repository;
        $this->em = $em;
    }

    public function importFile(SplFileInfo $file, array $lineHeaders = self::DEFAULT_LINE_HEADERS, bool $flush = true): int
    {
        $operations = $this->extractOperationsFromFile($file, $lineHeaders);

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

    public function importFromSources(array $lineHeaders): int
    {
        $files = (new Finder())->files()->in($this->bankSourcesDir)->sortByName();

        $numberPersisted = 0;

        foreach ($files as $file) {
            $numberPersisted += $this->importFile($file, $lineHeaders, false);
        }

        $this->em->flush();

        return $numberPersisted;
    }

    /**
     * @return Generator<Operation>
     */
    private function extractOperationsFromFile(SplFileInfo $file, array $lineHeaders): Generator
    {
        $filename = $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getBasename();

        $month = \pathinfo($filename, \PATHINFO_FILENAME);

        $monthDate = DateTimeImmutable::createFromFormat(self::FILE_DATE_FORMAT, $month);

        if (false === $monthDate) {
            throw new RuntimeException(\sprintf(
                'File date format was expected to be a valid date respecting the "%s" format, "%s" given.',
                self::FILE_DATE_FORMAT,
                $month,
            ));
        }

        if ($this->repository->monthIsPopulated($monthDate)) {
            throw new RuntimeException(\sprintf('The month %s is already persisted.', $month));
        }

        $h = \fopen($file->getPathname(), 'rb+');

        // Line 1 must be headers or details for you so we ignore it
        \fgetcsv($h, ...self::DEFAULT_CSV_PARAMS);

        while ($line = \fgetcsv($h, ...self::DEFAULT_CSV_PARAMS)) {
            yield Operation::fromImportLine(\array_combine($lineHeaders, $line));
        }
    }
}
