<?php

declare(strict_types=1);

namespace Aoe\Imgix\Tests\ViewHelpers;

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

use Aoe\Imgix\TYPO3\Configuration;
use Aoe\Imgix\ViewHelpers\ImgixUrlViewHelper;
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ImgixUrlViewHelperTest extends UnitTestCase
{
    private ImgixUrlViewHelper $viewHelper;

    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewHelper = new ImgixUrlViewHelper($this->configuration);
    }

    public function testShouldRenderImageUrlWithoutImgixHostWhenImgixIsNotEnabled(): void
    {
        $this->configuration->expects($this->once())->method('isEnabled')->willReturn(false);
        $this->viewHelper->setArguments([
            'imageUrl' => 'test-url',
        ]);
        $this->assertSame('test-url', $this->viewHelper->render());
    }

    /**
     * @dataProvider
     */
    public function testShouldRenderImageUrlWithImgixHostWhenImgixIsEnabled(): void
    {
        $this->configuration->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->configuration->expects($this->once())->method('getHost')->willReturn('aoe.host');
        $this->configuration->method('getImgixDefaultUrlParameters')->willReturn([]);
        $this->viewHelper->setArguments([
            'imageUrl' => 'test-url',
        ]);
        $this->assertSame('//aoe.host/test-url', $this->viewHelper->render());
    }

    /**
     * @dataProvider parameterProvider
     */
    public function testShouldRenderImageUrlWithParameters(array $defaultParameters, array $givenParameters, string $expectedParameterString): void
    {
        $this->configuration->expects($this->once())->method('isEnabled')->willReturn(true);
        $this->configuration->expects($this->once())->method('getHost')->willReturn('aoe.host');
        $this->configuration->method('getImgixDefaultUrlParameters')->willReturn($defaultParameters);
        $this->viewHelper->setArguments([
            'imageUrl' => 'test-url',
            'urlParameters' => $givenParameters,
        ]);
        $this->assertSame('//aoe.host/test-url' . $expectedParameterString, $this->viewHelper->render());
    }

    public function parameterProvider(): array
    {
        return [
            [
                [],
                ['foo' => 'bar', 'bar' => 'baz'],
                '?foo=bar&bar=baz',
            ],
            [
                ['foo' => 'bar', 'bar' => 'baz'],
                [],
                '?foo=bar&bar=baz',
            ],
            [
                ['foo' => 'bar'],
                ['foo' => 'baz'],
                '?foo=baz',
            ],
        ];
    }
}
