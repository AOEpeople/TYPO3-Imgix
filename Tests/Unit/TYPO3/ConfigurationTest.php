<?php
namespace Aoe\Imgix\TYPO3;

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
        $this->fakeConfiguration(['host' => 'mysubdomain.imgix.net']);
        $configuration = new Configuration();
        $this->assertSame('mysubdomain.imgix.net', $configuration->getHost());
    }

    /**
     * @test
     */
    public function shouldGetEnabled()
    {
        $this->fakeConfiguration(['enabled' => '1']);
        $configuration = new Configuration();
        $this->assertTrue($configuration->isEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEnableHostReplacement()
    {
        $this->fakeConfiguration(['enableHostReplacement' => '0']);
        $configuration = new Configuration();
        $this->assertFalse($configuration->isHostReplacementEnabled());
    }

    /**
     * @test
     */
    public function shouldGetEmptyImgixFluidOptions()
    {
        $this->fakeConfiguration([]);
        $configuration = new Configuration();
        $this->assertSame([], $configuration->getImgixFluidOptions());
    }

    /**
     * @test
     */
    public function shouldGetImgixFluidOptions()
    {
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
        $configuration = new Configuration();
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
}
