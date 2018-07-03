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

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Lang\LanguageService;

class PurgeImgixCacheErrorHandler
{
    /**
     * @param string $imageUrl
     * @param string $curlErrorMessage
     * @param integer $curlErrorCode
     * @param integer $curlHttpStatusCode
     * @return void
     */
    public function handleCouldNotPurgeImgixCacheOnFailedRestRequest($imageUrl, $curlErrorMessage, $curlErrorCode, $curlHttpStatusCode)
    {
        if (false === is_object($this->getBackendUser())) {
            // do nothing, wenn BE-user is not logged in
            return;
        }

        $messageKey = 'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnFailedRestRequest';
        $message = $this->createErrorMessage($messageKey, $imageUrl);
        $this->addCouldNotPurgeImgixCacheMessageInFlashMessageQueue($message);

        $errorMessageDetails = ['curlHttpStatusCode: ' . $curlHttpStatusCode];
        if (false === empty($curlErrorMessage) && false === empty($curlErrorCode)) {
            $errorMessageDetails[] = ' curlErrorMessage: ' . $curlErrorMessage;
            $errorMessageDetails[] = ' curlErrorCode: ' . $curlErrorCode;
        }
        $errorMessage = 'Could not purge imgix-cache for "'.$imageUrl.'" ('.implode(',', $errorMessageDetails).')!';
        $this->logCouldNotPurgeImgixCacheErrorInSysLog($errorMessage, 1530527897);
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
        $message = $this->createErrorMessage($messageKey, $imageUrl);

        $this->addCouldNotPurgeImgixCacheMessageInFlashMessageQueue($message);
        $this->logCouldNotPurgeImgixCacheErrorInSysLog($message, 1530527898);
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
     */
    protected function addCouldNotPurgeImgixCacheMessageInFlashMessageQueue($message)
    {
        try {
            /** @var $flashMessage FlashMessage */
            $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, '', FlashMessage::ERROR, true);
            /** @var $flashMessageService FlashMessageService */
            $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);

            /** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
            $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $defaultFlashMessageQueue->enqueue($flashMessage);
        } catch (\Exception $e) {
            $errorMessage = 'could not create flash-message ('.$e->getMessage().')';
            $this->getBackendUser()->writelog(3, 0, 2, $e->getCode(), $errorMessage, []);
        }
    }

    /**
     * @param string $messageKey
     * @param string $imageUrl
     * @return string
     */
    private function createErrorMessage($messageKey, $imageUrl)
    {
        $message = $this->getLanguageService()->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:'.$messageKey);
        $message = str_replace('###IMAGE_URL###', $imageUrl, $message);
        return $message;
    }

    /**
     * @param string $errorMessage
     * @param integer $errorCode
     * @return void
     */
    private function logCouldNotPurgeImgixCacheErrorInSysLog($errorMessage, $errorCode)
    {
        $this->getBackendUser()->writelog(3, 0, 2, $errorCode, $errorMessage, []);
    }
}
