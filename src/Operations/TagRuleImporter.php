<?php

declare(strict_types=1);

namespace App\Operations;

use App\Entity\Operation;
use App\Entity\TagRule;
use App\Repository\TagRepository;
use App\Repository\TagRuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class TagRuleImporter
{
    private array $lineHeaders = ['tags', 'pattern', 'regex'];
    private array $csvParams = [0, ';', '"', '\\'];

    private EntityManagerInterface $em;
    private TagRuleRepository $tagRuleRepository;
    private TagRepository $tagRepository;

    public function __construct(TagRuleRepository $tagRuleRepository, TagRepository $tagRepository, EntityManagerInterface $em)
    {
        $this->tagRuleRepository = $tagRuleRepository;
        $this->tagRepository = $tagRepository;
        $this->em = $em;
    }

    public function importFile(\SplFileInfo $sourceFile, array $csvParams = null): int
    {
        $numberPersisted = 0;

        if ('csv' !== $sourceFile->getExtension()) {
            throw new \InvalidArgumentException('Imported file must be a CSV');
        }

        $h = \fopen($sourceFile->getPathname(), 'rb+');

        $headers = \fgetcsv($h, $csvParams ?: $this->csvParams);

        foreach ($operations as $operation) {
            $this->em->persist($operation);
            $numberPersisted++;
        }

        $this->em->flush();

        return $numberPersisted;
    }

    /**
     * @return array<string, array<TagRule>>
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
                throw new \RuntimeException(sprintf('The month %s is already populated.', $month));
            }

            $months[$month] = $lines;
        }

        return $months;
    }

    /**
     * @return iterable<Operation>
     */
    private function extractFromFile(SplFileInfo $file): \Generator
    {
        $h = fopen($file->getPathname(), 'rb+');

        // Line 1 must be headers or details for you so we ignore it
        fgetcsv($h, ...$this->csvParams);

        while ($line = fgetcsv($h, ...$this->csvParams)) {
            yield Operation::fromImportLine(array_combine($this->lineHeaders, $line));
        }
    }
}
