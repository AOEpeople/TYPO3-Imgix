<?php
namespace Aoe\Imgix\Controller;

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

    public function initialAction()
    {
        $this->view->assign('host', $this->configuration->getHost());
        $this->view->assign('enabled', $this->configuration->isEnabled());
        $this->view->assign('enableFluid', $this->configuration->isFluidEnabled());
        $this->view->assign('enableObservation', $this->configuration->isObservationEnabled());
        $this->view->assign(
            'imgix-fluid-options',
            json_encode($this->configuration->getImgixFluidOptions(), JSON_FORCE_OBJECT)
        );
    }
}
