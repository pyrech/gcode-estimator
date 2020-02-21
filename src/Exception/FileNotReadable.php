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

class FileNotReadable extends \Exception
{
    private $path;

    public function __construct(string $path, \Throwable $previous = null)
    {
        parent::__construct(sprintf('Could not read the file "%s"', $path), 0, $previous);

        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
