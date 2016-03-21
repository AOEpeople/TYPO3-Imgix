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
        $options = [
            'host' => $this->configuration->getHost(),
            'enableFluid' => $this->configuration->isFluidEnabled(),
            'enableObservation' => $this->configuration->isObservationEnabled(),
            'imgix' => $this->configuration->getImgixFluidOptions(),
        ];
        $this->view->assign('enabled', $this->configuration->isEnabled());
        $this->view->assign('options', json_encode($options, JSON_FORCE_OBJECT));
    }
}
