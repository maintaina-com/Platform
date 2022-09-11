<?php

declare(strict_types=1);

namespace Horde\Platform\Test;

use Horde\Platform\ExecutionResult;
use Horde\Platform\ExecutionResultInterface;
use Horde\Platform\SimpleExecutor;
use Horde\Platform\UnixCommand;
use PHPUnit\Framework\TestCase;
use Error;

class UnixCommandTest extends TestCase
{
    public function testCreationFromArray()
    {
        $this->assertInstanceOf(UnixCommand::class, UnixCommand::fromArray(['php']));
    }

    public function testRenderWithoutArguments()
    {
        $cmd = (new UnixCommand())->withExecutable('php');
        $this->assertEquals('php', (string) $cmd);
        $cmd->withEnvironmentVariable('SECRET', 'VALUE');
        $this->assertEquals('SECRET=VALUE php', (string) $cmd);
    }

    public function testRenderWithoutExecutable()
    {
        $this->expectException(Error::class);
        $cmd = (new UnixCommand())->withArgument('help');
        (string) $cmd;
    }
}
