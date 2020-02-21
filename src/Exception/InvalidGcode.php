<?php

/*
 * This file is part of the GcodeEstimator project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\GcodeEstimator\Exception;

class InvalidGcode extends \Exception
{
    public function __construct(string $message, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Invalid gcode: "%s"', $message), 0, $previous);
    }
}
