<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Dto\Services\CsvRowDto;
use App\Dto\UseCase\GenerateTreeHandlerInputDto;
use App\Enums\NodeTypeEnum;
use App\Models\Node;
use App\Models\Tree;
use App\Services\CsvParserService;
use App\Services\TreeGeneratorService;
use App\UseCase\GenerateTreeHandler;
use Eva\Filesystem\Filesystem;
use Generator;
use PHPUnit\Framework\TestCase;

use function Eva\Common\Functions\json_encode;

class GenerateTreeHandlerTest extends TestCase
{
    private function mockParsedGenerator(CsvRowDto $dto): Generator
    {
        $parsedCsv = [$dto];

        foreach ($parsedCsv as $parsedCsvLine) {
            yield $parsedCsvLine;
        }
    }

    public function testMain(): void
    {
        $dto = new CsvRowDto([
            "itemName" => "Total",
            "type" => "Изделия и компоненты",
            "parent" => null,
            "relation" => ''
        ]);
        $inputFile = '/code/tasks/input.csv';
        $outputFile = '/code/tasks/output.json';

        $tree = new Tree();

        $tree->addNode(
            new Node(
                $dto->getItemName(),
                $dto->getParent(),
                NodeTypeEnum::DIRECT_COMPONENTS,
            ),
        );

        $csvParserService = $this->createMock(CsvParserService::class);
        $csvParserService
            ->method('parseByGenerator')
            ->willReturn($this->mockParsedGenerator($dto));
        $treeGeneratorService = $this
            ->createMock(TreeGeneratorService::class);
        $treeGeneratorService
            ->method('generateTree')
            ->willReturn($tree);
        $filesystem = $this
            ->createMock(Filesystem::class);
        $filesystem
            ->method('filePutContents')
            ->willReturnCallback(function (string $argOutputFile, string $argTree) use ($outputFile, $tree) {
                self::assertEquals($argOutputFile, $outputFile);
                self::assertEquals($argTree, json_encode($tree->toArray(), JSON_UNESCAPED_UNICODE));

                return 656;
            });

        $dto = new GenerateTreeHandlerInputDto([
            'inputFile' => $inputFile,
            'outputFile' => $outputFile,
        ]);
        $handler = new GenerateTreeHandler($csvParserService, $treeGeneratorService, $filesystem);
        $handler->handle($dto);
    }
}
