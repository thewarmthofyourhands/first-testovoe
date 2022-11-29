<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NodeTypeEnum;

class Node
{
    public null|array $arrayFormat = null;

    public function __construct(
        private readonly string $itemName,
        private readonly null|string $parent,
        private readonly NodeTypeEnum $type,
        private array $children = [],
        private null|Node $relation = null,
    ) {}

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

    public function getRelation(): null|Node
    {
        return $this->relation;
    }

    public function setRelation(null|Node $relation): void
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

    public function findNodeByName(string $nodeName): null|Node
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
     * @return Node[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function addChildren(Node $children): void
    {
        $this->children[] = $children;
    }

    /**
     * @param Node[] $childrenList
     */
    public function addChildrenList(array $childrenList): void
    {
        array_push($this->children, ...$childrenList);
    }
}
