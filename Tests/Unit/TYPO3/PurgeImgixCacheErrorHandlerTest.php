<?php
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

use Aoe\Imgix\TYPO3\PurgeImgixCacheErrorHandler;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Lang\LanguageService;
use PHPUnit_Framework_MockObject_MockObject;

class PurgeImgixCacheErrorHandlerTest extends UnitTestCase
{
    /**
     * @var BackendUserAuthentication|PHPUnit_Framework_MockObject_MockObject
     */
    private $backendUser;

    /**
     * @var FlashMessageQueue|PHPUnit_Framework_MockObject_MockObject
     */
    private $flashMessageQueue;

    /**
     * @var LanguageService|PHPUnit_Framework_MockObject_MockObject
     */
    private $languageService;

    /**
     * @var PurgeImgixCacheErrorHandler|PHPUnit_Framework_MockObject_MockObject
     */
    private $errorHandler;

    /**
     * set up the test
     */
    public function setUp()
    {
        $this->backendUser = $this->getMockBuilder(BackendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $this->flashMessageQueue = $this->getMockBuilder(FlashMessageQueue::class)->disableOriginalConstructor()->getMock();
        $this->languageService = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();

        $flashMessageService = $this->getMockBuilder(FlashMessageService::class)->disableOriginalConstructor()->getMock();
        $flashMessageService->expects(self::once())->method('getMessageQueueByIdentifier')->willReturn($this->flashMessageQueue);

        $this->errorHandler = $this
            ->getMockBuilder(PurgeImgixCacheErrorHandler::class)
            ->setConstructorArgs([$flashMessageService])
            ->setMethods(['getBackendUser', 'getLanguageService', 'createFlashMessage'])
            ->getMock();
        $this->errorHandler->expects(self::any())->method('getBackendUser')->willReturn($this->backendUser);
        $this->errorHandler->expects(self::any())->method('getLanguageService')->willReturn($this->languageService);
    }

    /**
     * @test
     */
    public function shouldHandleCouldNotPurgeImgixCacheOnFailedRestRequest()
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';
        $curlMessage = 'curl-error';
        $curlCode = 28;
        $curlHttpStatusCode = 401;

        $this->languageService
            ->expects(self::once())
            ->method('sL')
            ->with('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:'.
                'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnFailedRestRequest')
            ->willReturn('Could not purge imgix-cache for "###IMAGE_URL###"');

        $flashMessage = 'Could not purge imgix-cache for "'.$imageUrl.'"';
        $flashMessageObj = $this->getMockBuilder(FlashMessage::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler
            ->expects(self::once())
            ->method('createFlashMessage')
            ->with($flashMessage)
            ->willReturn($flashMessageObj);
        $this->flashMessageQueue->expects(self::once())->method('enqueue')->with($flashMessageObj);

        $sysLogMessage = 'Could not purge imgix-cache for "'.$imageUrl.'"';
        $sysLogMessage .= ' (curlHttpStatusCode: 401, curlErrorMessage: curl-error, curlErrorCode: 28)!';
        $this->backendUser->expects(self::once())->method('writelog')->with(3, 0, 2, 1530527897, $sysLogMessage, []);

        $this->errorHandler->handleCouldNotPurgeImgixCacheOnFailedRestRequest($imageUrl, $curlMessage, $curlCode, $curlHttpStatusCode);
    }

    /**
     * @test
     */
    public function shouldHandleCouldNotPurgeImgixCacheOnInvalidApiKey()
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';

        $this->languageService
            ->expects(self::once())
            ->method('sL')
            ->with('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:'.
                'PurgeImgixCacheErrorHandler.couldNotPurgeImgixCacheOnInvalidApiKey')
            ->willReturn('Could not purge imgix-cache for "###IMAGE_URL###"');

        $expectedMessage = 'Could not purge imgix-cache for "'.$imageUrl.'"';
        $flashMessageObj = $this->getMockBuilder(FlashMessage::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler
            ->expects(self::once())
            ->method('createFlashMessage')
            ->with($expectedMessage)
            ->willReturn($flashMessageObj);
        $this->flashMessageQueue->expects(self::once())->method('enqueue')->with($flashMessageObj);
        $this->backendUser->expects(self::once())->method('writelog')->with(3, 0, 2, 1530527898, $expectedMessage, []);

        $this->errorHandler->handleCouldNotPurgeImgixCacheOnInvalidApiKey($imageUrl);
    }
}
