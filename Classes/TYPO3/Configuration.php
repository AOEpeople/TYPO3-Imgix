<?php
namespace Aoe\Imgix\TYPO3;

use Aoe\Imgix\Utils\ArrayUtils;
use Aoe\Imgix\Utils\TypeUtils;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class Configuration
{
    /**
     * @var array
     */
    protected static $imgixFluidOptionsTypeMap = [
        'fluidClass' => TypeUtils::TYPE_STRING,
        'updateOnResize' => TypeUtils::TYPE_BOOLEAN,
        'updateOnResizeDown' => TypeUtils::TYPE_BOOLEAN,
        'updateOnPinchZoom' => TypeUtils::TYPE_BOOLEAN,
        'highDPRAutoScaleQuality' => TypeUtils::TYPE_BOOLEAN,
        'autoInsertCSSBestPractices' => TypeUtils::TYPE_BOOLEAN,
        'fitImgTagToContainerWidth' => TypeUtils::TYPE_BOOLEAN,
        'fitImgTagToContainerHeight' => TypeUtils::TYPE_BOOLEAN,
        'pixelStep' => TypeUtils::TYPE_INTEGER,
        'ignoreDPR' => TypeUtils::TYPE_BOOLEAN,
        'debounce' => TypeUtils::TYPE_INTEGER,
        'lazyLoad' => TypeUtils::TYPE_BOOLEAN,
        'lazyLoadOffsetVertical' => TypeUtils::TYPE_INTEGER,
        'lazyLoadOffsetHorizontal' => TypeUtils::TYPE_INTEGER,
        'throttle' => TypeUtils::TYPE_INTEGER,
        'maxWidth' => TypeUtils::TYPE_INTEGER,
        'maxHeight' => TypeUtils::TYPE_INTEGER,
    ];

    /**
     * @var array
     */
    private $configuration = array();

    /**
     * @var array
     */
    private $settings;

    /**
     * @param ConfigurationManagerInterface $configurationManager
     */
    public function __construct(ConfigurationManagerInterface $configurationManager)
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS
        );
        $this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imgix']);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        if (isset($this->settings['enabled']) && '' !== $this->settings['enabled']) {
            return (boolean)$this->settings['enabled'];
        }
        return (boolean)$this->configuration['enabled'];
    }

    /**
     * @return bool
     */
    public function isHostReplacementEnabled()
    {
        if (isset($this->settings['enableHostReplacement']) && '' !== $this->settings['enableHostReplacement']) {
            return (boolean)$this->settings['enableHostReplacement'];
        }
        return (boolean)$this->configuration['enableHostReplacement'];
    }

    /**
     * @return bool
     */
    public function getHost()
    {
        if (isset($this->settings['host']) && '' !== $this->settings['host']) {
            return (string)$this->settings['host'];
        }
        return (string)$this->configuration['host'];
    }

    /**
     * @return array
     */
    public function getImgixFluidOptions()
    {
        if (isset($this->configuration['imgix.']['fluid.'])) {
            $options = ArrayUtils::filterEmptyValues($this->configuration['imgix.']['fluid.']);
            $options = TypeUtils::castTypesByMap(self::$imgixFluidOptionsTypeMap, $options);
            return $options;
        }
        return [];
    }
}
