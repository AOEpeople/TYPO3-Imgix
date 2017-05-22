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
        $this->registerArgument('urlParameters', 'array', 'API Parameters to be appended to URL', false);
    }

    /**
     * @return string
     */
    public function render()
    {
        if (false === $this->configuration->isEnabled()) {
            return $this->arguments['imageUrl'];
        }

        $url = '//' . $this->configuration->getHost() . '/' . $this->arguments['imageUrl'];

        if ($this->hasUrlParameters()) {
            $url .= '?' . http_build_query($this->getUrlParameters());
        }

        return $url;
    }

    /**
     * @return array
     */
    private function getUrlParameters()
    {
        $parameters = $this->configuration->getImgixDefaultUrlParameters();
        if ($this->hasArgument('urlParameters')) {
            $parameters = array_merge($parameters, $this->arguments['urlParameters']);
        }
        return $parameters;
    }

    /**
     * @return boolean
     */
    private function hasUrlParameters()
    {
        return sizeof($this->getUrlParameters()) > 0;
    }
}
