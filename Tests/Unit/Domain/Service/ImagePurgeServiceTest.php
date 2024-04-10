<?php

declare(strict_types=1);

namespace Aoe\Imgix\Tests\Domain\Service;

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
use Aoe\Imgix\Domain\Service\ImagePurgeService;
use Aoe\Imgix\TYPO3\Configuration;
use Aoe\Imgix\TYPO3\PurgeImgixCacheErrorHandler;
use stdClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ImagePurgeServiceTest extends UnitTestCase
{
    private Configuration $configuration;

    private PurgeImgixCacheErrorHandler $errorHandler;

    private ImagePurgeService $imagePurgeService;

    protected function setUp(): void
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $this->errorHandler = $this->getMockBuilder(PurgeImgixCacheErrorHandler::class)->disableOriginalConstructor()->getMock();
        $this->imagePurgeService = $this
            ->getMockBuilder(ImagePurgeService::class)
            ->setConstructorArgs([$this->configuration, $this->errorHandler])
            ->onlyMethods(['doPostRequest'])
            ->getMock();
    }

    public function testShouldNotPurgeImgixCacheOnInvalidApiKey(): void
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';

        $this->configuration->expects(self::once())->method('isApiKeyConfigured')->willReturn(false);
        $this->errorHandler->expects(self::once())->method('handleCouldNotPurgeImgixCacheOnInvalidApiKey')->with($imageUrl);
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnFailedRestRequest');
        $this->imagePurgeService->expects(self::never())->method('doPostRequest');
        $this->assertFalse($this->imagePurgeService->purgeImgixCache($imageUrl)->isSuccessful());
    }

    public function testShouldNotPurgeImgixCacheOnFailedRestRequest(): void
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';
        $postRequest = new stdClass();
        $postRequest->data = new stdClass();
        $postRequest->data->attributes = new stdClass();
        $postRequest->data->attributes->url = $imageUrl;
        $postRequest->data->type = 'purges';
        $result = new ImagePurgeResult();
        $result->markImagePurgeAsFailed('curlError', 28, 503);

        $this->configuration->expects(self::once())->method('isApiKeyConfigured')->willReturn(true);
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnInvalidApiKey');
        $this->errorHandler->expects(self::once())->method('handleCouldNotPurgeImgixCacheOnFailedRestRequest')->with($imageUrl, $result);
        $this->imagePurgeService->expects(self::once())->method('doPostRequest')->with($postRequest)->willReturn($result);
        $this->assertFalse($this->imagePurgeService->purgeImgixCache($imageUrl)->isSuccessful());
    }

    public function testShouldPurgeImgixCache(): void
    {
        $imageUrl = 'http://congstar.imgix.com/directory/image.png';
        $postRequest = new stdClass();
        $postRequest->data = new stdClass();
        $postRequest->data->attributes = new stdClass();
        $postRequest->data->attributes->url = $imageUrl;
        $postRequest->data->type = 'purges';
        $result = new ImagePurgeResult();
        $result->markImagePurgeAsSuccessful();

        $this->configuration->expects(self::once())->method('isApiKeyConfigured')->willReturn(true);
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnInvalidApiKey');
        $this->errorHandler->expects(self::never())->method('handleCouldNotPurgeImgixCacheOnFailedRestRequest');
        $this->imagePurgeService->expects(self::once())->method('doPostRequest')->with($postRequest)->willReturn($result);
        $this->assertTrue($this->imagePurgeService->purgeImgixCache($imageUrl)->isSuccessful());
    }
}
