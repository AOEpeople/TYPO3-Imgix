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
    /**
     * @var boolean
     */
    private $isSuccessful;

    /**
     * @var string
     */
    private $curlErrorMessage;

    /**
     * @var integer
     */
    private $curlErrorCode;

    /**
     * @var integer
     */
    private $curlHttpStatusCode;

    /**
     * @return string
     */
    public function getCurlErrorMessage()
    {
        return $this->curlErrorMessage;
    }

    /**
     * @return integer
     */
    public function getCurlErrorCode()
    {
        return $this->curlErrorCode;
    }

    /**
     * @return integer
     */
    public function getCurlHttpStatusCode()
    {
        return $this->curlHttpStatusCode;
    }

    /**
     * @return boolean
     */
    public function hasCurlErrorMessage()
    {
        return (false === empty($this->curlErrorMessage));
    }

    /**
     * @return boolean
     */
    public function hasCurlErrorCode()
    {
        return (false === empty($this->curlErrorCode));
    }

    /**
     * @return boolean
     */
    public function isSuccessful()
    {
        return $this->isSuccessful;
    }

    /**
     * image-purge was not successful
     * @param string $curlErrorMessage
     * @param integer $curlErrorCode
     * @param integer $curlHttpStatusCode
     */
    public function markImagePurgeAsFailed($curlErrorMessage = '', $curlErrorCode = 0, $curlHttpStatusCode = 0)
    {
        $this->isSuccessful = false;
        $this->curlErrorMessage = $curlErrorMessage;
        $this->curlErrorCode = $curlErrorCode;
        $this->curlHttpStatusCode = $curlHttpStatusCode;
    }

    /**
     * image-purge was successful
     */
    public function markImagePurgeAsSuccessful()
    {
       $this->isSuccessful = true;
    }
}
