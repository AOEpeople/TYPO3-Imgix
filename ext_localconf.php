<?php

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Aoe.' . $_EXTKEY,
    'AngularLoader',
    array(
        'Load' => 'angular',
    ),
    array()
);


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Aoe.' . $_EXTKEY,
    'JQueryLoader',
    array(
        'Load' => 'jquery',
    ),
    array()
);
