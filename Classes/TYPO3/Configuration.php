<?php
namespace Aoe\Imgix\TYPO3;

use Aoe\Imgix\Utils\ArrayUtils;
use Aoe\Imgix\Utils\TypeUtils;

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
        //'onChangeParamOverride' => TypeUtils::TYPE_STRING,
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
        //'lazyLoadColor' => TypeUtils::TYPE_STRING,
        'throttle' => TypeUtils::TYPE_INTEGER,
        'maxWidth' => TypeUtils::TYPE_INTEGER,
        'maxHeight' => TypeUtils::TYPE_INTEGER,
        //'onLoad' => TypeUtils::TYPE_STRING,
    ];

    /**
     * @var array
     */
    private $configuration = array();

    /**
     */
    public function __construct()
    {
        $this->configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['imgix']);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (boolean)$this->configuration['enabled'];
    }

    /**
     * @return bool
     */
    public function isHostReplacementEnabled()
    {
        return (boolean)$this->configuration['enableHostReplacement'];
    }

    /**
     * @return bool
     */
    public function getHost()
    {
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
