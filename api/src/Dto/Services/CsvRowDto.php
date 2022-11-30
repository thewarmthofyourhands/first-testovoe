<?php

declare(strict_types=1);

namespace App\Dto\Services;

use Eva\Common\DtoTrait;

class CsvRowDto
{
    use DtoTrait;

    private readonly string $itemName;
    private readonly string $type;
    private readonly null|string $parent;
    private readonly null|string $relation;

    public function getItemName(): string
    {
        return $this->itemName;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getParent(): null|string
    {
        return $this->parent;
    }

    public function getRelation(): null|string
    {
        return $this->relation;
    }
}
