<?php

namespace Aoe\Imgix\Domain\Model;

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
class ImagePurgeResult
{
    private bool $isSuccessful;

    private string $curlErrorMessage;

    private int $curlErrorCode;

    private int $curlHttpStatusCode;

    public function getCurlErrorMessage(): string
    {
        return $this->curlErrorMessage;
    }

    public function getCurlErrorCode(): int
    {
        return $this->curlErrorCode;
    }

    public function getCurlHttpStatusCode(): int
    {
        return $this->curlHttpStatusCode;
    }

    public function hasCurlErrorMessage(): bool
    {
        return !empty($this->curlErrorMessage);
    }

    public function hasCurlErrorCode(): bool
    {
        return !empty($this->curlErrorCode);
    }

    public function isSuccessful(): bool
    {
        return $this->isSuccessful;
    }

    /**
     * image-purge was not successful
     *
     * @param string  $curlErrorMessage
     * @param integer $curlErrorCode
     * @param integer $curlHttpStatusCode
     */
    public function markImagePurgeAsFailed($curlErrorMessage = '', $curlErrorCode = 0, $curlHttpStatusCode = 0): void
    {
        $this->isSuccessful = false;
        $this->curlErrorMessage = $curlErrorMessage;
        $this->curlErrorCode = $curlErrorCode;
        $this->curlHttpStatusCode = $curlHttpStatusCode;
    }

    /**
     * image-purge was successful
     */
    public function markImagePurgeAsSuccessful(): void
    {
        $this->isSuccessful = true;
    }
}
