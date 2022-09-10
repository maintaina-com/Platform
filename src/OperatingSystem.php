<?php
/**
 * Platform detection utility
 *
 * OO interface
 *
 * @author Ralf Lang <ralf.lang@ralf-lang.de>
 */
declare(strict_types=1);

namespace Horde\Platform;

class OperatingSystem implements OperatingSystemInterface
{
    public function getFamily(): string
    {
        return '';
    }
}
