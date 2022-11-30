<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Enums\NodeTypeEnum;
use App\Models\Node;
use App\Models\Tree;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    public function testMain(): void
    {
        $relationChildItemName = 'Стандарт.#2';
        $relationChildParent = 'Стандарт.#1';
        $relationChildType = 'Изделия и компоненты';
        $relationChildNode = new Node(
            $relationChildItemName,
            $relationChildParent,
            NodeTypeEnum::from($relationChildType),
        );

        $relationItemName = 'Стандарт.#1';
        $relationParent = 'Total';
        $relationType = 'Изделия и компоненты';
        $relationNode = new Node(
            $relationItemName,
            $relationParent,
            NodeTypeEnum::from($relationType),
            [$relationChildNode],
        );

        $childItemName = 'ПВЛ';
        $childParent = 'Total';
        $childType = 'Прямые компоненты';
        $childNode = new Node(
            $childItemName,
            $childParent,
            NodeTypeEnum::from($childType),
            [],
            $relationNode,
        );

        $itemName = 'Total';
        $parent = null;
        $type = 'Изделия и компоненты';
        $node = new Node(
            $itemName,
            $parent,
            NodeTypeEnum::from($type),
            [$childNode],
        );

        $tree = new Tree();
        $tree->addNode($node);
        $this->assertEquals($tree->findNodeByItemName('Total')->getItemName(), $itemName);
        $this->assertEquals($tree->toArray()[0]['children'][0]['children'][0]['itemName'], $relationChildItemName);
        $tree->deleteRootNode([0]);
        $this->assertEquals($tree->toArray(), []);
    }
}
