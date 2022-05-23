<?php
use Aoe\Imgix\Controller\LoadController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::configurePlugin(
    'imgix',
    'AngularLoader',
    [LoadController::class => 'angular']
);

ExtensionUtility::configurePlugin(
    'imgix',
    'JQueryLoader',
    [LoadController::class => 'angular']
);
