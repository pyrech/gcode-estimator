<?php

/*
 * This file is part of the GcodeEstimator project.
 *
 * (c) Loïck Piera <pyrech@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pyrech\GcodeEstimator\Tests;

use PHPUnit\Framework\TestCase;
use Pyrech\GcodeEstimator\Estimate;
use Pyrech\GcodeEstimator\Estimator;
use Pyrech\GcodeEstimator\Exception\FileNotReadable;
use Pyrech\GcodeEstimator\Filament;

class EstimatorTest extends TestCase
{
    /**
     * @dataProvider getGcodeFixtures
     */
    public function testItEstimatesGcodes(string $filename, Estimate $expectedEstimate, ?Filament $filament): void
    {
        $estimate = (new Estimator())->estimate($filename, $filament);

        self::assertLessThanOrEqual(0.5, abs(
            ($expectedEstimate->getLength() - $estimate->getLength()) * 100 / $expectedEstimate->getLength()
        ), 'Expected length precision to be lower than 0.5%');

        if (null !== $expectedEstimate->getWeight()) {
            self::assertLessThanOrEqual(2, abs(
                ($expectedEstimate->getWeight() - $estimate->getWeight()) * 100 / $expectedEstimate->getWeight()
            ), 'Expected weight precision to be lower than 2%');
        }

        if (null !== $expectedEstimate->getCost()) {
            self::assertLessThanOrEqual(2, abs(
                ($expectedEstimate->getCost() - $estimate->getCost()) * 100 / $expectedEstimate->getCost()
            ), 'Expected cost precision to be lower than 2%');
        }
    }

    public function testItEstimatesOnlyLengthWhenNoFilamentGiven()
    {
        $estimate = (new Estimator())->estimate(__DIR__ . '/../tests/fixtures/Bulbasaur/model.gcode');

        self::assertNotNull($estimate->getLength());
        self::assertNull($estimate->getWeight());
        self::assertNull($estimate->getCost());
    }

    public function testItThrowsExceptionWhenInvalidFile()
    {
        self::expectException(FileNotReadable::class);

        (new Estimator())->estimate(__DIR__ . '/../tests/fixtures/yolo.gcode');
    }

    public function getGcodeFixtures(): \Generator
    {
        foreach (new \DirectoryIterator(__DIR__ . '/fixtures/') as $fixture) {
            if ($fixture->isDot() || !$fixture->isDir()) {
                continue;
            }

            $expectedFile = $fixture->getRealPath() . '/expected.php';
            $filamentFile = $fixture->getRealPath() . '/filament.php';

            yield [
                $fixture->getRealPath() . '/model.gcode',
                require($expectedFile),
                file_exists($filamentFile) ? include($filamentFile) : null,
            ];
        }
    }
}
