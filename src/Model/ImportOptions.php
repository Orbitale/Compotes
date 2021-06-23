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

class ImportOptions
{
    public const FILE_DATE_FORMAT = 'Y-m';

    public const OPERATION_DATE_FORMAT = 'd/m/Y H:i:s O';

    public const CSV_COLUMNS = ['date', 'type', 'type_display', 'details', 'amount'];

    public const CSV_DELIMITERS = [
        '"' => '"',
        "'" => "'",
        '' => '',
    ];

    public const CSV_SEPARATORS = [
        ';' => ';',
        ',' => ',',
    ];

    public const CSV_ESCAPE_CHARACTERS = [
        '\\' => '\\',
        '' => '',
    ];

    private string $separator;
    private string $delimiter;
    private string $escapeCharacter;

    private function __construct()
    {
    }

    public static function create(string $separator = null, string $delimiter = null, string $escapeCharacter = null): self
    {
        $self = new self();

        $self->separator = $separator ?? array_key_first(self::CSV_SEPARATORS);
        $self->delimiter = $delimiter ?? array_key_first(self::CSV_DELIMITERS);
        $self->escapeCharacter = $escapeCharacter ?? array_key_first(self::CSV_SEPARATORS);

        return $self;
    }

    public function getCsvFunctionArguments(): array
    {
        return [0, $this->separator, $this->delimiter, $this->escapeCharacter];
    }
}
