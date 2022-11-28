<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\TreeGeneratorService;
use Eva\Console\ArgvInput;
use RuntimeException;

class GenerateJsonTreeCommand
{
    public function __construct(
        private readonly string $projectDir,
        private readonly TreeGeneratorService $treeGeneratorService,
    ) {}

    public function execute(ArgvInput $argvInput): void
    {
        if (false === isset($argvInput->getOptions()['input-file'])) {
            throw new RuntimeException('Option input-file is required');
        }

        $inputFile = $this->projectDir . $argvInput->getOptions()['input-file'];
        $outputFile = $argvInput->getOptions()['output-file'] ?? '/task/output.json';
        $outputFile =  $this->projectDir . $outputFile;
        $this->treeGeneratorService->generateJsonTree($inputFile, $outputFile);

        echo <<<EOF
        Tree generated successful in $outputFile
        
        EOF;
    }
}
