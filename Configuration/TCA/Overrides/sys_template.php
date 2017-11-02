<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

// Register static TypoScript templates
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/',
    'imgix: Common Constants'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/Angular/Plugin/',
    'imgix: Load Angular Extbase-Plugin for further usage'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/Angular/Include/',
    'imgix: Include Angular-Module files into page'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/JQuery/Plugin/',
    'imgix: Load Jquery Extbase-Plugin for further usage'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/JQuery/Include/',
    'imgix: Include Jquery-Plugin files into page'
);
