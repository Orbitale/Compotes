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
    }
}
