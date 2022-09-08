<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

// Register static TypoScript templates
ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/',
    'imgix: Common Constants'
);

ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/Angular/Plugin/',
    'imgix: Load Angular Extbase-Plugin for further usage'
);

ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/Angular/Include/',
    'imgix: Include Angular-Module files into page'
);

ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/JQuery/Plugin/',
    'imgix: Load Jquery Extbase-Plugin for further usage'
);

ExtensionManagementUtility::addStaticFile(
    'imgix',
    'Configuration/Static/JQuery/Include/',
    'imgix: Include Jquery-Plugin files into page'
);
