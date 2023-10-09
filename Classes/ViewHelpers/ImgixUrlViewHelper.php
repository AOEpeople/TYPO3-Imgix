<?php

declare(strict_types=1);

namespace Aoe\Imgix\ViewHelpers;

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

use Aoe\Imgix\TYPO3\Configuration;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class ImgixUrlViewHelper extends AbstractViewHelper
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('imageUrl', 'string', 'URL to image file', true);
        $this->registerArgument('urlParameters', 'array', 'API Parameters to be appended to URL', false);
    }

    public function render(): string
    {
        if (!$this->configuration->isEnabled()) {
            return $this->arguments['imageUrl'];
        }

        if (str_starts_with($this->arguments['imageUrl'], 'https')) {
            $httpHost = GeneralUtility::getIndpEnv('TYPO3_REQUEST_HOST');
            $url = str_replace($httpHost, '//' . $this->configuration->getHost(), $this->arguments['imageUrl']);
        } else {
            $url = '//' . $this->configuration->getHost() . '/' . $this->arguments['imageUrl'];
        }

        if ($this->hasUrlParameters()) {
            $url .= '?' . http_build_query($this->getUrlParameters());
        }

        return $url;
    }

    private function getUrlParameters(): array
    {
        $parameters = $this->configuration->getImgixDefaultUrlParameters();
        if ($this->hasArgument('urlParameters')) {
            return array_merge($parameters, $this->arguments['urlParameters']);
        }

        return $parameters;
    }

    private function hasUrlParameters(): bool
    {
        return $this->getUrlParameters() !== [];
    }
}
