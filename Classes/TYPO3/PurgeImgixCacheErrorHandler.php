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

use Aoe\Imgix\Domain\Model\ImagePurgeResult;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PurgeImgixCacheErrorHandler
{
    /**
     * @var FlashMessageQueue
     */
    private $flashMessageQueue;

    /**
     * @param FlashMessageService $flashMessageService
     */
    public function __construct(FlashMessageService $flashMessageService)
    {
        $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
    }

    /**
     * @param string $imageUrl
     * @param ImagePurgeResult $result
     * @return void
     */
    public function handleCouldNotPurgeImgixCacheOnFailedRestRequest($imageUrl, ImagePurgeResult $result)
    {
        if (false === is_object($this->getBackendUser())) {
            // do nothing, wenn BE-user is not logged in
            return;
        }

        $errorMessageDetails = ['curlHttpStatusCode: ' . $result->getCurlHttpStatusCode()];
        if ($result->hasCurlErrorMessage() && $result->hasCurlErrorCode()) {
            $errorMessageDetails[] = ' curlErrorMessage: ' . $result->getCurlErrorMessage();
            $errorMessageDetails[] = ' curlErrorCode: ' . $result->getCurlErrorCode();
        }
        $messageKey = 'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnFailedRestRequest';
        $message = $this->getLanguageService()->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:'.$messageKey);
        $message = str_replace('###IMAGE_URL###', $imageUrl, $message);
        $message = str_replace('###ERROR_DETAILS###', implode(',', $errorMessageDetails), $message);

        $this->addMessageToFlashMessageQueue($message);
        $this->logErrorInSysLog($message, 1530527897);
    }

    /**
     * @param $imageUrl
     */
    public function handleCouldNotPurgeImgixCacheOnInvalidApiKey($imageUrl)
    {
        if (false === is_object($this->getBackendUser())) {
            // do nothing, wenn BE-user is not logged in
            return;
        }

        $messageKey = 'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnInvalidApiKey';
        $message = $this->getLanguageService()->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:'.$messageKey);
        $message = str_replace('###IMAGE_URL###', $imageUrl, $message);

        $this->addMessageToFlashMessageQueue($message);
        $this->logErrorInSysLog($message, 1530527898);
    }

    /**
     * When errorHandler is used in a 'extbase-controller-context', than we must use the flashMessageQueue from the extbase-controller
     * @param FlashMessageQueue $flashMessageQueue
     */
    public function overrideFlashMessageQueue(FlashMessageQueue $flashMessageQueue)
    {
        $this->flashMessageQueue = $flashMessageQueue;
    }

    /**
     * @return BackendUserAuthentication|null
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
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

    /**
     * @param string $message
     * @return FlashMessage
     */
    protected function createFlashMessage($message)
    {
        /** @var $flashMessage FlashMessage */
        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, '', FlashMessage::ERROR, true);
        return $flashMessage;
    }

    /**
     * @param string $message
     */
    private function addMessageToFlashMessageQueue($message)
    {
        try {
            $this->flashMessageQueue->enqueue($this->createFlashMessage($message));
        } catch (\Exception $e) {
            $errorMessage = 'could not create flash-message ('.$e->getMessage().')';
            $this->logErrorInSysLog($errorMessage, $e->getCode());
        }
    }

    /**
     * @param string $errorMessage
     * @param integer $errorCode
     * @return void
     */
    private function logErrorInSysLog($errorMessage, $errorCode)
    {
        $this->getBackendUser()->writelog(3, 0, 2, $errorCode, $errorMessage, []);
    }
}
