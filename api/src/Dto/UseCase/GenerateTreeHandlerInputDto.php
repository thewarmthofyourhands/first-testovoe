<?php

declare(strict_types=1);

namespace App\Dto\UseCase;

use Eva\Common\DtoTrait;

class GenerateTreeHandlerInputDto
{
    use DtoTrait;

    private readonly string $inputFile;
    private readonly string $outputFile;

    public function getInputFile(): string
    {
        return $this->inputFile;
    }

    public function getOutputFile(): string
    {
        return $this->outputFile;
    }
}
