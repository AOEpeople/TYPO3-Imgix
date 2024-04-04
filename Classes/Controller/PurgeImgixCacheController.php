<?php

declare(strict_types=1);

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

use Aoe\Imgix\Domain\Service\ImagePurgeService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Extbase\Http\ForwardResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PurgeImgixCacheController extends ActionController
{
    public function __construct(
        private readonly ImagePurgeService $imagePurgeService,
        private readonly ModuleTemplateFactory $moduleTemplateFactory
    ) {
    }

    public function indexAction(): ResponseInterface
    {
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        $moduleTemplate->setContent($this->view->render());
        return $this->htmlResponse($moduleTemplate->renderContent());
    }

    public function purgeImgixCacheAction(string $imageUrl): ForwardResponse
    {
        // Override flashMessageQueue in errorHandler:
        // When image-purge fails, then the errorHandler will automatically send a flashMessage with details about the failure
        $this->imagePurgeService->getErrorHandler()
            ->overrideFlashMessageQueue($this->getFlashMessageQueue());

        if ($this->imagePurgeService->purgeImgixCache($imageUrl)->isSuccessful()) {
            $messageKey = 'PurgeImgixCacheController.purgeImgixCacheWasSuccessful';
            $message = $this->getLanguageService()
                ->sL('LLL:EXT:imgix/Resources/Private/Language/locallang.xlf:' . $messageKey);
            $message = str_replace('###IMAGE_URL###', $imageUrl, $message);
            $this->addFlashMessage($message);
        }

        return new ForwardResponse('index');
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
