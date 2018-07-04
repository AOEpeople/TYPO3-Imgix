<?php
namespace Aoe\Imgix\Controller;

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

use Aoe\Imgix\Domain\Service\ImagePurgeService;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Lang\LanguageService;

class PurgeImgixCacheController extends ActionController
{
    /**
     * @var ImagePurgeService
     */
    private $imagePurgeService;

    /**
     * @param ImagePurgeService $imagePurgeService
     */
    public function __construct(ImagePurgeService $imagePurgeService)
    {
        $this->imagePurgeService = $imagePurgeService;
        parent::__construct();
    }

    /**
     * render form
     */
    public function indexAction()
    {
    }

    /**
     * @param string $imageUrl
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function purgeImgixCacheAction($imageUrl)
    {
        // Override flashMessageQueue in errorHandler:
        // When image-purge fails, than the errorHandler will automatically send a flashMessage with details about the failure
        $this->imagePurgeService->getErrorHandler()->overrideFlashMessageQueue($this->getControllerContext()->getFlashMessageQueue());

        if ($this->imagePurgeService->purgeImgixCache($imageUrl)->isSuccessful()) {
            $messageKey = 'PurgeImgixCacheController.purgeImgixCacheWasSuccessful';
            $message = $this->getLanguageService()->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:'.$messageKey);
            $message = str_replace('###IMAGE_URL###', $imageUrl, $message);
            $this->addFlashMessage($message);
        }

        $this->redirect('index');
    }

    /**
     * Returns LanguageService
     *
     * @return LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}
