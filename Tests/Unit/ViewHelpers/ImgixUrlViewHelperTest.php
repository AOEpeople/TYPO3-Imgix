<?php
namespace Aoe\Imgix\Tests\ViewHelpers;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH <dev@aoe.com>
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
    /**
     * @var ImgixUrlViewHelper
     */
    private $viewHelper;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * setUp
     */
    public function setUp()
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->viewHelper = new ImgixUrlViewHelper($this->configuration);
    }

    /**
     * @test
     */
    public function shouldRenderImageUrlWithoutImgixHostWhenImgixIsNotEnabled()
    {
        $this->configuration->expects($this->once())->method('isEnabled')->will(
            $this->returnValue(false)
        );
        $this->viewHelper->setArguments([
            'imageUrl' => 'test-url'
        ]);
        $this->assertSame('test-url', $this->viewHelper->render());
    }

    /**
     * @test
     * @dataProvider
     */
    public function shouldRenderImageUrlWithImgixHostWhenImgixIsEnabled()
    {
        $this->configuration->expects($this->once())->method('isEnabled')->will(
            $this->returnValue(true)
        );
        $this->configuration->expects($this->once())->method('getHost')->will(
            $this->returnValue('aoe.host')
        );
        $this->configuration->expects($this->any())->method('getImgixDefaultUrlParameters')->will(
            $this->returnValue([])
        );
        $this->viewHelper->setArguments([
            'imageUrl' => 'test-url'
        ]);
        $this->assertSame('//aoe.host/test-url', $this->viewHelper->render());
    }

    /**
     * @test
     * @dataProvider parameterProvider
     */
    public function shouldRenderImageUrlWithParameters($defaultParameters, $givenParameters, $expectedParameterString)
    {
        $this->configuration->expects($this->once())->method('isEnabled')->will(
            $this->returnValue(true)
        );
        $this->configuration->expects($this->once())->method('getHost')->will(
            $this->returnValue('aoe.host')
        );
        $this->configuration->expects($this->any())->method('getImgixDefaultUrlParameters')->will(
            $this->returnValue($defaultParameters)
        );
        $this->viewHelper->setArguments([
            'imageUrl' => 'test-url',
            'urlParameters' => $givenParameters
        ]);
        $this->assertSame('//aoe.host/test-url' . $expectedParameterString, $this->viewHelper->render());
    }

    /**
     * @return array
     */
    public function parameterProvider()
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
            ]
        ];
    }
}
