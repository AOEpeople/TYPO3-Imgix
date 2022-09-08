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
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class Configuration
{
    protected static array $imgixFluidOptionsTypeMap = [
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
    private $configuration;

    private array $settings;

    public function __construct(ConfigurationManagerInterface $configurationManager, ExtensionConfiguration $extensionConfiguration)
    {
        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'imgix'
        );
        $this->configuration = $extensionConfiguration->get('imgix');
    }

    public function isApiKeyConfigured(): bool
    {
        $apiKey = $this->getApiKey();

        return !empty($apiKey);
    }

    public function isEnabled(): bool
    {
        if (isset($this->settings['enabled']) && $this->settings['enabled'] !== '') {
            return (bool) $this->settings['enabled'];
        }

        return (bool) $this->configuration['enabled'];
    }

    public function isFluidEnabled(): bool
    {
        if (isset($this->settings['enableFluid']) && $this->settings['enableFluid'] !== '') {
            return (bool) $this->settings['enableFluid'];
        }

        return (bool) $this->configuration['enableFluid'];
    }

    public function isObservationEnabled(): bool
    {
        if (isset($this->settings['enableObservation']) && $this->settings['enableObservation'] !== '') {
            return (bool) $this->settings['enableObservation'];
        }

        return (bool) $this->configuration['enableObservation'];
    }

    public function getApiKey(): string
    {
        if (isset($this->settings['apiKey']) && $this->settings['apiKey'] !== '') {
            return (string) $this->settings['apiKey'];
        }

        if (isset($this->configuration['apiKey'])) {
            return (string) $this->configuration['apiKey'];
        }

        return '';
    }

    public function getHost(): string
    {
        if (isset($this->settings['host']) && $this->settings['host'] !== '') {
            return (string) $this->settings['host'];
        }

        return (string) $this->configuration['host'];
    }

    public function getImgixFluidOptions(): array
    {
        if (isset($this->configuration['imgix']['fluid'])) {
            $options = ArrayUtils::filterEmptyValues($this->configuration['imgix']['fluid']);

            return TypeUtils::castTypesByMap(self::$imgixFluidOptionsTypeMap, $options);
        }

        return [];
    }

    public function getImgixDefaultUrlParameters(): array
    {
        if (isset($this->configuration['imgix']['defaultUrlParameters'])) {
            parse_str($this->configuration['imgix']['defaultUrlParameters'], $defaultUrlParameters);

            return $defaultUrlParameters;
        }

        return [];
    }
}
