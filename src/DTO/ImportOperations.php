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

namespace App\DTO;

use App\Operations\OperationsImporter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @Assert\Callback(callback="validate")
 */
class ImportOperations
{
    /**
     * @Assert\NotBlank()
     */
    public ?UploadedFile $file = null;

    /**
     * @Assert\Type("array")
     */
    public array $csvColumns = OperationsImporter::CSV_COLUMNS;

    /**
     * @Assert\Length(min="1", max="1")
     */
    private string $csvEscapeCharacter = '\\';

    /**
     * @Assert\Length(min="1", max="1")
     */
    private string $csvDelimiter = '"';

    /**
     * @Assert\Length(min="1", max="1")
     */
    private string $csvSeparator = ';';

    public function validate(ExecutionContextInterface $context): void
    {
        if (!$this->file) {
            return;
        }

        if ('csv' !== $this->file->getClientOriginalExtension()) {
            $context
                ->buildViolation('import_operations.validators.invalid_file_extension')
                ->setTranslationDomain('messages')
                ->addViolation()
            ;
        }

        $originalName = $this->file->getClientOriginalName();

        if (!\preg_match('~^\d{4}-\d{2}\.csv$~i', $originalName)) {
            $context
                ->buildViolation('import_operations.validators.invalid_file_name')
                ->setParameter('{{ file_name }}', $originalName)
                ->setTranslationDomain('messages')
                ->addViolation()
            ;
        }

        $csvColumns = $this->csvColumns;
        $defaultHeaders = OperationsImporter::CSV_COLUMNS;
        sort($csvColumns);
        sort($defaultHeaders);

        if ($csvColumns !== $defaultHeaders) {
            $context
                ->buildViolation('import_operations.validators.invalid_line_headers')
                ->setParameter('{{ headers }}', \implode(', ', $defaultHeaders))
                ->setTranslationDomain('messages')
                ->addViolation()
            ;
        }
    }

    public function setCsvEscapeCharacter(?string $csvEscapeCharacter): void
    {
        $this->csvEscapeCharacter = (string) $csvEscapeCharacter;
    }

    public function setCsvDelimiter(?string $csvDelimiter): void
    {
        $this->csvDelimiter = (string) $csvDelimiter;
    }

    public function setCsvSeparator(?string $csvSeparator): void
    {
        $this->csvSeparator = (string) $csvSeparator;
    }

    public function getCsvEscapeCharacter(): string
    {
        return $this->csvEscapeCharacter;
    }

    public function getCsvDelimiter(): string
    {
        return $this->csvDelimiter;
    }

    public function getCsvSeparator(): string
    {
        return $this->csvSeparator;
    }
}
