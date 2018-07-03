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
use TYPO3\CMS\Lang\LanguageService;
use PHPUnit_Framework_MockObject_MockObject;

class PurgeImgixCacheErrorHandlerTest extends UnitTestCase
{
    /**
     * @var BackendUserAuthentication|PHPUnit_Framework_MockObject_MockObject
     */
    private $backendUser;

    /**
     * @var LanguageService|PHPUnit_Framework_MockObject_MockObject
     */
    private $languageService;

    /**
     * @var PurgeImgixCacheErrorHandler
     */
    private $errorHandler;

    /**
     * set up the test
     */
    public function setUp()
    {
        $this->backendUser = $this->getMockBuilder(BackendUserAuthentication::class)->disableOriginalConstructor()->getMock();
        $this->languageService = $this->getMockBuilder(LanguageService::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler = $this
            ->getMockBuilder(PurgeImgixCacheErrorHandler::class)
            ->setMethods(['getBackendUser', 'getLanguageService', 'addCouldNotPurgeImgixCacheMessageInFlashMessageQueue'])
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
        $this->errorHandler->expects(self::once())->method('addCouldNotPurgeImgixCacheMessageInFlashMessageQueue')->with($flashMessage);

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
        $this->errorHandler->expects(self::once())->method('addCouldNotPurgeImgixCacheMessageInFlashMessageQueue')->with($expectedMessage);
        $this->backendUser->expects(self::once())->method('writelog')->with(3, 0, 2, 1530527898, $expectedMessage, []);

        $this->errorHandler->handleCouldNotPurgeImgixCacheOnInvalidApiKey($imageUrl);
    }
}
