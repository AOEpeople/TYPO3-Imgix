<?php
namespace Aoe\Imgix\ViewHelpers;

use Aoe\Imgix\TYPO3\Configuration;

class ImgixUrlViewHelperTest extends \PHPUnit_Framework_TestCase
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
            imageUrl => 'test-url'
        ]);
        $this->assertSame('test-url', $this->viewHelper->render());
    }

    /**
     * @test
     */
    public function shouldRenderImageUrlWithImgixHostWhenImgixIsEnabled()
    {
        $this->configuration->expects($this->once())->method('isEnabled')->will(
            $this->returnValue(true)
        );
        $this->configuration->expects($this->once())->method('getHost')->will(
            $this->returnValue('aoe.host')
        );
        $this->viewHelper->setArguments([
            imageUrl => 'test-url'
        ]);
        $this->assertSame('//aoe.host/test-url', $this->viewHelper->render());
    }
}
