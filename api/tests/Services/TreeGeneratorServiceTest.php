<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Dto\Services\CsvRowDto;
use App\Services\TreeGeneratorService;
use PHPUnit\Framework\TestCase;

class TreeGeneratorServiceTest extends TestCase
{
    public function testMain(): void
    {
        $csvRowDto = new CsvRowDto([
            'itemName' => 'Total',
            'type' => 'Изделия и компоненты',
            'parent' => null,
            'relation' => null,
        ]);

        $treeGeneratorService = new TreeGeneratorService();
        $tree = $treeGeneratorService->generateTree([$csvRowDto]);
        $this->assertEquals($tree->toArray(), [
            [
                'itemName' => 'Total',
                'parent' => null,
                'children' => [],
            ],
        ]);
    }
}
