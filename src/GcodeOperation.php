<?php

/*
 * This file is part of the GcodeEstimator project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\GcodeEstimator;

class GcodeOperation
{
    private $command;
    private $arguments;
    private $comment;
    private $lineNumber;

    public function __construct(string $line, int $lineNumber)
    {
        $code = trim($line);
        $commentPosition = strpos($code, ';');
        $comment = null;

        if (false !== $commentPosition) {
            $code = mb_substr($line, 0, $commentPosition);
            $comment = trim(mb_substr($line, $commentPosition + 1));
        }

        $parts = preg_split('/\s+/', $code);

        $this->command = trim(array_shift($parts));
        $this->arguments = array_filter($parts);
        $this->comment = $comment;
        $this->lineNumber = $lineNumber;
    }

    public function getCommand(): ?string
    {
        return $this->command;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    public function getExtruderValue(): ?float
    {
        foreach ($this->getArguments() as $argument) {
            if (0 !== mb_strpos($argument, 'E')) {
                continue;
            }

            return (float) mb_substr($argument, 1);
        }

        return null;
    }
}
