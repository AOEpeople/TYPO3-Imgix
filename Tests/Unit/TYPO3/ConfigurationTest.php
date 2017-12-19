<?php
namespace Aoe\Imgix\Tests\TYPO3;

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
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ConfigurationTest extends UnitTestCase
{
    public function tearDown()
    {
        unset($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imgix']);
    }

    /**
     * @test
     */
    public function shouldGetHost()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['host' => 'mysubdomain.imgix.net']);
        $configuration = new Configuration($configurationManager);
        $this->assertSame('mysubdomain.imgix.net', $configuration->getHost());
    }

    /**
     * @test
     */
    public function shouldGetOverwrittenHostBySettings()
    {
        $configurationManager = $this->getMockedConfigurationManager(['host' => 'mysubdomain2.imgix.net']);
        $this->fakeConfiguration(['host' => 'mysubdomain.imgix.net']);
        $configuration = new Configuration($configurationManager);
        $this->assertSame('mysubdomain2.imgix.net', $configuration->getHost());
    }

    /**
     * @test
     */
    public function shouldGetEnabled()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['enabled' => '1']);
        $configuration = new Configuration($configurationManager);
        $this->assertTrue($configuration->isEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnabledOverwrittenBySettings()
    {
        $configurationManager = $this->getMockedConfigurationManager(['enabled' => '0']);
        $this->fakeConfiguration(['enabled' => '1']);
        $configuration = new Configuration($configurationManager);
        $this->assertFalse($configuration->isEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnableFluid()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['enableFluid' => '0']);
        $configuration = new Configuration($configurationManager);
        $this->assertFalse($configuration->isFluidEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnableFluidOverwrittenBySettings()
    {
        $configurationManager = $this->getMockedConfigurationManager(['enableFluid' => '1']);
        $this->fakeConfiguration(['enableFluid' => '0']);
        $configuration = new Configuration($configurationManager);
        $this->assertTrue($configuration->isFluidEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnableObservation()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['enableObservation' => '0']);
        $configuration = new Configuration($configurationManager);
        $this->assertFalse($configuration->isObservationEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnableObservationOverwrittenBySettings()
    {
        $configurationManager = $this->getMockedConfigurationManager(['enableObservation' => '1']);
        $this->fakeConfiguration(['enableObservation' => '0']);
        $configuration = new Configuration($configurationManager);
        $this->assertTrue($configuration->isObservationEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEmptyImgixFluidOptions()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration([]);
        $configuration = new Configuration($configurationManager);
        $this->assertSame([], $configuration->getImgixFluidOptions());
    }

    /**
     * @test
     */
    public function shouldGetImgixFluidOptions()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['imgix.' => ['fluid.' => [
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
            'maxHeight' => '222',
        ]]]);
        $configuration = new Configuration($configurationManager);
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

    /**
     * @test
     */
    public function shouldGetEmptyImgixDefaultUrlParameters()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration([]);
        $configuration = new Configuration($configurationManager);
        $this->assertSame([], $configuration->getImgixDefaultUrlParameters());
    }

    /**
     * @test
     */
    public function shouldGetImgixDefaultUrlParameters()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['imgix.' => ['defaultUrlParameters' => 'q=75&auto=format']]);
        $configuration = new Configuration($configurationManager);
        $this->assertSame(
            [
                'q' => '75',
                'auto' => 'format'
            ],
            $configuration->getImgixDefaultUrlParameters()
        );
    }

    private function fakeConfiguration(array $configuration)
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imgix'] = serialize($configuration);
    }

    /**
     * @param array $configuration
     * @return \PHPUnit_Framework_MockObject_MockObject|ConfigurationManagerInterface
     */
    private function getMockedConfigurationManager(array $configuration)
    {
        /** @var ConfigurationManagerInterface|\PHPUnit_Framework_MockObject_MockObject $configurationManager */
        $configurationManager = $this->getMockBuilder(ConfigurationManagerInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'setContentObject',
                'getContentObject',
                'getConfiguration',
                'setConfiguration',
                'isFeatureEnabled'
            ])
            ->getMock();
        $configurationManager->expects($this->once())->method('getConfiguration')->will(
            $this->returnValue($configuration)
        );
        return $configurationManager;
    }
}
