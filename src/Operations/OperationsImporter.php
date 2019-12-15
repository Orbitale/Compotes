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
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class OperationsImporter
{
    /**
     * File format MUST be "Y-m.csv".
     */
    private const FILE_DATE_FORMAT = 'Y-m';

    private const DEFAULT_LINE_HEADERS = ['date', 'type', 'type_display', 'details', 'amount'];

    private array $lineHeaders = self::DEFAULT_LINE_HEADERS;

    private array $csvParams = [0, ';', '"', '\\'];

    private string $bankSourcesDir;
    private OperationRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(string $bankSourcesDir, OperationRepository $repository, EntityManagerInterface $em)
    {
        $this->bankSourcesDir = $bankSourcesDir;
        $this->repository = $repository;
        $this->em = $em;
    }

    public function import(array $lineHeaders = null): int
    {
        $previousHeaders = null;
        if ($lineHeaders) {
            $previousHeaders = $this->lineHeaders;
            $this->lineHeaders = $lineHeaders;
        }

        $months = $this->getMonthsData();

        $numberPersisted = 0;

        foreach ($months as $month => $operations) {
            $monthDate = DateTimeImmutable::createFromFormat(self::FILE_DATE_FORMAT, $month);

            if ($this->repository->monthIsPopulated($monthDate)) {
                continue;
            }

            foreach ($operations as $operation) {
                $this->em->persist($operation);
                $numberPersisted++;
            }
        }

        $this->em->flush();

        if ($previousHeaders) {
            $this->lineHeaders = $previousHeaders;
        }

        return $numberPersisted;
    }

    /**
     * @return array<string, array<Operation>>
     */
    private function getMonthsData(): array
    {
        $files = (new Finder())->files()->in($this->bankSourcesDir)->sortByName();

        $months = [];

        foreach ($files as $file) {
            /** @var SplFileInfo $file */
            $data = $this->extractFromFile($file);

            $lines = [];

            foreach ($data as $line) {
                $lines[] = $line;
            }

            $month = $file->getFilenameWithoutExtension();

            if (isset($months[$month])) {
                throw new RuntimeException(\sprintf('The month %s is already populated.', $month));
            }

            $months[$month] = $lines;
        }

        return $months;
    }

    /**
     * @return Generator<Operation>
     */
    private function extractFromFile(SplFileInfo $file): Generator
    {
        $h = \fopen($file->getPathname(), 'rb+');

        // Line 1 must be headers or details for you so we ignore it
        \fgetcsv($h, ...$this->csvParams);

        while ($line = \fgetcsv($h, ...$this->csvParams)) {
            yield Operation::fromImportLine(\array_combine($this->lineHeaders, $line));
        }
    }
}
