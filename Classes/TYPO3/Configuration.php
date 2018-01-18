<?php
namespace Aoe\Imgix\TYPO3;

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
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'imgix'
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
    public function isFluidEnabled()
    {
        if (isset($this->settings['enableFluid']) && '' !== $this->settings['enableFluid']) {
            return (boolean)$this->settings['enableFluid'];
        }
        return (boolean)$this->configuration['enableFluid'];
    }

    /**
     * @return bool
     */
    public function isObservationEnabled()
    {
        if (isset($this->settings['enableObservation']) && '' !== $this->settings['enableObservation']) {
            return (boolean)$this->settings['enableObservation'];
        }
        return (boolean)$this->configuration['enableObservation'];
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

    /**
     * @return array
     */
    public function getImgixDefaultUrlParameters()
    {
        if (isset($this->configuration['imgix.']['defaultUrlParameters'])) {
            parse_str($this->configuration['imgix.']['defaultUrlParameters'], $defaultUrlParameters);
            return $defaultUrlParameters;
        }
        return [];
    }
}
