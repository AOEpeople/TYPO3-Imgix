<?php
use Aoe\Imgix\Controller\PurgeImgixCacheController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') or die();

ExtensionUtility::registerModule(
    'imgix',
    'system',
    'PurgeImgixCache',
    'bottom',
    [
        PurgeImgixCacheController::class => 'index,purgeImgixCache'
    ],
    [
        'access' => 'user,group',
        'icon' => 'EXT:imgix/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:imgix/Resources/Private/Language/locallang_mod.xlf'
    ]
);
