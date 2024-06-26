<?php

declare(strict_types=1);

namespace Aoe\Imgix\Tests\TYPO3;

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
use Aoe\Imgix\TYPO3\PurgeImgixCacheErrorHandler;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class PurgeImgixCacheErrorHandlerTest extends UnitTestCase
{
    private BackendUserAuthentication $backendUser;

    private FlashMessageQueue $flashMessageQueue;

    private LanguageService $languageService;

    private PurgeImgixCacheErrorHandler $errorHandler;

    protected function setUp(): void
    {
        $this->backendUser = $this->getMockBuilder(BackendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $this->flashMessageQueue = $this->getMockBuilder(FlashMessageQueue::class)->disableOriginalConstructor()->getMock();
        $this->languageService = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();

        $flashMessageService = $this->getMockBuilder(FlashMessageService::class)->disableOriginalConstructor()->getMock();
        $flashMessageService->expects(self::once())->method('getMessageQueueByIdentifier')->willReturn($this->flashMessageQueue);

        $this->errorHandler = $this
            ->getMockBuilder(PurgeImgixCacheErrorHandler::class)
            ->setConstructorArgs([$flashMessageService])
            ->onlyMethods(['getBackendUser', 'getLanguageService', 'createFlashMessage'])
            ->getMock();
        $this->errorHandler->expects(self::any())->method('getBackendUser')->willReturn($this->backendUser);
        $this->errorHandler->expects(self::any())->method('getLanguageService')->willReturn($this->languageService);
    }

    public function testShouldHandleCouldNotPurgeImgixCacheOnFailedRestRequest(): void
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';
        $result = new ImagePurgeResult();
        $result->markImagePurgeAsFailed('curl-error', 28, 401);

        $this->languageService
            ->expects(self::once())
            ->method('sL')
            ->with('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:' .
                'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnFailedRestRequest')
            ->willReturn('Could not purge imgix-cache for "###IMAGE_URL###"');

        $expectedMessage = 'Could not purge imgix-cache for "' . $imageUrl . '"';
        $flashMessageObj = $this->getMockBuilder(FlashMessage::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler
            ->expects(self::once())
            ->method('createFlashMessage')
            ->with($expectedMessage)
            ->willReturn($flashMessageObj);
        $this->flashMessageQueue->expects(self::once())->method('enqueue')->with($flashMessageObj);
        $this->backendUser->expects(self::once())->method('writelog')->with(3, 0, 2, 15305, $expectedMessage, []);

        $this->errorHandler->handleCouldNotPurgeImgixCacheOnFailedRestRequest($imageUrl, $result);
    }

    public function testShouldHandleCouldNotPurgeImgixCacheOnInvalidApiKey(): void
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';

        $this->languageService
            ->expects(self::once())
            ->method('sL')
            ->with('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:' .
                'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnInvalidApiKey')
            ->willReturn('Could not purge imgix-cache for "###IMAGE_URL###"');

        $expectedMessage = 'Could not purge imgix-cache for "' . $imageUrl . '"';
        $flashMessageObj = $this->getMockBuilder(FlashMessage::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler
            ->expects(self::once())
            ->method('createFlashMessage')
            ->with($expectedMessage)
            ->willReturn($flashMessageObj);
        $this->flashMessageQueue->expects(self::once())->method('enqueue')->with($flashMessageObj);
        $this->backendUser->expects(self::once())->method('writelog')->with(3, 0, 2, 15306, $expectedMessage, []);

        $this->errorHandler->handleCouldNotPurgeImgixCacheOnInvalidApiKey($imageUrl);
    }
}
