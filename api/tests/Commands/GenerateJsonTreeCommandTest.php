<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Commands\GenerateJsonTreeCommand;
use App\UseCase\GenerateTreeHandler;
use Eva\Console\ArgvInput;
use PHPUnit\Framework\TestCase;

class GenerateJsonTreeCommandTest extends TestCase
{
    public function testMain(): void
    {
        $projectDir = '/code';
        $handler = $this->createMock(GenerateTreeHandler::class);
        $handler->method('handle');
        $argvInput = new ArgvInput();
        $argvInput->parseArgv([
            './bin/console',
            'generate.tree.json',
            '--input-file=/tasks/input.json',
        ]);
        $class = new GenerateJsonTreeCommand($projectDir, $handler);
        $this->assertNull($class->execute($argvInput));
    }
}
