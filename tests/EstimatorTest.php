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
use Pyrech\GcodeEstimator\Filament;

class EstimatorTest extends TestCase
{
    /**
     * @dataProvider getGcodeFixtures
     */
    public function testItEstimatesGcodes(string $filename, Estimate $expectedEstimate, ?Filament $filament): void
    {
        $estimate = (new Estimator())->estimate($filename, $filament);

        self::assertLessThanOrEqual(5, abs(
            ($expectedEstimate->getLength() - $estimate->getLength()) * 100 / $expectedEstimate->getLength()
        ), 'Expected length precision to be lower than 5%');

        if (null !== $expectedEstimate->getWeight()) {
            self::assertLessThanOrEqual(5, abs(
                ($expectedEstimate->getWeight() - $estimate->getWeight()) * 100 / $expectedEstimate->getWeight()
            ), 'Expected weight precision to be lower than 5%');
        }

        if (null !== $expectedEstimate->getCost()) {
            self::assertLessThanOrEqual(5, abs(
                ($expectedEstimate->getCost() - $estimate->getCost()) * 100 / $expectedEstimate->getCost()
            ), 'Expected cost precision to be lower than 5%');
        }
    }

    public function testItEstimatesOnlyWeightWhenNoFilamentGiven()
    {
        $estimate = (new Estimator())->estimate(__DIR__ . '/../tests/fixtures/Bulbasaur/model.gcode');

        self::assertNotNull($estimate->getLength());
        self::assertNull($estimate->getWeight());
        self::assertNull($estimate->getCost());
    }

    public function testItThrowsExceptionWhenInvalidFile()
    {
        self::expectException(\Exception::class);

        (new Estimator())->estimate(__DIR__ . '/../tests/fixtures/yolo.gcode');
    }

    public function getGcodeFixtures(): \Generator
    {
        foreach (new \DirectoryIterator(__DIR__ . '/fixtures/') as $fixture) {
            if ($fixture->isDot() || !$fixture->isDir()) {
                continue;
            }

            yield [$fixture->getRealPath() . '/model.gcode', require($fixture->getRealPath() . '/expected.php'), include($fixture->getRealPath() . '/filament.php') ?? null];
        }
    }
}
