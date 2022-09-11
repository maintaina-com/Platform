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

    public function testSettingExecutableTwice()
    {
        $cmd = (new UnixCommand())->withExecutable('php5')->withExecutable('php');
        $this->assertEquals('php', (string) $cmd);
    }

    public function testSettingArguments()
    {
        $cmd = (new UnixCommand())->withExecutable('php')->withArgument('vendor/bin/hordectl');
        $this->assertEquals('php vendor/bin/hordectl', (string) $cmd);
        $cmd->withArgument('patch')->withArgument('user')->withArgument('secretPassword');
        $this->assertEquals('php vendor/bin/hordectl patch user secretPassword', (string) $cmd);
    }

    public function testSettingOptions()
    {
        $cmd = (new UnixCommand())->withExecutable('php')->withOption('version');
        $this->assertEquals('php --version', (string) $cmd);
        $cmd = (new UnixCommand())->withExecutable('php')->withOption('h');
        $this->assertEquals('php -h', (string) $cmd);
        $cmd = (new UnixCommand())->withExecutable('php')->withOption('r', '"phpinfo();"');
        $this->assertEquals('php -r="phpinfo();"', (string) $cmd);
        $cmd = (new UnixCommand())->withExecutable('phpunit')->withOption('atleast-version', '7.0.0');
        $this->assertEquals('phpunit --atleast-version=7.0.0', (string) $cmd);
    }
}
