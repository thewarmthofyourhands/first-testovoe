<?php

declare(strict_types=1);

namespace App\Models;

class Node
{
    public function __construct(
        private readonly string $itemName,
        private readonly string $parent,
        private array $children,
    ) {}

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getParent(): string
    {
        return $this->parent;
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

    /**
     * @param Node[] $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function toArray(): array
    {
        $children = [];

        foreach ($this->getChildren() as $child) {
            $children[] = $child->toArray();
        }

        return [
            'itemName' => $this->getItemName(),
            'parent' => $this->getParent(),
            'children' => $children,
        ];
    }
}
