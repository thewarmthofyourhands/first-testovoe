<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Services\CsvRowDto;
use App\Enums\NodeTypeEnum;
use App\Models\Node;
use App\Models\Tree;
use Generator;

class TreeGeneratorService
{
    public function generateTree(Generator|array $csvScheme): Tree
    {
        $tree = new Tree();
        //Буфер для связей
        $bufferRelationSeeker = [];

        foreach ($csvScheme as $csvRowDto) {
            assert($csvRowDto instanceof CsvRowDto);
            $newNode = new Node(
                $csvRowDto->getItemName(),
                $csvRowDto->getParent(),
                NodeTypeEnum::from($csvRowDto->getType()),
            );
            $this->fillRelation($tree, $newNode, $csvRowDto, $bufferRelationSeeker);

            //если мы потомок
            if ($newNode->getParent()) {
                $parentNode = $tree->findNodeByItemName($newNode->getParent());

                if (null !== $parentNode) {
                    $parentNode->addChildren($newNode);
                    continue;
                }
            }

            //Если мы родитель
            if ($rootNodeList = $tree->findRootNodeListByParent($newNode->getItemName())) {
                $newNode->addChildrenList($rootNodeList);
                $tree->deleteRootNode(array_keys($rootNodeList));
                continue;
            }

            //Если пока ничего не нашли, считаемся рутовым узлом
            $tree->addNode($newNode);
        }

        return $tree;
    }

    private function fillRelation(Tree $tree, Node $newNode, CsvRowDto $csvRowDto, array &$bufferRelationSeeker): void
    {
        if ($newNode->getType() === NodeTypeEnum::DIRECT_COMPONENTS) {
            $relation = $tree->findNodeByItemName($csvRowDto->getRelation());
            $newNode->setRelation($relation);

            //Если пока в дереве нет relation, добавляем в буффер
            if (null === $relation) {
                $bufferRelationSeeker[$csvRowDto->getRelation()][] = $newNode;
            }
        }

        //Если запись есть в буффере, то связываем
        if (
            $newNode->getType() === NodeTypeEnum::PRODUCTS_AND_COMPONENTS &&
            true === isset($bufferRelationSeeker[$newNode->getItemName()])
        ) {
            foreach ($bufferRelationSeeker[$newNode->getItemName()] as $item) {
                $item->setRelation($newNode);
            }

            unset($bufferRelationSeeker[$newNode->getItemName()]);
        }
    }
}
