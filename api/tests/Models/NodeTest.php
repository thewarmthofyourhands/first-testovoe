<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Enums\NodeTypeEnum;
use App\Models\Node;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
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
        );

        $this->assertEquals($node->getItemName(), $itemName);
        $this->assertEquals($node->getParent(), $parent);
        $this->assertEquals($node->getType(), NodeTypeEnum::PRODUCTS_AND_COMPONENTS);
        $this->assertEquals($node->getRelation(), null);
        $this->assertEquals($node->getChildren(), []);

        $node->addChildren($childNode);

        $this->assertEquals($node->getChildren()[0]->getItemName(), $childItemName);
        $this->assertEquals($node->findNodeByName($childItemName)->getItemName(), $childItemName);

        $this->assertEquals($node->toArray()['children'][0]['itemName'], $childItemName);
        $this->assertEquals($node->toArray()['children'][0]['children'][0]['itemName'], $relationChildItemName);
    }
}
