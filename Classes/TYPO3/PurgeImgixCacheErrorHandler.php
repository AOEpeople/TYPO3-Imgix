<?php

declare(strict_types=1);

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
use Exception;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PurgeImgixCacheErrorHandler
{
    private FlashMessageQueue $flashMessageQueue;

    public function __construct(FlashMessageService $flashMessageService)
    {
        $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
    }

    public function handleCouldNotPurgeImgixCacheOnFailedRestRequest(string $imageUrl, ImagePurgeResult $result): void
    {
        if (!is_object($this->getBackendUser())) {
            // do nothing, wenn BE-user is not logged in
            return;
        }

        $errorMessageDetails = ['curlHttpStatusCode: ' . $result->getCurlHttpStatusCode()];
        if ($result->hasCurlErrorMessage() && $result->hasCurlErrorCode()) {
            $errorMessageDetails[] = ' curlErrorMessage: ' . $result->getCurlErrorMessage();
            $errorMessageDetails[] = ' curlErrorCode: ' . $result->getCurlErrorCode();
        }

        $messageKey = 'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnFailedRestRequest';
        $message = $this->getLanguageService()
            ->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:' . $messageKey);
        $message = str_replace('###IMAGE_URL###', $imageUrl, $message);
        $message = str_replace('###ERROR_DETAILS###', implode(',', $errorMessageDetails), $message);

        $this->addMessageToFlashMessageQueue($message);
        $this->logErrorInSysLog($message, 15305);
    }

    public function handleCouldNotPurgeImgixCacheOnInvalidApiKey(string $imageUrl): void
    {
        if (!is_object($this->getBackendUser())) {
            // do nothing, wenn BE-user is not logged in
            return;
        }

        $messageKey = 'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnInvalidApiKey';
        $message = $this->getLanguageService()
            ->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:' . $messageKey);
        $message = str_replace('###IMAGE_URL###', $imageUrl, $message);

        $this->addMessageToFlashMessageQueue($message);
        $this->logErrorInSysLog($message, 15306);
    }

    /**
     * When errorHandler is used in a 'extbase-controller-context', then we must use the flashMessageQueue from the extbase-controller
     */
    public function overrideFlashMessageQueue(FlashMessageQueue $flashMessageQueue): void
    {
        $this->flashMessageQueue = $flashMessageQueue;
    }

    protected function getBackendUser(): ?BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }

    protected function createFlashMessage(string $message): FlashMessage
    {
        /** @var FlashMessage $flashMessage */
        $flashMessage = GeneralUtility::makeInstance(FlashMessage::class, $message, '', FlashMessage::ERROR, true);

        return $flashMessage;
    }

    private function addMessageToFlashMessageQueue(string $message): void
    {
        try {
            $this->flashMessageQueue->enqueue($this->createFlashMessage($message));
        } catch (Exception $exception) {
            $errorMessage = 'could not create flash-message (' . $exception->getMessage() . ')';
            $this->logErrorInSysLog($errorMessage, $exception->getCode());
        }
    }

    private function logErrorInSysLog(string $errorMessage, int $errorCode): void
    {
        $backendUser = $this->getBackendUser();

        if ($backendUser !== null) {
            $backendUser->writelog(3, 0, 2, $errorCode, $errorMessage, []);
        }
    }
}
