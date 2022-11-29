<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NodeTypeEnum;
use App\Services\StackVersionService;

class TestNode
{
    public null|array $arrayFormat = null;

    public function __construct(
        private readonly string $itemName,
        private readonly null|string $parent,
        private readonly NodeTypeEnum $type,
        private array $children = [],
        private null|TestNode $relation = null,
    ) {}

//    public function generateArrayFormat(): void
//    {
//
//    }

    public function toArray(): array
    {
        if (null !== $this->arrayFormat) {
            return $this->arrayFormat;
        }

        $children = [];

        foreach ($this->getChildren() as $child) {
            if (null === $child->arrayFormat) {
                $child->toArray();
            }
            $children[] = &$child->arrayFormat;
        }

        if (null !== $this->getRelation()) {
            $relationChildren = $this->getRelation()->getChildren();
            foreach ($relationChildren as $relationChild) {
                if ($relationChild->arrayFormat === null) {
                    $relationChild->toArray();
                }
//                $children[] = &$relationChild->arrayFormat;
                $children[] = [
                    'itemName' => &$relationChild->arrayFormat['itemName'],
                    'parent' => $this->getItemName(),
                    'children' => &$relationChild->arrayFormat['children'],
                ];
            }
        }

        $this->arrayFormat = [
            'itemName' => $this->getItemName(),
            'parent' => $this->getParent(),
            'children' => $children,
        ];

        return $this->arrayFormat;
    }

    public function getType(): NodeTypeEnum
    {
        return $this->type;
    }

    public function getRelation(): null|TestNode
    {
        return $this->relation;
    }

    public function setRelation(null|TestNode $relation): void
    {
        $this->relation = $relation;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getParent(): null|string
    {
        return $this->parent;
    }

    public function findNodeByName(string $nodeName): null|TestNode
    {
        if ($this->itemName === $nodeName) {
            return $this;
        }

        if ($this->getChildren() === []) {
            return null;
        }

        foreach ($this->getChildren() as $child) {
            if ($node = $child->findNodeByName($nodeName)) {
                return $node;
            }
        }

        return null;
    }

    /**
     * @return TestNode[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChildren(TestNode $children): void
    {
        $this->children[] = $children;
    }

    /**
     * @param TestNode[] $childrenList
     */
    public function addChildrenList(array $childrenList): void
    {
        array_push($this->children, ...$childrenList);
    }
}
