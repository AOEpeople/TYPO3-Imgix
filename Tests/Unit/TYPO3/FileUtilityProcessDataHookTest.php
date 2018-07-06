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

use Aoe\Imgix\Domain\Service\ImagePurgeService;
use Aoe\Imgix\TYPO3\Configuration;
use Aoe\Imgix\TYPO3\FileUtilityProcessDataHook;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use PHPUnit_Framework_MockObject_MockObject;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtility;

class FileUtilityProcessDataHookTest extends UnitTestCase
{
    /**
     * @var Configuration|PHPUnit_Framework_MockObject_MockObject
     */
    private $configuration;

    /**
     * @var FileUtilityProcessDataHook
     */
    private $fileHook;

    /**
     * @var ImagePurgeService|PHPUnit_Framework_MockObject_MockObject
     */
    private $imagePurgeService;

    /**
     * set up the test
     */
    public function setUp()
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $this->imagePurgeService = $this->getMockBuilder(ImagePurgeService::class)->disableOriginalConstructor()->getMock();
        $this->fileHook = new FileUtilityProcessDataHook($this->configuration, $this->imagePurgeService);
    }

    /**
     * @test
     */
    public function shouldPurgeImgixCache()
    {
        $this->configuration->expects(self::once())->method('getHost')->willReturn('www.congstar.imgix.de');
        $this->imagePurgeService
            ->expects(self::once())
            ->method('purgeImgixCache')
            ->with('http://www.congstar.imgix.de/directory/image.png');

        $action = 'upload';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getPublicUrl')->willReturn('directory/image.png');
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);
        $file->expects(self::once())->method('getUpdatedProperties')->willReturn(['property1']);
        $result = [];
        $result[0] = [];
        $result[0][0] = $file;

        $parentObject = $this->getMockBuilder(ExtendedFileUtility::class)->disableOriginalConstructor()->getMock();

        $this->fileHook->processData_postProcessAction($action, [], $result, $parentObject);
    }

    /**
     * @test
     */
    public function shouldNotPurgeImgixCacheWhenItIsNotRequired()
    {
        $action = 'upload';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $result = [];
        $result[0] = [];
        $result[0][0] = $file;
        $parentObject = $this->getMockBuilder(ExtendedFileUtility::class)->disableOriginalConstructor()->getMock();

        $this->imagePurgeService->expects(self::never())->method('purgeImgixCache');

        $this->fileHook = $this
            ->getMockBuilder(FileUtilityProcessDataHook::class)
            ->setConstructorArgs([$this->configuration, $this->imagePurgeService])
            ->setMethods(['buildImgUrl', 'getFile', 'isPurgingOfImgixCacheRequired'])
            ->getMock();
        $this->fileHook->expects(self::never())->method('buildImgUrl');
        $this->fileHook->expects(self::once())->method('getFile')->with($result)->willReturn($file);
        $this->fileHook->expects(self::once())->method('isPurgingOfImgixCacheRequired')->with($action, $file)->willReturn(false);
        $this->fileHook->processData_postProcessAction($action, [], $result, $parentObject);
    }

    /**
     * @test
     */
    public function shouldBuildImgUrl()
    {
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getPublicUrl')->willReturn('directory/image.png');
        $this->configuration->expects(self::once())->method('getHost')->willReturn('www.congstar.imgix.de');
        $imgUrl = $this->callInaccessibleMethod($this->fileHook, 'buildImgUrl', $file);
        $this->assertEquals('http://www.congstar.imgix.de/directory/image.png', $imgUrl);
    }

    /**
     * @test
     */
    public function shouldNotGetFile()
    {
        $result = [];
        $this->assertSame(null, $this->callInaccessibleMethod($this->fileHook, 'getFile', $result));
    }

    /**
     * @test
     */
    public function shouldGetFile()
    {
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $result = [];
        $result[0] = [];
        $result[0][0] = $file;
        $this->assertSame($file, $this->callInaccessibleMethod($this->fileHook, 'getFile', $result));
    }

    /**
     * @test
     */
    public function shouldCheckThatPurgingOfImgixCacheIsNotRequiredWhenFileIsNoImage()
    {
        $action = 'replace';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_APPLICATION);

        $this->assertFalse($this->callInaccessibleMethod($this->fileHook, 'isPurgingOfImgixCacheRequired', $action, $file));
    }

    /**
     * @test
     */
    public function shouldCheckThatPurgingOfImgixCacheIsNotRequiredWhenImageFileIsNewCreated()
    {
        $action = 'upload';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);
        $file->expects(self::once())->method('getUpdatedProperties')->willReturn([]);

        $this->assertFalse($this->callInaccessibleMethod($this->fileHook, 'isPurgingOfImgixCacheRequired', $action, $file));
    }

    /**
     * @test
     */
    public function shouldCheckThatPurgingOfImgixCacheIsRequiredWhenImageFileIsReplaced()
    {
        $action = 'replace';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);

        $this->assertTrue($this->callInaccessibleMethod($this->fileHook, 'isPurgingOfImgixCacheRequired', $action, $file));
    }

    /**
     * @test
     */
    public function shouldCheckThatPurgingOfImgixCacheIsRequiredWhenImageFileIsUpdated()
    {
        $action = 'upload';
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);
        $file->expects(self::once())->method('getUpdatedProperties')->willReturn(['property1']);

        $this->assertTrue($this->callInaccessibleMethod($this->fileHook, 'isPurgingOfImgixCacheRequired', $action, $file));
    }
}
