<?php
namespace Aoe\Imgix\ViewHelpers;

use Aoe\Imgix\TYPO3\Configuration;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

class ImgixUrlViewHelper extends AbstractViewHelper
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('imageUrl', 'string', 'URL to image file', true);
    }

    /**
     * @return string
     */
    public function render()
    {
        if (false === $this->configuration->isEnabled()) {
            return $this->arguments['imageUrl'];
        }
        return '//' . $this->configuration->getHost() . '/' . $this->arguments['imageUrl'];
    }
}
