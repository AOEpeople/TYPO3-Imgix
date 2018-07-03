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

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_extfilefunc.php']['processData'][] =
    \Aoe\Imgix\TYPO3\FileUtilityProcessDataHook::class;
