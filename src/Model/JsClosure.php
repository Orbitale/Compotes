<?php

namespace App\Model;

class JsClosure implements \JsonSerializable
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