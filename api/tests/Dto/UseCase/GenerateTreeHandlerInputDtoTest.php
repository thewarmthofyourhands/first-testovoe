<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Dto\UseCase\GenerateTreeHandlerInputDto;
use PHPUnit\Framework\TestCase;

class GenerateTreeHandlerInputDtoTest extends TestCase
{
    public function testMain(): void
    {
        $inputFile = '/code/tasks/input.csv';
        $outputFile = '/code/tasks/output.json';
        $dto = new GenerateTreeHandlerInputDto([
            'inputFile' => $inputFile,
            'outputFile' => $outputFile,
        ]);
        $this->assertEquals($dto->getInputFile(), $inputFile);
        $this->assertEquals($dto->getOutputFile(), $outputFile);
    }
}
