<?php

declare(strict_types=1);

namespace App\Models;

class Tree
{
    /** @var TestNode[] $nodeList */
    private array $nodeList = [];

    public function addNode(TestNode $node): void
    {
        array_unshift($this->nodeList, $node);
    }

    public function findNodeByItemName(string $itemName): null|TestNode
    {
        $foundNode = null;

        foreach ($this->nodeList as $node) {
             $foundNode = $node->findNodeByName($itemName);
             if (null !== $foundNode) {
                 break;
             }
        }

        return $foundNode;
    }

    public function findRootNodeListByParent(string $parent): array
    {
        $foundNodeList = [];

        foreach ($this->nodeList as $key => $node) {
            if ($node->getParent() === $parent) {
                $foundNodeList[$key] = $node;
            }
        }

        return $foundNodeList;
    }

    public function deleteRootNode(array $keyList): void
    {
        foreach ($keyList as $key) {
            unset($this->nodeList[$key]);
        }
    }

    public function toArray(): array
    {
        $nodeList = [];

        foreach ($this->nodeList as $node) {
            $nodeList[] = $node->toArray();
        }

        return $nodeList;
    }
}
