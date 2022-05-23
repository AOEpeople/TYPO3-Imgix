<?php
namespace Aoe\Imgix\TYPO3;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2022 AOE GmbH <dev@aoe.com>
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

use Aoe\Imgix\Domain\Service\ImagePurgeService;
use TYPO3\CMS\Core\Resource\Event\AfterFileCommandProcessedEvent;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;

class AfterFileCommandProcessedEventListener
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ImagePurgeService
     */
    private $imagePurgeService;

    /**
     * @param Configuration     $configuration
     * @param ImagePurgeService $imagePurgeService
     */
    public function __construct(Configuration $configuration, ImagePurgeService $imagePurgeService)
    {
        $this->configuration = $configuration;
        $this->imagePurgeService = $imagePurgeService;
    }

    public function __invoke(AfterFileCommandProcessedEvent $event): void
    {
        $file = $this->getFile($event->getResult());
        if ($this->isPurgingOfImgixCacheRequired($event->getCommand(), $event->getConflictMode(), $file)) {
            $this->imagePurgeService->purgeImgixCache($this->buildImgUrl($file));
        }
    }

    /**
     * @param File $file
     * @return string
     */
    protected function buildImgUrl(File $file)
    {
        $imagePath = ltrim($file->getPublicUrl(), '/');
        $host = rtrim($this->configuration->getHost(), '/');
        return sprintf('http://%s/%s', $host, $imagePath);
    }

    /**
     * @param mixed $result
     * @return File|null
     */
    protected function getFile($result): ?File
    {
        if (is_array($result) && isset($result[0]) && $result[0] instanceof File) {
            return $result[0];
        }
        return null;
    }

    /**
     * @param string $action
     * @param File|null $file
     * @return boolean
     */
    protected function isPurgingOfImgixCacheRequired(array $command, string $conflictMode, File $file = null)
    {
        if ($file === null || $file->getType() !== File::FILETYPE_IMAGE) {
            return false;
        }
        if (isset($command['upload']) && $conflictMode === DuplicationBehavior::REPLACE) {
            // editor has updated an existing image (by uploading and overwriting an existing image)
            return true;
        }
        if (isset($command['replace']) &&
            isset($command['replace']['keepFilename']) &&
            (boolean) $command['replace']['keepFilename'] === true) {
            // editor has replaced an existing image and current image-filename should be kept
            return true;
        }
        return false;
    }
}
