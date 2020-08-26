<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (TYPO3_MODE === 'BE') {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Aoe.imgix',
        'system',
        'imgix',
        'bottom',
        [
            'PurgeImgixCache' => 'index,purgeImgixCache'
        ],
        [
            'access' => 'user,group',
            'icon' => 'EXT:imgix/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:imgix/Resources/Private/Language/locallang_mod.xlf'
        ]
    );
}
