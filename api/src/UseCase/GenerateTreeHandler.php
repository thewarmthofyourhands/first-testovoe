<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Dto\UseCase\GenerateTreeHandlerInputDto;
use App\Services\CsvParserService;
use App\Services\TreeGeneratorService;
use JsonException;

use function Eva\Common\Functions\json_encode;

class GenerateTreeHandler
{
    public function __construct(
       private readonly CsvParserService $csvParserService,
       private readonly TreeGeneratorService $treeGeneratorService,
    ) {}

    /**
     * @throws JsonException
     */
    public function handle(GenerateTreeHandlerInputDto $generateTreeHandlerInputDto): void
    {
        $inputFile = $generateTreeHandlerInputDto->getInputFile();
        $outputFile = $generateTreeHandlerInputDto->getOutputFile();
        $csvScheme = $this->csvParserService->parseByGenerator($inputFile);
        $tree = $this->treeGeneratorService->generateTree($csvScheme);
        file_put_contents(
            $outputFile,
            json_encode($tree->toArray(), JSON_UNESCAPED_UNICODE),
        );
    }
}
