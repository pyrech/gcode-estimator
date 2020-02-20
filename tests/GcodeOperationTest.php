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
use Pyrech\GcodeEstimator\GcodeOperation;

class GcodeOperationTest extends TestCase
{
    /**
     * @dataProvider getLines
     */
    public function testItParsesLines(string $line, string $command, array $arguments, ?string $comment): void
    {
        $operation = new GcodeOperation($line);

        self::assertSame($command, $operation->getCommand());
        self::assertSame($arguments, $operation->getArguments());
        self::assertSame($comment, $operation->getComment());
    }

    public function getLines()
    {
        yield ['', '', [], null];
        yield [';   this is a comment  ', '', [], 'this is a comment'];
        yield [';  G1 E5', '', [], 'G1 E5'];
        yield [';   this is a ; comment  ', '', [], 'this is a ; comment'];
        yield ['G1 E5.3 X1', 'G1', ['E5.3', 'X1'], null];
        yield ['G1 X10      E10  ', 'G1', ['X10', 'E10'], null];
        yield ['G1 X8      E-10.5  ; test ', 'G1', ['X8', 'E-10.5'], 'test'];
        yield ['M83 ;test', 'M83', [], 'test'];
        yield ['M83', 'M83', [], null];
    }

    /**
     * @dataProvider getExtruderLines
     */
    public function testItExtractExtruderValue(string $line, ?float $extruderValue): void
    {
        $operation = new GcodeOperation($line);

        self::assertSame($extruderValue, $operation->getExtruderValue());
    }

    public function getExtruderLines()
    {
        yield ['', null];
        yield [';   this is a comment  ', null];
        yield [';  G1 E5', null];
        yield [';   this is a ; comment  ', null];
        yield ['G1 E5.3 X1', 5.3];
        yield ['G1 X10      E10  ', 10.0];
        yield ['G1 X10      E-10.5  ; test ', -10.5];
        yield ['M83 ;test', null];
        yield ['M83', null];
    }
}
