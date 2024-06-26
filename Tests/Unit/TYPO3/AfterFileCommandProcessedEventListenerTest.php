<?php

declare(strict_types=1);

namespace Aoe\Imgix\Tests\TYPO3;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2022 AOE GmbH <dev@aoe.com>
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
use Aoe\Imgix\TYPO3\AfterFileCommandProcessedEventListener;
use Aoe\Imgix\TYPO3\Configuration;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\Event\AfterFileCommandProcessedEvent;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class AfterFileCommandProcessedEventListenerTest extends UnitTestCase
{
    private Configuration $configuration;

    private AfterFileCommandProcessedEventListener $eventListener;

    private ImagePurgeService $imagePurgeService;

    protected function setUp(): void
    {
        $this->configuration = $this->getMockBuilder(Configuration::class)->disableOriginalConstructor()->getMock();
        $this->imagePurgeService = $this->getMockBuilder(ImagePurgeService::class)->disableOriginalConstructor()->getMock();

        $this->eventListener = $this->getAccessibleMock(
            AfterFileCommandProcessedEventListener::class,
            null,
            [$this->configuration, $this->imagePurgeService]
        );
    }

    public function testShouldPurgeImgixCacheWhenExistingImageFileIsUpdated(): void
    {
        $this->configuration->expects(self::once())->method('getHost')->willReturn('www.congstar.imgix.de');
        $this->imagePurgeService
            ->expects(self::once())
            ->method('purgeImgixCache')
            ->with('http://www.congstar.imgix.de/directory/image.png');

        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getPublicUrl')->willReturn('/directory/image.png');
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);

        $command = [
            'upload' => [
                'mockedKey' => ['mockedData'],
            ],
        ];
        $result = [$file];
        $event = new AfterFileCommandProcessedEvent(
            $command,
            $result,
            DuplicationBehavior::REPLACE
        );

        $this->eventListener->__invoke($event);
    }

    public function testShouldPurgeImgixCacheWhenExistingImageFileIsReplaced(): void
    {
        $this->configuration->expects(self::once())->method('getHost')->willReturn('www.congstar.imgix.de');
        $this->imagePurgeService
            ->expects(self::once())
            ->method('purgeImgixCache')
            ->with('http://www.congstar.imgix.de/directory/image.png');

        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getPublicUrl')->willReturn('/directory/image.png');
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);

        $command = ['replace' => ['mockedKey' => ['mockedData'], 'keepFilename' => '1']];
        $result = [$file];
        $event = new AfterFileCommandProcessedEvent(
            $command,
            $result,
            DuplicationBehavior::REPLACE
        );

        $this->eventListener->__invoke($event);
    }

    public function testShouldNotPurgeImgixCacheWhenItIsNotRequired(): void
    {
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();

        $command = ['upload' => ['mockedKey' => ['mockedData']]];
        $result = [$file];
        $event = new AfterFileCommandProcessedEvent(
            $command,
            $result,
            DuplicationBehavior::CANCEL
        );

        $this->imagePurgeService->expects(self::never())->method('purgeImgixCache');

        $this->eventListener = $this
            ->getMockBuilder(AfterFileCommandProcessedEventListener::class)
            ->setConstructorArgs([$this->configuration, $this->imagePurgeService])
            ->onlyMethods(['buildImgUrl', 'getFile', 'isPurgingOfImgixCacheRequired'])
            ->getMock();
        $this->eventListener->expects(self::never())->method('buildImgUrl');
        $this->eventListener->expects(self::once())->method('getFile')->with($result)->willReturn($file);
        $this->eventListener
            ->expects(self::once())
            ->method('isPurgingOfImgixCacheRequired')
            ->with($command, DuplicationBehavior::CANCEL, $file)
            ->willReturn(false);
        $this->eventListener->__invoke($event);
    }

    /**
     * @dataProvider shouldBuildImgUrlDataProvider
     */
    public function testShouldBuildImgUrl(string $publicUrl, string $host, string $expectedImgUrl): void
    {
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getPublicUrl')->willReturn($publicUrl);
        $this->configuration->expects(self::once())->method('getHost')->willReturn($host);

        $imgUrl = $this->eventListener->_call('buildImgUrl', $file);
        $this->assertSame($expectedImgUrl, $imgUrl);
    }

    public static function shouldBuildImgUrlDataProvider(): array
    {
        return [
            [
                'publicUrl' => '/directory/image.png',
                'host' => 'www.congstar.imgix.de',
                'expectedImgUrl' => 'http://www.congstar.imgix.de/directory/image.png',
            ],
            [
                'publicUrl' => 'http://www.congstar.de/directory/image.png',
                'host' => 'www.congstar.imgix.de',
                'expectedImgUrl' => 'http://www.congstar.imgix.de/directory/image.png',
            ],
            [
                'publicUrl' => 'https://www.congstar.de/directory/image.png',
                'host' => 'www.congstar.imgix.de',
                'expectedImgUrl' => 'http://www.congstar.imgix.de/directory/image.png',
            ],
        ];
    }

    public function testShouldNotGetFile(): void
    {
        $result = [];
        $this->assertNull($this->eventListener->_call('getFile', $result));
    }

    public function testShouldGetFile(): void
    {
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $result = [$file];
        $this->assertSame($file, $this->eventListener->_call('getFile', $result));
    }

    public function testShouldCheckThatPurgingOfImgixCacheIsNotRequiredWhenFileIsNoImage(): void
    {
        $command = ['upload' => ['mockedKey' => ['mockedData']]];
        $conflictMode = DuplicationBehavior::REPLACE;
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_APPLICATION);

        $result = $this->eventListener->_call('isPurgingOfImgixCacheRequired', $command, $conflictMode, $file);
        $this->assertFalse($result);
    }

    public function testShouldCheckThatPurgingOfImgixCacheIsNotRequiredWhenImageFileIsNewCreated(): void
    {
        $command = ['upload' => ['mockedKey' => ['mockedData']]];
        $conflictMode = DuplicationBehavior::CANCEL;
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);

        $result = $this->eventListener->_call('isPurgingOfImgixCacheRequired', $command, $conflictMode, $file);
        $this->assertFalse($result);
    }

    public function testShouldCheckThatPurgingOfImgixCacheIsRequiredWhenImageFileIsReplaced(): void
    {
        $command = ['replace' => ['mockedKey' => ['mockedData'], 'keepFilename' => '1']];
        $conflictMode = DuplicationBehavior::REPLACE;
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);

        $result = $this->eventListener->_call('isPurgingOfImgixCacheRequired', $command, $conflictMode, $file);
        $this->assertTrue($result);
    }

    public function testShouldCheckThatPurgingOfImgixCacheIsRequiredWhenImageFileIsUpdated(): void
    {
        $command = ['upload' => ['mockedKey' => ['mockedData']]];
        $conflictMode = DuplicationBehavior::REPLACE;
        $file = $this->getMockBuilder(File::class)->disableOriginalConstructor()->getMock();
        $file->expects(self::once())->method('getType')->willReturn(File::FILETYPE_IMAGE);

        $result = $this->eventListener->_call('isPurgingOfImgixCacheRequired', $command, $conflictMode, $file);
        $this->assertTrue($result);
    }
}
