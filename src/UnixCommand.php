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
 * Fluent interface for building and running a command
 * 
 * Implementation is geared towards unix style executables and builtins
 * 
 * @author   Ralf Lang <ralf.lang@ralf-lang.de>
 * @copyright 2019-2022 Horde LLC
 */
class UnixCommand
{
    private array $stack = [];
    private array $extraEnvironment = [];
    private string $executable;

    public function __construct(
        private Environment $env = new Environment(), 
        private LoggerInterface $logger = new NullLogger()
    )
    {

    }

    public function withExecutable(string $name): self
    {
        // TODO: Find full path for robustness
        $this->executable = $name;
        return $this;
    }

    /**
     * Add an argument
     * 
     * Values will be trimmed of leading/trailing whitespace.
     * Values will not be shell escaped
     *
     * @param string $argument
     * @return self
     */
    public function withArgument(string $argument): self
    {
        $this->stack[] = trim($argument);
        return $this;
    }

    public function withEnvironmentVariable(string $name, string $value): self
    {
        $this->extraEnvironment[$name] = $value;
        return $this;
    }

    /**
     * Append an option to the command
     * 
     * If the name is exactly one character, use a single hyphen -f
     * If the name is longer, use two hyphens --foo
     * If a value is provided, append it with equals --foo=bar
     *
     * TODO: Shell-escape values
     * 
     * @param string $name
     * @param string $value
     * @return void
     */
    public function withOption(string $name, ?string $value = null): self
    {
        $prefix = strlen($name) > 1 ? '--' : '-';
        $option = $prefix . $name;
        if ($value !== null) {
            $option .= '=' . $value;
        }
        $this->stack[] = $option;
        return $this;
    }

    /**
     * Append an option to the command if a value is given
     * 
     * If the name is exactly one character, use a single hyphen -f
     * If the name is longer, use two hyphens --foo
     * Append the value with equals --foo=bar
     * If no value is provided, don't add the option at all
     *
     * @param string $name
     * @param string $value
     * @return void
     */
    public function withOptionIfValue(string $name, ?string $value = null): self
    {
        if ($value !== null) {
            $this->withOption($name, $value);
        }
        return $this;
    }

    /**
     * Render the command as a string
     *
     * @return string
     */
    public function __toString(): string
    {
        $cmdLine = '';
        foreach ($this->extraEnvironment as $key => $value) {
            $cmdLine .= $key . '=' . $value . ' ';
        }
        $cmdLine .= $this->executable;
        if ($this->stack) {
            $cmdLine .= ' ';
        }
        $cmdLine .= implode(' ', $this->stack);
        return $cmdLine;
    }

    public static function fromArray(array $args, Environment $env = new Environment, LoggerInterface $logger = new NullLogger): UnixCommand
    {
        $cmd = new UnixCommand($env, $logger);
        foreach ($args as $argument) {
            $cmd->withArgument($argument);
        }
        return $cmd;
    }

    /**
     * Run the command to catch output and return code
     * 
     * The STDERR (error) output will not be caught
     *
     * @param class-string<Exception>|'' $throwOnNonZero An exception to be thrown on non-zero return codes
     *
     *
     * @return ExecutionResult
     */
    public function execute(string $throwOnNonZero = ''): ExecutionResultInterface
    {
        $cmd = (string) $this;
        $outputArr = [];
        $returnCode = 0;
        $this->logger->debug('Executed: ' . $cmd);
        exec($cmd, $outputArr, $returnCode);
        if ($throwOnNonZero && $returnCode) {
            throw new $throwOnNonZero('Command returned non-zero value: ' . $cmd .  "\n"  . array_shift($outputArr), $returnCode);
        }
        return new ExecutionResult($outputArr, $returnCode);
    }

    public function __invoke(string $throwOnNonZero = ''): ExecutionResultInterface
    {
        return $this->execute($throwOnNonZero);
    }

    // TODO: public function start(): Process

}