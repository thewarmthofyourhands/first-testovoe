<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\NodeTypeEnum;
use App\Models\Node;
use App\Models\TestNode;
use App\Models\Tree;
use JsonException;

class StackVersionService
{
    public function __construct(
        private readonly CsvParserService $csvParserService,
    ) {}

    static public function memoryUsage(): string
    {
        $size = memory_get_peak_usage(true);
        $unit=array('b','kb','mb','gb','tb','pb');

        return @round($size/ (1024 ** ($i = floor(log($size, 1024)))),2).' '.$unit[$i];
    }

    /**
     * @throws JsonException
     */
    public function generateJsonTree(string $inputFile, string $outputFile): void
    {
        $csvScheme = $this->csvParserService->parse($inputFile);
        $tree = $this->generateTree($csvScheme);
        file_put_contents($outputFile, \Eva\Common\Functions\json_encode($tree->toArray(), JSON_UNESCAPED_UNICODE));
    }

    private function generateTree(array $scheme): Tree
    {
        $tree = new Tree();
        $bufferRelationSeeker = [];
//        $stackList = [];

        foreach ($scheme as $el) {
            $newNode = new TestNode(
                $el['Item Name'],
                $el['Parent'] === '' ? null : $el['Parent'],
                NodeTypeEnum::from($el['Type']),
            );

            if ($newNode->getType() === NodeTypeEnum::DIRECT_COMPONENTS) {
                $relation = $tree->findNodeByItemName($el['Relation']);
                if (null === $relation) {
                    $bufferRelationSeeker[$el['Relation']][] = $newNode;
                } else {
                    $newNode->setRelation($relation);
                }
            }

            if ($newNode->getType() === NodeTypeEnum::PRODUCTS_AND_COMPONENTS) {
                if (true === isset($bufferRelationSeeker[$newNode->getItemName()])) {
                    foreach ($bufferRelationSeeker[$newNode->getItemName()] as $item) {
                        $item->setRelation($newNode);
                    }
                    unset($bufferRelationSeeker[$newNode->getItemName()]);
                }
            }

            //если мы потомок
            $parentNode = $tree->findNodeByItemName($newNode->getParent() ?? '');
            if (null !== $parentNode) {
                $parentNode->addChildren($newNode);
                continue;
            }

            if ($rootNodeList = $tree->findRootNodeListByParent($newNode->getItemName())) {
                $newNode->addChildrenList($rootNodeList);
                $tree->deleteRootNode(array_keys($rootNodeList));
                continue;
            }

            $tree->addNode($newNode);

        }

        return $tree;
//exit;
//            foreach ($stackList as $stackKey => $stack) {
//                if (current($stack)['parent'] === $itemName) {
//                    array_unshift($stackList[$stackKey], $newVersion);
//                    $somethingDid = true;
//
//                    break;
//                }
//
//                if (end($stack)['itemName'] === $parent) {
//                    $stackList[$stackKey][] = $newVersion;
//                    $somethingDid = true;
//
//                    break;
//                }
//
//                foreach ($stack as $parentNodeKey => $parentNode) {
//                    if ($parentNode['itemName'] === $parent) {
//                        array_splice($stackList[$stackKey], $parentNodeKey+1, 0, [$newVersion]);
//                        $somethingDid = true;
//
//                        break 2;
//                    }
//                }
//            }
//
//            if (false === $somethingDid) {
//                $stackList[] = [$newVersion];
//            }
//        }
//
//        return $this->glueStacks($stackList);
    }
}
