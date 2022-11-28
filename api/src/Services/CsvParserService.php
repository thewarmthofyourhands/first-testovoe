<?php

declare(strict_types=1);

namespace App\Services;

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

            $csvRow = $this->parseLine($csvLine);
            $csvRow = array_combine($columnList, $csvRow);

            $csvSchema[] = $csvRow;
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

            $csvRow = $this->parseLine($csvLine);
            $csvRow = array_combine($columnList, $csvRow);

            yield $csvRow;
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
