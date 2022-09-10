<?php
declare(strict_types=1);
namespace Horde\Platform;

use IteratorAggregate;
use SeekableIterator;
use Traversable;
use ValueError;

class Environment implements IteratorAggregate
{
    public function __construct(array $vars = getenv())
    {
    }

    public function getIterator(): Traversable
    {
        yield from $this->vars;
    }

    public function exists(string $key): bool
    {
        return array_key_exists($key, $this->vars);
    }

    public function get(string $key): string
    {
        if (!$this->exists) {
            throw new ValueError('This environment value is not set');
        }
        return $this->vars[$key];
    }

    public function getOrEmptyString(string $key): string
    {
        return $this->vars[$key] ?? '';
    }
}