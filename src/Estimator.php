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

use Pyrech\GcodeEstimator\Exception\FileNotReadable;
use Pyrech\GcodeEstimator\Exception\InvalidGcode;

class Estimator
{
    const LENGTH_UNIT_MM = 'mm';
    const LENGTH_UNIT_INCH = 'inch';

    /**
     * @throws FileNotReadable
     * @throws InvalidGcode
     */
    public function estimate(string $gcodeFilename, Filament $filament = null): Estimate
    {
        $length = $this->estimateLengthUsed($gcodeFilename);

        if ($length <= 0) {
            throw new InvalidGcode('Invalid filament length estimated');
        }

        $weight = null;
        $cost = null;

        if ($filament) {
            $weight = $filament->getDensity() * ($length / 10) * M_PI * pow($filament->getDiameter() / 2 / 10, 2);
            $cost = $filament->getSpoolPrice() * $weight / $filament->getSpoolWeight();
        }

        return new Estimate($length, $weight, $cost);
    }

    private function estimateLengthUsed(string $gcodeFilename): float
    {
        try {
            $file = new \SplFileObject($gcodeFilename);
        } catch (\Exception $e) {
            throw new FileNotReadable($gcodeFilename, $e);
        }

        $totalLengths = [
            self::LENGTH_UNIT_MM => 0,
            self::LENGTH_UNIT_INCH => 0,
        ];

        $positioningAbsolute = true;
        $currentUnit = self::LENGTH_UNIT_MM;
        $lastExtruderPosition = 0;

        foreach ($file as $index => $line) {
            $operation = new GcodeOperation($line, $index + 1);

            if (empty($operation->getCommand())) {
                continue;
            }

            switch ($operation->getCommand()) {
                case 'G0':
                case 'G1':
                case 'G2':
                case 'G3':
                    $value = $operation->getExtruderValue();

                    if (null !== $value) {
                        if ($positioningAbsolute) {
                            $length = $value - $lastExtruderPosition;
                            $lastExtruderPosition = $value;
                        } else {
                            $length = $value;
                            $lastExtruderPosition = 0;
                        }

                        $totalLengths[$currentUnit] += $length;
                    }
                    break;
                case 'G20':
                    $currentUnit = self::LENGTH_UNIT_INCH;
                    break;
                case 'G21':
                    $currentUnit = self::LENGTH_UNIT_MM;
                    break;
                case 'G92':
                    $value = $operation->getExtruderValue();

                    if (null !== $value || 0 === \count($operation->getArguments())) {
                        $lastExtruderPosition = $value ?? 0;
                    }
                    break;
                case 'G90':
                case 'M82':
                    $positioningAbsolute = true;
                    break;
                case 'G91':
                case 'M83':
                    $positioningAbsolute = false;
                    break;
            }
        }

        return $totalLengths[self::LENGTH_UNIT_MM] + $totalLengths[self::LENGTH_UNIT_INCH] * 25.4;
    }
}
