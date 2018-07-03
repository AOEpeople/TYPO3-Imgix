<?php
namespace Aoe\Imgix\Tests\Rest;

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
use Aoe\Imgix\TYPO3\Configuration;
use Aoe\Imgix\TYPO3\PurgeImgixCacheErrorHandler;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit_Framework_MockObject_MockObject;
use stdClass;

class RestClientTest extends UnitTestCase
{
    /**
     * @var Configuration|PHPUnit_Framework_MockObject_MockObject
     */
    private $configuration;

    /**
     * @var PurgeImgixCacheErrorHandler|PHPUnit_Framework_MockObject_MockObject
     */
    private $errorHandler;

    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * set up the test
     */
    public function setUp()
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler = $this->getMockBuilder(PurgeImgixCacheErrorHandler::class)->disableOriginalConstructor()->getMock();
        $this->restClient = $this
            ->getMockBuilder(RestClient::class)
            ->setConstructorArgs([$this->configuration, $this->errorHandler])
            ->setMethods(['doPostRequest'])
            ->getMock();
    }

    /**
     * @test
     */
    public function shouldNotPurgeImgixCacheOnInvalidApiKey()
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';

        $this->configuration->expects(self::once())->method('isApiKeyConfigured')->willReturn(false);
        $this->errorHandler->expects(self::once())->method('handleCouldNotPurgeImgixCacheOnInvalidApiKey')->with($imageUrl);
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnFailedRestRequest');
        $this->restClient->expects(self::never())->method('doPostRequest');
        $this->restClient->purgeImgixCache($imageUrl);
    }

    /**
     * @test
     */
    public function shouldNotPurgeImgixCacheOnFailedRestRequest()
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';
        $postRequest = new stdClass();
        $postRequest->url = $imageUrl;
        $result = [
            'isSuccessful' => false,
            'curlErrorMessage' => 'curlError',
            'curlErrorCode' => 28,
            'curlHttpStatusCode' => 503
        ];

        $this->configuration->expects(self::once())->method('isApiKeyConfigured')->willReturn(true);
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnInvalidApiKey');
        $this->errorHandler
            ->expects(self::once())
            ->method('handleCouldNotPurgeImgixCacheOnFailedRestRequest')
            ->with($imageUrl, $result['curlErrorMessage'], $result['curlErrorCode'], $result['curlHttpStatusCode']);
        $this->restClient->expects(self::once())->method('doPostRequest')->with($postRequest)->willReturn($result);
        $this->restClient->purgeImgixCache($imageUrl);
    }

    /**
     * @test
     */
    public function shouldPurgeImgixCache()
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';
        $postRequest = new stdClass();
        $postRequest->url = $imageUrl;
        $result = ['isSuccessful' => true];

        $this->configuration->expects(self::once())->method('isApiKeyConfigured')->willReturn(true);
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnInvalidApiKey');
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnFailedRestRequest');
        $this->restClient->expects(self::once())->method('doPostRequest')->with($postRequest)->willReturn($result);
        $this->restClient->purgeImgixCache($imageUrl);
    }
}
