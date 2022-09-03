<?php
/**
 * Copyright 2008-2022 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @author   Ralf Lang <ralf.lang@ralf-lang.de>
 * @license  http://www.horde.org/licenses/lgpl21 LGPL
 */
declare(strict_types=1);

namespace Horde\Platform;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Run a one-off command and capture STDOUT and return code.
 *
 * @author   Ralf Lang <ralf.lang@ralf-lang.de>
 * @copyright 2019-2022 Horde LLC
 */
class SimpleExecutor
{
    public function __construct(private LoggerInterface $logger = new NullLogger())
    {

    }
    /**
     * Run the command
     *
     * @param non-empty-string $cmd The complete command to run including all parameters
     * @param class-string<Exception>|'' $throwOnNonZero An exception to be thrown on non-zero return codes
     *
     *
     * @return ExecutionResult
     */
    public function __invoke(string $cmd, string $throwOnNonZero = ''): ExecutionResult
    {
        $outputArr = [];
        $returnCode = 0;
        exec($cmd, $outputArr, $returnCode);
        if ($throwOnNonZero && $returnCode) {
            throw new $throwOnNonZero('Command returned non-null value: ' . $cmd .  "\n"  . array_shift($outputArr), $returnCode);
        }
        return new ExecutionResult($outputArr, $returnCode);
    }
}
