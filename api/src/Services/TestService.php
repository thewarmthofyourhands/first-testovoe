<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Node;
use JsonException;

class TestService
{
    public function __construct(
        private readonly CsvParserService $csvParserService,
    ) {}

    private function memoryUsage(): string
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
        ini_set('memory_limit', '800m');
        $csvScheme = $this->csvParserService->parse($inputFile);
//        var_dump($this->memoryUsage());exit();
        $nodeList = $this->generateTree($csvScheme);

//        var_dump(count($nodeList));exit();
        foreach ($nodeList as $key => $node) {
            $nodeList[$key] = $node->toArray();
        }
//
//        var_dump($this->memoryUsage());
        file_put_contents($outputFile, \Eva\Common\Functions\json_encode($nodeList, JSON_UNESCAPED_UNICODE));
    }

    private function glueStacks(array &$stackList): array
    {
        foreach ($stackList as $stackStartKey => $stackStart) {
            foreach ($stackList as $stackEndKey => $stackEnd) {
                if (current($stackStart)['parent'] === end($stackEnd)['itemName']) {
                    array_push($stackList[$stackEndKey], ...$stackStart);
                    unset($stackList[$stackStartKey]);

                    return $this->glueStacks($stackList);
                }
            }
        }

        return $stackList;
    }

    /**
     * @param array $scheme
     * @param array $stackList
     * @param Node[] $parentList
     * @return void
     */
    private function tt(array &$scheme, array $parentList): bool
    {
        $newParentList = [];

        if ($scheme === []) {
            return true;
        }

        $somethingFound = false;

        foreach ($scheme as $key => $el) {
            $itemName = $el['Item Name'];
            $parent = $el['Parent'];

            foreach ($parentList as $parentNode) {
                if ($parent === $parentNode->getItemName()) {
                    $somethingFound = true;
                    $newNode = new Node($itemName, $parent, []);
                    $parentNode->addChildren($newNode);
                    $newParentList[] = $newNode;
//                    unset($scheme[$key]);

                    break;
                }
            }
        }

        if (false === $somethingFound) {
            return true;
        }

        return $this->tt($scheme, $newParentList);
    }

    private function bb(array &$scheme, array $parentList): array
    {
        $newParentList = [];

        foreach ($scheme as $key => $el) {
            $itemName = $el['Item Name'];
            $parent = $el['Parent'];

            foreach ($parentList as $parentNode) {
                if ($parent === $parentNode) {
                    $newNode = new Node($itemName, $parent, []);
                    $newParentList[] = $newNode;
//                    unset($scheme[$key]);

                    break;
                }
            }
        }

        return $newParentList;
    }

    private function generateTree(array $scheme): array
    {
//        $scheme = [
//            ['item3', 'item2'],
//            ['item1', null],
//            ['item4','item3'],
//            ['item2', 'item1'],
//        ];

        $rootList = $this->bb($scheme, ['']);
        $this->tt($scheme, $rootList);
//        var_dump($rootList[0]);exit();
        return $rootList;

        $stackList = [];

        foreach ($scheme as $el) {
            $itemName = $el['Item Name'];
            $parent = $el['Parent'];
            $newVersion = ['itemName' => $itemName, 'parent' => $parent];
        }

        foreach ($scheme as $el) {
//            $itemName = $el[0];
//            $parent = $el[1];
            $itemName = $el['Item Name'];
            $parent = $el['Parent'];
            $newVersion = ['itemName' => $itemName, 'parent' => $parent];

            if ($stackList === []) {
                $stackList[] = [$newVersion];
                continue;
            }

            $somethingDid = false;

            foreach ($stackList as $stackKey => $stack) {
                if (current($stack)['parent'] === $itemName) {
                    array_unshift($stackList[$stackKey], $newVersion);
                    $somethingDid = true;

                    break;
                }

                if (end($stack)['itemName'] === $parent) {
                    $stackList[$stackKey][] = $newVersion;
                    $somethingDid = true;

                    break;
                }

                foreach ($stack as $parentNodeKey => $parentNode) {
                    if ($parentNode['itemName'] === $parent) {
                        array_splice($stackList[$stackKey], $parentNodeKey+1, 0, [$newVersion]);
                        $somethingDid = true;

                        break 2;
                    }
                }
            }

            if (false === $somethingDid) {
                $stackList[] = [$newVersion];
            }
        }

//        foreach ($stackList as $stackStartKey => $stackStart) {
//            foreach ($stackList as $stackEndKey => $stackEnd) {
//                if (current($stackStart)['parent'] === end($stackEnd)['itemName']) {
//                    array_push($stackList[$stackEndKey], ...$stackStart);
//                    unset($stackList[$stackStartKey]);
//                }
//            }
//        }
        return $this->glueStacks($stackList);
//        var_dump($stackList);
//        exit;
        $list = [];
        $waitingForParent = [];

        foreach ($scheme as $row)
        {
            $parent = $row['Parent'];
            $itemName = $row['Item Name'];
            $children = [];
            if (isset($waitingForParent[$itemName])) {
                $children = $waitingForParent[$itemName];
                unset($waitingForParent[$itemName]);
            }

            $row['children'] = $children;

            if ($parent === '') {
                $list[$itemName] = $row;
            } else {
                if (isset($list[$parent])) {
                    $list[$parent]['children'] = $row;
                } else {
                    $waitingForParent[$itemName] = $row;
                }
            }
        }

        return $list;
//        return $this->findRootNodes($scheme);
    }

    private function findRootNodes(array $scheme): array
    {
        $nodeList = [];
        foreach ($scheme as $row) {
            if ($row['Parent'] === '') {
                $children = $this->findNodesByParentName($scheme, $row['Item Name']);
                $nodeList[] = [
                    $row['Item Name'],
                    $row['Parent'],
                    [],
                ];
            }
        }

        return $nodeList;
    }

    private function findNodesByParentName(array $scheme, string $parentName): array
    {
        $nodeList = [];

        foreach ($scheme as $row) {
            if ($row['Parent'] === $parentName) {
                $name = $row['Item Name'];
                $children = $this->findNodesByParentName($scheme, $name);
                $nodeList[] = [
                    $name,
                    $parentName,
                    $children,
                ];
            }
        }

        return $nodeList;
    }

    private function findByParent(array $list, string $parentName)
    {
//        foreach ($list )
    }
}
