<?php
namespace Aoe\Imgix\Tests\Functional\Controller;

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

use Aoe\Imgix\Controller\PurgeImgixCacheController;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * @covers \Aoe\Imgix\Controller\PurgeImgixCacheController
 */
class PurgeImgixCacheControllerTest extends FunctionalTestCase
{
    /**
     * @var array
     * Load all TYPO3-extensions, which we use in our depencency/constructor-injection
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/imgix'];

    /**
     * @var PurgeImgixCacheController
     */
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpBackendUserFromFixture(1);

        $this->controller = GeneralUtility::makeInstance(PurgeImgixCacheController::class);
    }

    /**
     * @test
     */
    public function shouldRenderIndexAction()
    {
        /** @var Request $request */
        $request = new Request();
        $request->setControllerActionName('index');
        $request->setControllerName('PurgeImgixCache');
        $request->setControllerExtensionName('Imgix');
        $request = $request
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withPluginName('PurgeImgixCachePluginName');
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $response = $this->controller->processRequest($request);
        $this->assertStringContainsString(
            '<h2>Purge imgix-cache for an image</h2>',
            $response->getBody()
        );
        $this->assertStringContainsString(
            '<label for="imageUrl">Image-URL (e.g. &quot;http://mydomain.imgix.net/fileadmin/myimage.png&quot;):</label>',
            $response->getBody()
        );
    }
}