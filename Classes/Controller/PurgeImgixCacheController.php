<?php
namespace Aoe\Imgix\Controller;

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

use Aoe\Imgix\Rest\RestClient;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PurgeImgixCacheController extends ActionController
{
    /**
     * @var RestClient
     */
    private $restClient;

    /**
     * @param RestClient $restClient
     */
    public function __construct(RestClient $restClient)
    {
        $this->restClient = $restClient;
        parent::__construct();
    }

    /**
     * render form
     */
    public function indexAction()
    {
    }

    /**
     * @param string $imageUrl
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function purgeImgixCacheAction($imageUrl)
    {
        // Override flashMessageQueue in errorHandler:
        // When REST-call fails, than the errorHandler will automatically send a flashMessage with details about the failure
        $this->restClient->getErrorHandler()->overrideFlashMessageQueue($this->getControllerContext()->getFlashMessageQueue());

        if (true === $this->restClient->purgeImgixCache($imageUrl)) {
            $this->addFlashMessage('Purge-Request for "'.$imageUrl.'" was successfully send!');
        }

        $this->redirect('index');
    }
}
