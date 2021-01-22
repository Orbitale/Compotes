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

use JsonSerializable;

class JsClosure implements JsonSerializable
{
    public const TAG_START = '!!closure_';
    public const TAG_END = '_erusolc!!';

    private string $functionBody;

    public function __construct(string $functionBody)
    {
        $this->functionBody = $functionBody;
    }

    public function jsonSerialize(): string
    {
        return self::TAG_START.$this->functionBody.self::TAG_END;
    }
}
