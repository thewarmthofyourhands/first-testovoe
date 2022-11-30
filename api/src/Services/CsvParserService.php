<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Services\CsvRowDto;
use Eva\Filesystem\Filesystem;
use Generator;
use RuntimeException;

class CsvParserService
{
    public function __construct(private readonly Filesystem $filesystem = new Filesystem()) {}

    public function parse(string $filePath): array
    {
        $csvSchema = [];
        $stream = $this->openFile($filePath);
        $columnList = $this->parseLine($this->getLine($stream));

        while (false === $this->filesystem->feof($stream)) {
            $csvLine = $this->getLine($stream);

            if (null === $csvLine) {
                break;
            }

            if ($csvLine === '' || $csvLine === PHP_EOL) {
                continue;
            }

            $csvRow = array_combine($columnList, $this->parseLine($csvLine));
            $csvRowDto = new CsvRowDto([
                'itemName' => $csvRow['Item Name'],
                'type' => $csvRow['Type'],
                'parent' => $csvRow['Parent'] === '' ? null : $csvRow['Parent'],
                'relation' => $csvRow['Relation'] === '' ? null : $csvRow['Relation'],
            ]);

            $csvSchema[] = $csvRowDto;
        }

        $this->filesystem->fclose($stream);

        return $csvSchema;
    }

    public function parseByGenerator(string $filePath): Generator
    {
        $stream = $this->openFile($filePath);
        $columnList = $this->parseLine($this->getLine($stream));

        while (false === $this->filesystem->feof($stream)) {
            $csvLine = $this->getLine($stream);

            if (null === $csvLine) {
                break;
            }

            if ($csvLine === '' || $csvLine === PHP_EOL) {
                continue;
            }

            $csvRow = array_combine($columnList, $this->parseLine($csvLine));
            $csvRowDto = new CsvRowDto([
                'itemName' => $csvRow['Item Name'],
                'type' => $csvRow['Type'],
                'parent' => $csvRow['Parent'] === '' ? null : $csvRow['Parent'],
                'relation' => $csvRow['Relation'] === '' ? null : $csvRow['Relation'],
            ]);

            yield $csvRowDto;
        }

        $this->filesystem->fclose($stream);
    }

    private function parseLine(string $line): array
    {
        return str_getcsv($line, ';');
    }

    private function openFile(string $filePath): mixed
    {
        $stream = $this->filesystem->fopen($filePath, 'rb');

        if (false === $stream) {
            throw new RuntimeException('File is not exist');
        }

        return $stream;
    }

    private function getLine($stream): null|string
    {
        $csvLine = $this->filesystem->fgets($stream);

        if (false === $csvLine) {
            if (true === $this->filesystem->feof($stream)) {
                return null;
            }

            throw new RuntimeException('Invalid csv format');
        }

        return $csvLine;
    }
}
