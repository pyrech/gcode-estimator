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

class Estimate
{
    private $length;
    private $weight;
    private $cost;

    public function __construct(float $length, ?float $weight, ?float $cost)
    {
        $this->length = $length;
        $this->weight = $weight;
        $this->cost = $cost;
    }

    public function getLength(): float
    {
        return $this->length;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function getCost(): ?float
    {
        return $this->cost;
    }
}
