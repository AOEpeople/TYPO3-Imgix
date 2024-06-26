<?php

declare(strict_types=1);

namespace Aoe\Imgix\Tests\Utils;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2018 AOE GmbH <dev@aoe.com>
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Aoe\Imgix\Utils\TypeUtils;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TypeUtilsTest extends UnitTestCase
{
    public function testShouldMapTypeString(): void
    {
        $actual = TypeUtils::castTypesByMap(
            [
                'foo' => TypeUtils::TYPE_STRING,
                'bar' => TypeUtils::TYPE_STRING,
            ],
            [
                'foo' => 0,
                'bar' => null,
            ]
        );
        $this->assertSame([
            'foo' => '0',
            'bar' => '',
        ], $actual);
    }

    public function testShouldMapTypeInteger(): void
    {
        $actual = TypeUtils::castTypesByMap(
            [
                'foo' => TypeUtils::TYPE_INTEGER,
                'bar' => TypeUtils::TYPE_INTEGER,
            ],
            [
                'foo' => 0,
                'bar' => null,
            ]
        );
        $this->assertSame([
            'foo' => 0,
            'bar' => 0,
        ], $actual);
    }

    public function testShouldMapTypeBoolean(): void
    {
        $actual = TypeUtils::castTypesByMap(
            [
                'foo' => TypeUtils::TYPE_BOOLEAN,
                'bar' => TypeUtils::TYPE_BOOLEAN,
                'baz' => TypeUtils::TYPE_BOOLEAN,
            ],
            [
                'foo' => 0,
                'bar' => null,
                'baz' => 1,
            ]
        );
        $this->assertSame([
            'foo' => false,
            'bar' => false,
            'baz' => true,
        ], $actual);
    }

    public function testShouldDoNothingOnMissingMap(): void
    {
        $actual = TypeUtils::castTypesByMap(
            [],
            [
                'foo' => 0,
                'bar' => null,
                'baz' => 1,
            ]
        );
        $this->assertSame([
            'foo' => 0,
            'bar' => null,
            'baz' => 1,
        ], $actual);
    }
}
