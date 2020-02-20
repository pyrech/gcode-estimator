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

class Estimator
{
    const LENGTH_UNIT_MM = 'mm';
    const LENGTH_UNIT_INCH = 'inch';

    public function estimate(string $gcodeFilename, Filament $filament = null): Estimate
    {
        $length = $this->estimateLengthUsed($gcodeFilename);
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
        $file = new \SplFileObject($gcodeFilename);

        $totalLengths = [
            self::LENGTH_UNIT_MM => 0,
            self::LENGTH_UNIT_INCH => 0,
        ];

        $positioningAbsolute = true;
        $currentUnit = self::LENGTH_UNIT_MM;
        $lastExtruderPosition = 0;

        while (!$file->eof()) {
            $line = $file->fgets();
            $operation = new GcodeOperation($line);

            switch ($operation->getCommand()) {
                case 'G0':
                case 'G1':
                case 'G2':
                case 'G3':
                    $length = $operation->getExtruderValue();

                    if ($positioningAbsolute) {
                        $totalLengths[$currentUnit] += $length - $lastExtruderPosition;
                    } else {
                        $totalLengths[$currentUnit] += $length;
                    }

                    $lastExtruderPosition = $length;
                    break;
                case 'G20':
                    $currentUnit = self::LENGTH_UNIT_INCH;
                    break;
                case 'G21':
                    $currentUnit = self::LENGTH_UNIT_MM;
                    break;
                case 'G92':
                    $lastExtruderPosition = $operation->getExtruderValue();
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

        // Unset the file to call __destruct(), closing the file handle.
        $file = null;

        return $totalLengths[self::LENGTH_UNIT_MM] + $totalLengths[self::LENGTH_UNIT_INCH] * 25.4;
    }
}
