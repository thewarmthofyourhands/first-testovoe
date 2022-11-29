<?php

declare(strict_types=1);

namespace App\Commands;

use App\Dto\UseCase\GenerateTreeHandlerInputDto;
use App\UseCase\GenerateTreeHandler;
use Eva\Console\ArgvInput;
use JsonException;
use RuntimeException;
use function App\Functions\memoryPeakUsage;

class GenerateJsonTreeCommand
{
    public function __construct(
        private readonly string $projectDir,
        private readonly GenerateTreeHandler $generateTreeHandler,
    ) {}

    /**
     * @throws JsonException
     */
    public function execute(ArgvInput $argvInput): void
    {
        if (false === isset($argvInput->getOptions()['input-file'])) {
            throw new RuntimeException('Option input-file is required');
        }

        $inputFile = $this->projectDir . $argvInput->getOptions()['input-file'];
        $outputFile = $argvInput->getOptions()['output-file'] ?? '/task/output.json';
        $outputFile =  $this->projectDir . $outputFile;
        $generateTreeHandlerInputDto = new GenerateTreeHandlerInputDto([
            'inputFile' => $inputFile,
            'outputFile' => $outputFile,
        ]);
        $this->generateTreeHandler->handle($generateTreeHandlerInputDto);
        $memoryPeakUsage = memoryPeakUsage();
        $executeTime = microtime(true) - START_TIME;


        echo <<<EOF
        Tree generated successful in $outputFile
        Memory peak usage: $memoryPeakUsage
        Execute time: $executeTime
        
        EOF;
    }
}
