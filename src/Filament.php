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

class Filament
{
    /**
     * Unit: mm.
     */
    private $diameter;

    /**
     * Unit: g/cm³.
     */
    private $density;

    /**
     * Unit: g.
     */
    private $spoolWeight;

    private $spoolPrice;

    public function __construct(float $diameter, float $density, float $spoolWeight, float $spoolPrice)
    {
        $this->diameter = $diameter;
        $this->density = $density;
        $this->spoolWeight = $spoolWeight;
        $this->spoolPrice = $spoolPrice;
    }

    public function getDiameter(): float
    {
        return $this->diameter;
    }

    public function getDensity(): float
    {
        return $this->density;
    }

    public function getSpoolWeight(): float
    {
        return $this->spoolWeight;
    }

    public function getSpoolPrice(): float
    {
        return $this->spoolPrice;
    }

    public static function createClassicPLA(): self
    {
        return new self(1.75, 1.24, 750, 25.99);
    }
}
