<?php
namespace Aoe\Imgix\Tests\Domain\Model;

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
use Nimut\TestingFramework\TestCase\UnitTestCase;

class ImagePurgeResultTest extends UnitTestCase
{
    /**
     * @var ImagePurgeResult
     */
    private $imagePurgeResult;

    /**
     * set up the test
     */
    public function setUp()
    {
        $this->imagePurgeResult = new ImagePurgeResult();
    }

    /**
     * @test
     */
    public function shouldCheckThatResultHasNoCurlErrorMessage()
    {
        $this->assertFalse($this->imagePurgeResult->hasCurlErrorMessage());
    }

    /**
     * @test
     */
    public function shouldCheckThatResultHasCurlErrorMessage()
    {
        $curlErrorMessage = 'curlErrorMessage';
        $curlErrorCode = 28;
        $curlHttpStatusCode = 503;
        $this->imagePurgeResult->markImagePurgeAsFailed($curlErrorMessage, $curlErrorCode, $curlHttpStatusCode);
        $this->assertTrue($this->imagePurgeResult->hasCurlErrorMessage());
    }

    /**
     * @test
     */
    public function shouldCheckThatResultHasNoCurlErrorCode()
    {
        $this->assertFalse($this->imagePurgeResult->hasCurlErrorCode());
    }

    /**
     * @test
     */
    public function shouldCheckThatResultHasCurlErrorCode()
    {
        $curlErrorMessage = 'curlErrorMessage';
        $curlErrorCode = 28;
        $curlHttpStatusCode = 503;
        $this->imagePurgeResult->markImagePurgeAsFailed($curlErrorMessage, $curlErrorCode, $curlHttpStatusCode);
        $this->assertTrue($this->imagePurgeResult->hasCurlErrorCode());
    }

    /**
     * @test
     */
    public function shouldMarkImagePurgeAsFailed()
    {
        $curlErrorMessage = 'curlErrorMessage';
        $curlErrorCode = 28;
        $curlHttpStatusCode = 503;
        $this->imagePurgeResult->markImagePurgeAsFailed($curlErrorMessage, $curlErrorCode, $curlHttpStatusCode);

        $this->assertFalse($this->imagePurgeResult->isSuccessful());
        $this->assertSame($curlErrorMessage, $this->imagePurgeResult->getCurlErrorMessage());
        $this->assertSame($curlErrorCode, $this->imagePurgeResult->getCurlErrorCode());
        $this->assertSame($curlHttpStatusCode, $this->imagePurgeResult->getCurlHttpStatusCode());
    }

    /**
     * @test
     */
    public function shouldMarkImagePurgeAsSuccessful()
    {
        $this->imagePurgeResult->markImagePurgeAsSuccessful();
        $this->assertTrue($this->imagePurgeResult->isSuccessful());
    }
}
