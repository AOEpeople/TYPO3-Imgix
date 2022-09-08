<?php

namespace Aoe\Imgix\Tests\TYPO3;

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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;

class ConfigurationTest extends UnitTestCase
{
    public function testShouldCheckThatApiKeyIsNotConfigured(): void
    {
        $configuration = $this->createConfigurationObject([], []);
        $this->assertFalse($configuration->isApiKeyConfigured());
    }

    public function testShouldCheckThatApiKeyIsConfigured(): void
    {
        $configuration = $this->createConfigurationObject([], ['apiKey' => 'myApiKey']);
        $this->assertTrue($configuration->isApiKeyConfigured());
    }

    public function testShouldGetApiKey(): void
    {
        $configuration = $this->createConfigurationObject([], ['apiKey' => 'myApiKey']);
        $this->assertSame('myApiKey', $configuration->getApiKey());
    }

    public function testShouldGetOverwrittenApiKeyBySettings(): void
    {
        $configuration = $this->createConfigurationObject(['apiKey' => 'myApiKey2'], ['apiKey' => 'myApiKey']);
        $this->assertSame('myApiKey2', $configuration->getApiKey());
    }

    public function testShouldGetHost(): void
    {
        $configuration = $this->createConfigurationObject([], ['host' => 'mysubdomain.imgix.net']);
        $this->assertSame('mysubdomain.imgix.net', $configuration->getHost());
    }

    public function testShouldGetOverwrittenHostBySettings(): void
    {
        $configuration = $this->createConfigurationObject(['host' => 'mysubdomain2.imgix.net'], ['host' => 'mysubdomain.imgix.net']);
        $this->assertSame('mysubdomain2.imgix.net', $configuration->getHost());
    }

    public function testShouldGetEnabled(): void
    {
        $configuration = $this->createConfigurationObject([], ['enabled' => '1']);
        $this->assertTrue($configuration->isEnabled());
    }

    public function testShouldGetEnabledOverwrittenBySettings(): void
    {
        $configuration = $this->createConfigurationObject(['enabled' => '0'], ['enabled' => '1']);
        $this->assertFalse($configuration->isEnabled());
    }

    public function testShouldGetEnableFluid(): void
    {
        $configuration = $this->createConfigurationObject([], ['enableFluid' => '0']);
        $this->assertFalse($configuration->isFluidEnabled());
    }

    public function testShouldGetEnableFluidOverwrittenBySettings(): void
    {
        $configuration = $this->createConfigurationObject(['enableFluid' => '1'], ['enableFluid' => '0']);
        $this->assertTrue($configuration->isFluidEnabled());
    }

    public function testShouldGetEnableObservation(): void
    {
        $configuration = $this->createConfigurationObject([], ['enableObservation' => '0']);
        $this->assertFalse($configuration->isObservationEnabled());
    }

    public function testShouldGetEnableObservationOverwrittenBySettings(): void
    {
        $configuration = $this->createConfigurationObject(['enableObservation' => '1'], ['enableObservation' => '0']);
        $this->assertTrue($configuration->isObservationEnabled());
    }

    public function testShouldGetEmptyImgixFluidOptions(): void
    {
        $configuration = $this->createConfigurationObject([], []);
        $this->assertSame([], $configuration->getImgixFluidOptions());
    }

    public function testShouldGetImgixFluidOptions(): void
    {
        $configuration = $this->createConfigurationObject(
            [],
            ['imgix' =>
                [
                    'fluid' => [
                        'fluidClass' => 'my-class',
                        'updateOnResize' => '1',
                        'updateOnResizeDown' => '0',
                        'updateOnPinchZoom' => '1',
                        'highDPRAutoScaleQuality' => '0',
                        'autoInsertCSSBestPractices' => '1',
                        'fitImgTagToContainerWidth' => '0',
                        'fitImgTagToContainerHeight' => '1',
                        'pixelStep' => '100',
                        'ignoreDPR' => '0',
                        'debounce' => '500',
                        'lazyLoad' => '1',
                        'lazyLoadOffsetVertical' => '167',
                        'lazyLoadOffsetHorizontal' => '767',
                        'throttle' => '4711',
                        'maxWidth' => '111',
                        'maxHeight' => '222'
                    ]
                ]
            ]
        );
        $this->assertSame(
            [
                'fluidClass' => 'my-class',
                'updateOnResize' => true,
                'updateOnResizeDown' => false,
                'updateOnPinchZoom' => true,
                'highDPRAutoScaleQuality' => false,
                'autoInsertCSSBestPractices' => true,
                'fitImgTagToContainerWidth' => false,
                'fitImgTagToContainerHeight' => true,
                'pixelStep' => 100,
                'ignoreDPR' => false,
                'debounce' => 500,
                'lazyLoad' => true,
                'lazyLoadOffsetVertical' => 167,
                'lazyLoadOffsetHorizontal' => 767,
                'throttle' => 4711,
                'maxWidth' => 111,
                'maxHeight' => 222,
            ],
            $configuration->getImgixFluidOptions()
        );
    }

    public function testShouldGetEmptyImgixDefaultUrlParameters(): void
    {
        $configuration = $this->createConfigurationObject([], []);
        $this->assertSame([], $configuration->getImgixDefaultUrlParameters());
    }

    public function testShouldGetImgixDefaultUrlParameters(): void
    {
        $configuration = $this->createConfigurationObject(
            [],
            ['imgix' => ['defaultUrlParameters' => 'q=75&auto=format']]
        );
        $this->assertSame(
            [
                'q' => '75',
                'auto' => 'format'
            ],
            $configuration->getImgixDefaultUrlParameters()
        );
    }

    /**
     * @param array $settings
     * @param array $extensionConfig
     * @return Configuration
     */
    private function createConfigurationObject(array $settings, array $extensionConfig): Configuration
    {
        /** @var ExtensionConfiguration|MockObject $extensionConfiguration */
        $extensionConfiguration = $this->getMockBuilder(ExtensionConfiguration::class)
            ->disableOriginalConstructor()
            ->getMock();
        $extensionConfiguration->expects(self::once())->method('get')->with('imgix')->willReturn($extensionConfig);

        /** @var ConfigurationManagerInterface|MockObject $configurationManager */
        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configurationManager->expects(self::once())->method('getConfiguration')->willReturn($settings);

        return new Configuration($configurationManager, $extensionConfiguration);
    }
}
