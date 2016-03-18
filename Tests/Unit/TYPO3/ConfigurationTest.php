<?php
namespace Aoe\Imgix\TYPO3;

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
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
    public function shouldGetEnableHostReplacement()
    {
        $configurationManager = $this->getMockedConfigurationManager([]);
        $this->fakeConfiguration(['enableHostReplacement' => '0']);
        $configuration = new Configuration($configurationManager);
        $this->assertFalse($configuration->isHostReplacementEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnableHostReplacementOverwrittenBySettings()
    {
        $configurationManager = $this->getMockedConfigurationManager(['enableHostReplacement' => '1']);
        $this->fakeConfiguration(['enableHostReplacement' => '0']);
        $configuration = new Configuration($configurationManager);
        $this->assertTrue($configuration->isHostReplacementEnabled());
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
