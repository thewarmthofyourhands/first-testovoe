<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Node;
use JsonException;

class TreeGeneratorService
{
    public function __construct(
        private readonly CsvParserService $csvParserService,
    ) {}

    private function memoryUsage()
    {
        $size = memory_get_usage(true);
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/ (1024 ** ($i = floor(log($size, 1024)))),2).' '.$unit[$i];
    }

    /**
     * @throws JsonException
     */
    public function generateJsonTree(string $inputFile, string $outputFile): void
    {
        ini_set('memory_limit', '100m');
        $csvScheme = $this->csvParserService->parse($inputFile);
        $nodeList = $this->generateTree($csvScheme);

        foreach ($nodeList as $key => $node) {
            $nodeList[$key] = $node->toArray();
        }

        var_dump($this->memoryUsage());
        file_put_contents($outputFile, \Eva\Common\Functions\json_encode($nodeList, JSON_UNESCAPED_UNICODE));
    }

    private function generateTree(array $scheme): array
    {
        $rootNodeList = $this->findRootNodes($scheme);


        return $rootNodeList;
    }

    private function findNodesByParentName(array $scheme, string $parentName): array
    {
        $nodeList = [];
        foreach ($scheme as $row) {
            if ($row['Parent'] === $parentName) {
                $name = $row['Item Name'];
                $children = $this->findNodesByParentName($scheme, $name);
//                if ($row['Type'] === 'Прямые компоненты') {
//                    $relation = $this->findByName($scheme, $row['Relation'])->getChildren();
//                    array_push($children, ...$relation);
//                }
                $nodeList[] = new Node(
                    $name,
                    $parentName,
                    $children,
                );
            }
        }

        return $nodeList;
    }

    private function findByName(array $scheme, string $name): Node
    {
        $node = null;

        foreach ($scheme as $row) {
            if ($row['Item Name'] === $name) {
                $parentName = $row['Parent'];
                $children = $this->findNodesByParentName($scheme, $name);
                if ($row['Type'] === 'Прямые компоненты') {
                    $relation = $this->findByName($scheme, $row['Relation'])->getChildren();
                    array_push($children, ...$relation);
                }
                $node = new Node(
                    $name,
                    $parentName,
                    $children,
                );
            }
        }

        if (null === $node) {
            throw new \RuntimeException('Node with that name is not exist');
        }

        return $node;
    }

    private function findRootNodes(array $scheme): array
    {
        $nodeList = [];
        foreach ($scheme as $row) {
            if ($row['Parent'] === '') {
                $children = $this->findNodesByParentName($scheme, $row['Item Name']);
//                if ($row['Type'] === 'Прямые компоненты') {
//                    $relation = $this->findByName($scheme, $row['Relation'])->getChildren();
//                    array_push($children, ...$relation);
//                }

                $nodeList[] = new Node(
                    $row['Item Name'],
                    $row['Parent'],
                    [],
                );
            }
        }

        return $nodeList;
    }
}
