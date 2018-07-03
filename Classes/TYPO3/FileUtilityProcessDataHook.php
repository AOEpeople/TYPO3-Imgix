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

use Aoe\Imgix\Rest\RestClient;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtility;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class FileUtilityProcessDataHook implements ExtendedFileUtilityProcessDataHookInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * @param Configuration $configuration
     * @param RestClient $restClient
     */
    public function __construct(Configuration $configuration = null, RestClient $restClient = null)
    {
        if ($configuration === null || $restClient === null) {
            // TYPO3 doesn't support Dependency-Injection in Hooks - so we must create the objects manually
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $configuration = $objectManager->get(Configuration::class);
            $restClient = $objectManager->get(RestClient::class);
        }
        $this->configuration = $configuration;
        $this->restClient = $restClient;
    }

    /**
     * @param string              $action
     * @param array               $cmdArr
     * @param array               $result
     * @param ExtendedFileUtility $parentObject
     * @return void
     * @see \TYPO3\CMS\Core\Utility\File\ExtendedFileUtility::processData
     */
    public function processData_postProcessAction($action, array $cmdArr, array $result, ExtendedFileUtility $parentObject)
    {
        $file = $this->getFile($result);
        if ($this->isPurgingOfImgixCacheRequired($action, $file)) {
            $this->restClient->purgeImgixCache($this->buildImgUrl($file));
        }
    }

    /**
     * @param File $file
     * @return string
     */
    protected function buildImgUrl(File $file)
    {
        return sprintf('http://%s/%s', $this->configuration->getHost(), $file->getPublicUrl());
    }

    /**
     * @param array $result
     * @return File|null
     */
    protected function getFile(array $result)
    {
        if (isset($result[0]) && isset($result[0][0]) && $result[0][0] instanceof File) {
            return $result[0][0];
        }
        return null;
    }

    /**
     * We must purge the imgix-cache, when:
     *  - editor has updated an existing image (by uploading and overwriting an existing image)
     *  - editor has replaced an existing image
     *
     * @param string $action
     * @param File|null $file
     * @return boolean
     */
    protected function isPurgingOfImgixCacheRequired($action, File $file = null)
    {
        if (false === $this->configuration->isEnabled()) {
            return false;
        }
        if ($file === null || $file->getType() !== File::FILETYPE_IMAGE) {
            return false;
        }
        if ($action === 'replace') {
            return true;
        }
        if ($action === 'upload' && count($file->getUpdatedProperties()) > 0) {
            // editor has updated an existing image (by uploading and overwriting an existing image)
            return true;
        }
        return false;
    }
}
