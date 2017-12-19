<?php
namespace Aoe\Imgix\Controller;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 AOE GmbH <dev@aoe.com>
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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class LoadController extends ActionController
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
        parent::__construct();
    }

    public function jqueryAction()
    {
        $this->view->assign('enabled', $this->configuration->isEnabled());
        $this->view->assign('options', $this->getOptionsAsJson());
    }

    public function angularAction()
    {
        $this->view->assign('enabled', $this->configuration->isEnabled());
        $this->view->assign('options', $this->getOptionsAsJson());
    }

    /**
     * @return string
     */
    private function getOptionsAsJson()
    {
        $options = [
            'host' => $this->configuration->getHost(),
            'enableFluid' => $this->configuration->isFluidEnabled(),
            'enableObservation' => $this->configuration->isObservationEnabled(),
            'imgix' => $this->configuration->getImgixFluidOptions(),
            'imgixUrlParams' => $this->configuration->getImgixDefaultUrlParameters()
        ];
        return json_encode($options, JSON_FORCE_OBJECT);
    }
}
