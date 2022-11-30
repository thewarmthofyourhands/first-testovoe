<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Dto\Services\CsvRowDto;
use PHPUnit\Framework\TestCase;

class CsvRowDtoTest extends TestCase
{
    public function testMain(): void
    {
        $itemName = 'Total';
        $type = 'Изделия и компоненты';
        $parent = null;
        $relation = '';
        $dto = new CsvRowDto([
            'itemName' => $itemName,
            'type' => $type,
            'parent' => null,
            'relation' => $relation,
        ]);
        $this->assertEquals($dto->getItemName(), $itemName);
        $this->assertEquals($dto->getType(), $type);
        $this->assertEquals($dto->getParent(), $parent);
        $this->assertEquals($dto->getRelation(), $relation);
    }
}
