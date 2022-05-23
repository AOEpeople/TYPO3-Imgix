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

use Aoe\Imgix\Controller\LoadController;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;

/**
 * @covers \Aoe\Imgix\Controller\LoadController
 */
class LoadControllerTest extends FunctionalTestCase
{
    /**
     * @var array
     * Load all TYPO3-extensions, which we use in our depencency/constructor-injection
     */
    protected $testExtensionsToLoad = ['typo3conf/ext/imgix'];

    /**
     * @var LoadController
     */
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();

        $this->controller = GeneralUtility::makeInstance(LoadController::class);
    }

    /**
     * @test
     */
    public function shouldRenderAngularAction()
    {
        /** @var Request $request */
        $request = new Request();
        $request->setControllerActionName('angular');
        $request->setControllerName('Load');
        $request->setControllerExtensionName('Imgix');
        $request = $request
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withPluginName('LoadPluginName');
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $response = $this->controller->processRequest($request);
        $this->assertStringContainsString(
            'var aoe = aoe || {};',
            $response->getBody()
        );
        $this->assertStringContainsString(
            'settings: JSON.parse(\'{"host":"meinesubdomain.imgix.net","enableFluid":true,"enableObservation":true,"imgix":{"fluidClass":"imgix-fluid"},"imgixUrlParams":{}}\')',
            $response->getBody()
        );
    }

    /**
     * @test
     */
    public function shouldRenderJqueryAction()
    {
        /** @var Request $request */
        $request = new Request();
        $request->setControllerActionName('jquery');
        $request->setControllerName('Load');
        $request->setControllerExtensionName('Imgix');
        $request = $request
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
            ->withPluginName('LoadPluginName');
        $GLOBALS['TYPO3_REQUEST'] = $request;

        $response = $this->controller->processRequest($request);
        $this->assertStringContainsString(
            '{"host":"meinesubdomain.imgix.net","enableFluid":true,"enableObservation":true,"imgix":{"fluidClass":"imgix-fluid"},"imgixUrlParams":{}}',
            $response->getBody()
        );
    }
}
