<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\Services\CsvRowDto;
use Generator;
use RuntimeException;

class CsvParserService
{
    public function parse(string $filePath): array
    {
        $csvSchema = [];
        $stream = $this->openFile($filePath);
        $columnList = $this->parseLine($this->getLine($stream));

        while (false === feof($stream)) {
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
                'relation' => $csvRow['Relation'],
            ]);

            $csvSchema[] = $csvRowDto;
        }

        fclose($stream);

        return $csvSchema;
    }

    public function parseByGenerator(string $filePath): Generator
    {
        $stream = $this->openFile($filePath);
        $columnList = $this->parseLine($this->getLine($stream));

        while (false === feof($stream)) {
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
                'relation' => $csvRow['Relation'],
            ]);

            yield $csvRowDto;
        }

        fclose($stream);
    }

    private function parseLine(string $line): array
    {
        return str_getcsv($line, ';');
    }

    private function openFile(string $filePath): mixed
    {
        $stream = fopen($filePath, 'rb');

        if (false === $stream) {
            throw new RuntimeException('File is not exist');
        }

        return $stream;
    }

    private function getLine($stream): null|string
    {
        $csvLine = fgets($stream);

        if (false === $csvLine) {
            if (true === feof($stream)) {
                return null;
            }

            throw new RuntimeException('Invalid csv format');
        }

        return $csvLine;
    }
}
