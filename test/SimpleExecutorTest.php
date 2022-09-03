<?php

declare(strict_types=1);

namespace Horde\Platform\Test;

use Horde\Platform\ExecutionResult;
use Horde\Platform\ExecutionResultInterface;
use Horde\Platform\SimpleExecutor;
use PHPUnit\Framework\TestCase;

class SimpleExecutorTest extends TestCase
{
    public function testCreation()
    {
        $this->assertInstanceOf(SimpleExecutor::class, new SimpleExecutor());
    }

    public function testRunningNonExistingCommand()
    {
        $run = new SimpleExecutor();
        $result = $run('magiccommand');
        $this->assertInstanceOf(ExecutionResult::class, $result);
        $this->assertInstanceOf(ExecutionResultInterface::class, $result);
        $this->assertIsInt($result->getReturnCode());
        $this->assertIsString($result->getOutputString());
        $this->assertIsArray($result->getOutputArray());
    }

    public function testRunningExistingCommand()
    {
        $run = new SimpleExecutor();
        $result = $run('echo "foo"');
        $this->assertInstanceOf(ExecutionResult::class, $result);
        $this->assertInstanceOf(ExecutionResultInterface::class, $result);
        $this->assertIsInt($result->getReturnCode());
        $this->assertIsString($result->getOutputString());
        $this->assertIsArray($result->getOutputArray());
        $this->assertEquals('foo', $result->getOutputArray()[0]);
        $this->assertEquals('foo', $result->getOutputString());
    }

}