<?php

declare(strict_types=1);

namespace App\UseCase;

use App\Dto\UseCase\GenerateTreeHandlerInputDto;
use App\Services\CsvParserService;
use App\Services\TreeGeneratorService;
use Eva\Filesystem\Filesystem;
use JsonException;

use function Eva\Common\Functions\json_encode;

class GenerateTreeHandler
{
    public function __construct(
        private readonly CsvParserService $csvParserService,
        private readonly TreeGeneratorService $treeGeneratorService,
        private readonly Filesystem $filesystem = new Filesystem(),
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
        $this->filesystem->filePutContents($outputFile, json_encode($tree->toArray(), JSON_UNESCAPED_UNICODE));
    }
}
