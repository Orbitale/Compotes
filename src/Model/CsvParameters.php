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

namespace App\Model;

class CsvParameters
{
    private string $separator;
    private string $delimiter;
    private string $escapeCharacter;

    private function __construct()
    {
    }

    public static function create(string $separator = ';', string $delimiter = '"', string $escapeCharacter = '\\'): self
    {
        $self = new self();

        $self->separator = $separator;
        $self->delimiter = $delimiter;
        $self->escapeCharacter = $escapeCharacter;

        return $self;
    }

    public function getCsvFunctionArguments(): array
    {
        return [0, $this->separator, $this->delimiter, $this->escapeCharacter];
    }
}
