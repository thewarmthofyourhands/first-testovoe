<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Dto\Services\CsvRowDto;
use App\Services\CsvParserService;
use Eva\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

class CsvParserServiceTest extends TestCase
{
    public function testMain(): void
    {
        $csvRowDto = new CsvRowDto([
            'itemName' => 'Total',
            'type' => 'Изделия и компоненты',
            'parent' => null,
            'relation' => null,
        ]);

        $filePath = '/code/tasks/input.csv';
        $filesystem = $this
            ->createMock(Filesystem::class);
        $filesystem
            ->expects($this->once())
            ->method('fopen')
            ->willReturnCallback(function (string $file, string $mode) use ($filePath) {
                self::assertEquals($file, $filePath);
                self::assertEquals($mode, 'rb');

                return $filePath;
            });
        $filesystem
            ->expects($this->exactly(2))
            ->method('fgets')
            ->withConsecutive(['/code/tasks/input.csv'], ['/code/tasks/input.csv'])
            ->willReturnOnConsecutiveCalls(
                '"Item Name";"Type";"Parent";"Relation"',
                '"Total";"Изделия и компоненты";;',
            );
        $filesystem
            ->expects($this->exactly(2))
            ->method('feof')
            ->withConsecutive(['/code/tasks/input.csv'], ['/code/tasks/input.csv'])
            ->willReturnOnConsecutiveCalls(
                false,
                true,
            );
        $filesystem
            ->expects($this->once())
            ->method('fclose')
            ->willReturnCallback(function (string $stream) {
                return true;
            });

        $csvParserService = new CsvParserService($filesystem);
        $generator = $csvParserService->parseByGenerator($filePath);

        foreach ($generator as $generateCsvRowDto) {
            $this->assertEquals($csvRowDto->toArray(), $generateCsvRowDto->toArray());
        }
    }
}
