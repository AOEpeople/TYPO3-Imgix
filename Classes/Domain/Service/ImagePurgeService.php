<?php

declare(strict_types=1);

namespace Aoe\Imgix\Domain\Service;

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
use Aoe\Imgix\TYPO3\Configuration;
use Aoe\Imgix\TYPO3\PurgeImgixCacheErrorHandler;
use stdClass;

class ImagePurgeService
{
    /**
     * @var string
     */
    public const IMG_PURGE_REQUEST_URL = 'https://api.imgix.com/api/v1/purge';

    private Configuration $configuration;

    private PurgeImgixCacheErrorHandler $errorHandler;

    public function __construct(Configuration $configuration, PurgeImgixCacheErrorHandler $errorHandler)
    {
        $this->configuration = $configuration;
        $this->errorHandler = $errorHandler;
    }

    public function getErrorHandler(): PurgeImgixCacheErrorHandler
    {
        return $this->errorHandler;
    }

    public function purgeImgixCache(string $imageUrl): ImagePurgeResult
    {
        if (!$this->configuration->isApiKeyConfigured()) {
            $this->errorHandler->handleCouldNotPurgeImgixCacheOnInvalidApiKey($imageUrl);
            $result = new ImagePurgeResult();
            $result->markImagePurgeAsFailed();

            return $result;
        }

        $postRequest = new stdClass();
        $postRequest->data = new stdClass();
        $postRequest->data->attributes = new stdClass();
        $postRequest->data->attributes->url = $imageUrl;
        $postRequest->data->type = 'purges';

        $result = $this->doPostRequest($postRequest);
        if (!$result->isSuccessful()) {
            $this->errorHandler->handleCouldNotPurgeImgixCacheOnFailedRestRequest($imageUrl, $result);
        }

        return $result;
    }

    protected function doPostRequest(stdClass $postRequest): ImagePurgeResult
    {
        $postJsonData = (string) json_encode($postRequest);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::IMG_PURGE_REQUEST_URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postJsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->configuration->getApiKey(),
            'Content-Type: application/vnd.api+json',
            'Accept: application/vnd.api+json',
            'Content-Length: ' . strlen($postJsonData),
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);

        $result = new ImagePurgeResult();
        if ($response === false || $responseInfo['http_code'] !== 200) {
            $result->markImagePurgeAsFailed(curl_error($ch), curl_errno($ch), $responseInfo['http_code']);
        } else {
            $result->markImagePurgeAsSuccessful();
        }

        curl_close($ch);

        return $result;
    }
}
