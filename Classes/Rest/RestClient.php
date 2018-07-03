<?php
namespace Aoe\Imgix\Rest;

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

use Aoe\Imgix\TYPO3\Configuration;
use Aoe\Imgix\TYPO3\PurgeImgixCacheErrorHandler;
use stdClass;

class RestClient
{
    const IMG_PURGE_REQUEST_URL = 'https://api.imgix.com/v2/image/purger';

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var PurgeImgixCacheErrorHandler
     */
    private $errorHandler;

    /**
     * @param Configuration $configuration
     * @param PurgeImgixCacheErrorHandler $errorHandler
     */
    public function __construct(Configuration $configuration, PurgeImgixCacheErrorHandler $errorHandler)
    {
        $this->configuration = $configuration;
        $this->errorHandler = $errorHandler;
    }

    /**
     * @param string $imageUrl
     * @return void
     */
    public function purgeImgixCache($imageUrl)
    {
        if (false === $this->configuration->isApiKeyConfigured()) {
            $this->errorHandler->handleCouldNotPurgeImgixCacheOnInvalidApiKey($imageUrl);
            return;
        }

        $postRequest = new stdClass();
        $postRequest->url = $imageUrl;

        $result = $this->doPostRequest($postRequest);
        if ($result['isSuccessful'] === false) {
            $this->errorHandler->handleCouldNotPurgeImgixCacheOnFailedRestRequest(
                $imageUrl,
                $result['curlErrorMessage'],
                $result['curlErrorCode'],
                $result['curlHttpStatusCode']
            );
        }
    }

    /**
     * @param stdClass $postRequest
     * @return array
     */
    protected function doPostRequest(stdClass $postRequest)
    {
        $postJsonData = json_encode($postRequest);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::IMG_PURGE_REQUEST_URL);
        curl_setopt($ch, CURLOPT_USERNAME, $this->configuration->getApiKey());
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postJsonData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($postJsonData)]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);

        if ($response === false || $responseInfo['http_code'] !== 200) {
            $result = [
                'isSuccessful' => false,
                'curlErrorMessage' => curl_error($ch),
                'curlErrorCode' => curl_errno($ch),
                'curlHttpStatusCode' => $responseInfo['http_code']
            ];
        } else {
            $result = [
                'isSuccessful' => true
            ];
        }

        curl_close($ch);
        return $result;
    }
}
