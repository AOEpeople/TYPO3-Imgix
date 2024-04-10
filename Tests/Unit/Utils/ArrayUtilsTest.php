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

use Aoe\Imgix\Utils\ArrayUtils;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ArrayUtilsTest extends UnitTestCase
{
    public function testShouldFilterNullValue(): void
    {
        $actual = ArrayUtils::filterEmptyValues([
            'foo' => null,
            'bar' => 'baz',
        ]);
        $this->assertSame(['bar' => 'baz'], $actual);
    }

    public function testShouldFilterEmptyStringValue(): void
    {
        $actual = ArrayUtils::filterEmptyValues(['foo' => '', 'bar' => 'baz']);
        $this->assertSame(['bar' => 'baz'], $actual);
    }

    public function testShouldNotFilterZeroNumbers(): void
    {
        $actual = ArrayUtils::filterEmptyValues(['foo' => 0, 'bar' => 'baz']);
        $this->assertSame($actual, $actual);
    }
}
